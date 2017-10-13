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
        $this->deleteOldContentBlocks($entry);

        // Store new widgets
        $this->storeNewContentBlocks($entry, $contentBlocks);

        // Store translations
        $entryTranslations = (array)array_get($requestData, 'translations', []);
        $this->storeEntryTranslation($entry, $entryTranslations);

        return $entry;
    }

    /**
     * @param Entry $entry
     * @param array $contentBlocks
     */
    private function storeNewContentBlocks(Entry $entry, Array $contentBlocks)
    {
        // Save widgets and their data
        // 1. Put data in $entry->content_blocks table
        // 1.1 Put data in additional tables, according to each specific widget

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
     * @param Entry $entry
     */
    private function deleteOldContentBlocks(Entry $entry)
    {
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
     * @param Entry $entry
     * @param array $entryTranslations
     */
    private function storeEntryTranslation(Entry $entry, Array $entryTranslations)
    {
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
}
