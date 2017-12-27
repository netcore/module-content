<?php

namespace Modules\Content\PassThroughs\Entry;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Content\Models\ContentBlock;
use Modules\Content\Models\Entry;
use Modules\Content\Models\HtmlBlock;
use Modules\Content\Models\WidgetBlock;
use Modules\Content\PassThroughs\PassThrough;
use Modules\Content\Translations\EntryTranslation;
use Netcore\Translator\Helpers\TransHelper;

class Revision extends PassThrough
{
    /**
     * @var Entry
     */
    private $entry;

    /**
     * @var Collection
     */
    private $languages;

    /**
     * Storage constructor.
     *
     * @param Entry $entry
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
        $this->languages = TransHelper::getAllLanguages();
    }

    /**
     * @return Entry
     */
    public function make(): Entry
    {
        $originalEntry = $this->entry;

        // 1. Basic data (entries)
        // 2. Translations (entry_translations_
        // 3. widget_blocks
        // 4. widget_block_fields
        // 5. widget_block_items
        // 6. widget_block_item_fields
        // 7. content_blocks

        // Basic Entry model. We can copy all the fields except created_at, updated_at, parent_id.
        $replicatedEntry = $originalEntry->replicate();
        $replicatedEntry->parent_id = $originalEntry->id;
        $replicatedEntry->type = 'revision';
        $replicatedEntry->save();

        // Translations
        foreach ($originalEntry->translations as $originalEntryTranslation) {

            // Basic translation data
            $replicatedTranslation = $originalEntryTranslation->replicate();
            $replicatedTranslation->entry_id = $replicatedEntry->id;

            // Stapler attachment
            $replicatedTranslation->attachment = STAPLER_NULL; // TODO
            $replicatedTranslation->save();

            // Content blocks
            foreach ($originalEntryTranslation->contentBlocks as $originalContentBlock) {
                $this->replicateContentBlock($originalContentBlock, $replicatedTranslation);
            }
        }

        return $replicatedEntry;
    }

    /**
     * @param ContentBlock $originalContentBlock
     * @param EntryTranslation $replicatedTranslation
     * @return mixed
     */
    public function replicateContentBlock(ContentBlock $originalContentBlock, EntryTranslation $replicatedTranslation)
    {
        // Widget block
        $originalContentBlockData = $originalContentBlock->data;
        $originalWidgetBlockId = array_get($originalContentBlockData, 'widget_block_id');
        $originalWidgetBlock = WidgetBlock::find($originalWidgetBlockId);

        if (!$originalWidgetBlock) {
            return false;
        }

        $replicatedWidgetBlock = $originalWidgetBlock->replicate();
        $replicatedWidgetBlock->save();

        // Widget block items
        foreach ($originalWidgetBlock->items as $originalWidgetBlockItem) {
            $replicatedWidgetBlockItem = $originalWidgetBlockItem->replicate();
            $replicatedWidgetBlockItem->widget_block_id = $replicatedWidgetBlock->id;
            $replicatedWidgetBlockItem->save();

            // Widget block item fields
            foreach ($originalWidgetBlockItem->fields as $originalWidgetBlockItemField) {
                $replicatedWidgetBlockItemField = $originalWidgetBlockItemField->replicate();
                $replicatedWidgetBlockItemField->widget_block_item_id = $replicatedWidgetBlockItem->id;
                $replicatedWidgetBlockItemField->image = STAPLER_NULL; // TODO
                $replicatedWidgetBlockItemField->save();
            }
        }

        // Content block
        $replicatedContentBlock = $originalContentBlock->replicate();
        $replicatedContentBlock->contentable_id = $replicatedTranslation->id;
        $replicatedContentBlock->data = [
            'widget_block_id' => $replicatedWidgetBlock->id
        ];
        $replicatedContentBlock->save();

        return $replicatedContentBlock;
    }

}
