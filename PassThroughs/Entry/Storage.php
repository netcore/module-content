<?php

namespace Modules\Content\PassThroughs\Entry;

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

        // Regular data
        $entry->update([
            'layout'    => array_get($requestData, 'layout'),

            // Checkboxes user array_has
            'is_active' => array_has($requestData, 'is_active')
        ]);


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
            $config = collect(config('module_content.widgets'))->where('key', $key)->first();
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
        $config = collect(config('module_content.widgets'))->where('key', $key)->first();
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

            return $translations;
        })->toArray();

        $entry->updateTranslations($entryTranslations);

        $contentBlocks = $entry
            ->contentBlocks()
            ->whereWidget('simple_text')
            ->get();

        $entryTranslations = [];

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
