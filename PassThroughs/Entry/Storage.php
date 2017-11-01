<?php

namespace Modules\Content\PassThroughs\Entry;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Content\Models\ContentBlock;
use Modules\Content\Models\Entry;
use Modules\Content\Models\HtmlBlock;
use Modules\Content\PassThroughs\PassThrough;

class Storage extends PassThrough
{
    /**
     * @var Entry
     */
    private $entry;

    /**
     * Storage constructor.
     *
     * @param Entry $entry
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @param array $requestData
     * @return Entry
     */
    public function update(Array $requestData): Entry
    {
        $entry = DB::transaction(function () use ($requestData) {
            return $this->transaction($requestData);
        });

        return $entry;
    }

    /**
     * @param array $requestData
     * @return Entry
     */
    private function transaction(Array $requestData): Entry
    {
        $entry = $this->entry;

        // Cast widgets to array
        $contentBlocks = json_decode(array_get($requestData, 'widgets', null));
        $contentBlocks = (array)array_map(function ($contentBlock) {
            return (array)$contentBlock;
        }, $contentBlocks);


        $publishedAt = array_get($requestData, 'published_at');
        $publishedAtCarbon = Carbon::createFromFormat('d.m.Y', $publishedAt)->startOfDay();
        $publishedAtFormatted = $publishedAtCarbon ? $publishedAtCarbon->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');

        $isActive = array_has($requestData, 'is_active');

        // Regular data
        $isHomepage = array_has($requestData, 'is_homepage');
        $entry->update([
            'layout'       => array_get($requestData, 'layout'),
            'published_at' => $publishedAtFormatted,

            // Checkboxes user array_has
            'is_active'    => $isActive,
            'is_homepage'  => $isHomepage,

            'attachment' => request()->file('attachment')
        ]);

        // If this is homepage, then mark other pages as regular ones
        if ($isHomepage) {
            Entry::where('id', '!=', $entry->id)->update([
                'is_homepage' => 0
            ]);
        }

        $existingContentBlockIds = $entry->contentBlocks()->pluck('id')->toArray();
        $receivedContentBlockIds = [];

        foreach ($contentBlocks as $index => $contentBlock) {

            $contentBlockId = array_get($contentBlock, 'contentBlockId');
            $widget = array_get($contentBlock, 'widget');

            if (is_integer($contentBlockId)) {
                // $contentBlockId is real id in DB.

                $receivedContentBlockIds[] = $contentBlockId;

                $existingContentBlock = $entry->contentBlocks()->find($contentBlockId);
                if (!$existingContentBlock) {
                    continue;
                }

                $existingContentBlock->update([
                    'order' => ($index + 1)
                ]);

                $this->storeNewContentBlock($existingContentBlock, $contentBlock, $index);

            } else {
                // $contentBlockId is random string, something like "Mfjxi"

                $newContentBlock = $entry->contentBlocks()->create([
                    'widget' => $widget,
                    'order'  => ($index + 1),
                    'data'   => []
                ]);

                $this->storeNewContentBlock($newContentBlock, $contentBlock, $index);
            }
        }

        // Delete blocks that we didnt receive
        $deletableContentBlockIds = [];
        foreach ($existingContentBlockIds as $existingContentBlockId) {
            if (!in_array($existingContentBlockId, $receivedContentBlockIds)) {
                $deletableContentBlockIds[] = $existingContentBlockId;
            }
        }

        $deletableContentBlocks = $entry->contentBlocks()->whereIn('id', $deletableContentBlockIds)->get();
        foreach ($deletableContentBlocks as $deletableContentBlock) {
            $key = $deletableContentBlock->widget;
            $config = collect(config('netcore.module-content.widgets'))->where('key', $key)->first();
            $backendWorker = array_get($config, 'backend_worker');
            if ($backendWorker) {
                $backendWorker = new $backendWorker($config);
                $backendWorker->delete($deletableContentBlock); // Delete data in related tables
                $deletableContentBlock->delete();
            }
        }

        // Store translations
        $entryTranslations = (array)array_get($requestData, 'translations', []);
        $this->storeEntryTranslations($entryTranslations);

        // Hide/show menu items that link to this entry
        $menuItemClass = '\Modules\Admin\Models\MenuItem';
        if(class_exists($menuItemClass)) {
            $slug = '/' . trim($entry->slug, '/');
            app($menuItemClass)->whereValue($slug)->update([
                'is_active' => $isActive
            ]);
        }

        return $entry;
    }

    /**
     * @param ContentBlock $existingContentBlock
     * @param array $contentBlock
     * @param Int $index
     */
    private function storeNewContentBlock(ContentBlock $existingContentBlock, Array $contentBlock, Int $index)
    {
        // Save widgets and their data
        // 1. Put data in $entry->content_blocks table
        // 1.1 Put data in additional tables, according to each specific widget

        $entry = $this->entry;

        $key = array_get($contentBlock, 'widget');
        $config = collect(config('netcore.module-content.widgets'))->where('key', $key)->first();
        $backendWorker = array_get($config, 'backend_worker');

        if ($backendWorker) {
            $backendWorker = new $backendWorker($config);
            $action = $backendWorker->action;

            if ($action == 'update') {
                $frontendData = (array)array_get($contentBlock, 'data', []);
                $data = $backendWorker->update($frontendData, $entry);

                $existingContentBlock->update([
                    'order'  => ($index + 1),
                    'widget' => $key,
                    'data'   => $data
                ]);
            }

            if ($action == 'recreate') {

                // Delete data in related tables
                $backendWorker->delete($existingContentBlock);
                $existingContentBlock->delete();

                $frontendData = (array)array_get($contentBlock, 'data', []);
                $data = $backendWorker->store($frontendData);

                $contentBlockData = [
                    'order'  => ($index + 1),
                    'widget' => $key,
                    'data'   => $data
                ];

                $entry->contentBlocks()->create($contentBlockData);
            }
        }
    }

    /**
     *
     */
    public function deleteOldContentBlocks()
    {
        $entry = $this->entry;

        foreach($entry->contentBlocks as $contentBlock) {

            $key = $contentBlock->widget;
            $config = collect(config('netcore.module-content.widgets'))->where('key', $key)->first();
            $backendWorker = array_get($config, 'backend_worker');
            $backendWorker = new $backendWorker($config);

            // Delete data in related tables
            $backendWorker->delete($contentBlock);
            $contentBlock->delete();
        }
    }

    /**
     * @param array $entryTranslations
     */
    private function storeEntryTranslations(Array $entryTranslations)
    {
        $entry = $this->entry;

        $entryTranslations = collect($entryTranslations)->map(function ($translations, $locale) use ($entry) {

            if (strlen(array_get($translations, 'slug')) == 0) {
                $slug = str_slug(
                    array_get($translations, 'title')
                );
            } else {
                $slug = str_slug(
                    array_get($translations, 'slug')
                );
            }

            $channelId = $entry->channel_id;
            $translations['slug'] = $this->uniqueSlug($slug, $locale, $channelId);
            $translations['content'] = ''; // Default value

            return $translations;
        })->toArray();

        $contentBlocks = $entry
            ->contentBlocks()
            ->where('data', 'LIKE', '%html_block_id%')
            ->get();

        foreach ($contentBlocks as $contentBlock) {

            $htmlBlockId = array_get($contentBlock->data, 'html_block_id', null);
            if (!$htmlBlockId) {
                continue;
            }

            $htmlBlock = HtmlBlock::find($htmlBlockId);

            if (!$htmlBlock) {
                continue;
            }

            foreach ($htmlBlock->translations as $translation) {
                if (!isset($entryTranslations[$translation->locale]['content'])) {
                    $entryTranslations[$translation->locale]['content'] = '';
                }

                $entryTranslations[$translation->locale]['content'] .= $translation->content;
            }
        }

        // Save translations
        $entry->updateTranslations($entryTranslations);
    }

    /**
     * @param $originalSlug
     * @param $locale
     * @param $channelId
     * @return mixed
     */
    private function uniqueSlugCount($originalSlug, $locale, $channelId)
    {
        return Entry::join('netcore_content__entry_translations', 'netcore_content__entries.id', '=',
            'netcore_content__entry_translations.entry_id')
            ->where('netcore_content__entry_translations.slug', $originalSlug)
            ->where('netcore_content__entry_translations.locale', $locale)
            ->where('netcore_content__entries.channel_id', $channelId)
            ->where('netcore_content__entries.id', '!=', $this->entry->id)
            ->count();
    }

    /**
     * @param $originalSlug
     * @param $locale
     * @param $channelId
     * @return string
     */
    private function uniqueSlug($originalSlug, $locale, $channelId)
    {
        $slug = $originalSlug;

        $count = $this->uniqueSlugCount($originalSlug, $locale, $channelId);
        if ($count) {
            $count++; // This will generate test and test-2, not test and test-1
            $slug = $originalSlug . '-' . $count;
        }

        while ($this->uniqueSlugCount($slug, $locale, $channelId) > 0) {
            $count++;
            $slug = $originalSlug . '-' . $count;
        }

        return $slug;
    }

}
