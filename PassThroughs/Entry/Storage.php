<?php

namespace Modules\Content\PassThroughs\Entry;

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
        $entry = $this->entry;

        // Cast widgets to array
        $contentBlocks = json_decode(array_get($requestData, 'widgets', null));
        $contentBlocks = (array)array_map(function ($contentBlock) {
            return (array)$contentBlock;
        }, $contentBlocks);

        // Regular data
        $entry->update([
            'is_active' => array_has($requestData, 'is_active')
        ]);

        // Delete old widgets
        $this->deleteOldContentBlocks();

        // Store new widgets
        $this->storeNewContentBlocks($contentBlocks);

        // Store translations
        $entryTranslations = (array)array_get($requestData, 'translations', []);
        $this->storeEntryTranslation($entryTranslations);

        return $entry;
    }

    /**
     * @param array $contentBlocks
     */
    private function storeNewContentBlocks(Array $contentBlocks)
    {
        // Save widgets and their data
        // 1. Put data in $entry->content_blocks table
        // 1.1 Put data in additional tables, according to each specific widget

        $entry = $this->entry;

        foreach ($contentBlocks as $index => $contentBlock) {

            $key = array_get($contentBlock, 'widget');
            $config = collect(config('module_content.widgets'))->where('key', $key)->first();
            $backendWorker = array_get($config, 'backend_worker');

            $data = [];
            if ($backendWorker AND method_exists($backendWorker, 'store')) {
                $frontendData = (array)array_get($contentBlock, 'data', []);
                $data = app($backendWorker)->store($frontendData);
            }

            $contentBlockData = [
                'order'  => ($index + 1),
                'widget' => $key,
                'data'   => $data
            ];

            $entry->contentBlocks()->create($contentBlockData);
        }
    }

    /**
     *
     */
    public function deleteOldContentBlocks()
    {
        $entry = $this->entry;
        foreach ($entry->contentBlocks as $oldContentBlock) {

            $key = $oldContentBlock->widget;
            $config = collect(config('module_content.widgets'))->where('key', $key)->first();
            $backendWorker = array_get($config, 'backend_worker');

            // Delete data in related tables
            if ($backendWorker AND method_exists($backendWorker, 'delete')) {
                app($backendWorker)->delete($oldContentBlock);
            }

            $oldContentBlock->delete();
        }
    }

    /**
     * @param array $entryTranslations
     */
    private function storeEntryTranslation(Array $entryTranslations)
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
