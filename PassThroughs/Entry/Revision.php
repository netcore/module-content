<?php

namespace Modules\Content\PassThroughs\Entry;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Content\Models\ContentBlock;
use Modules\Content\Models\Entry;
use Modules\Content\Models\HtmlBlock;
use Modules\Content\Models\MetaTag;
use Modules\Content\Models\WidgetBlock;
use Modules\Content\Models\WidgetBlockItemField;
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
     * @param string $type
     * @return Entry
     */
    public function make($type = 'revision'): Entry
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
        $replicatedEntry->type = $type;
        $replicatedEntry->save();

        // Translations
        foreach ($originalEntry->translations as $originalEntryTranslation) {

            // Basic translation data
            $replicatedTranslation = $originalEntryTranslation->replicate();
            $replicatedTranslation->entry_id = $replicatedEntry->id;

            // Stapler attachment
//            $replicatedTranslation->attachment = $this->replicateEntryTranslationAttachment($originalEntryTranslation);
//            $replicatedTranslation->save();

            // Content blocks
            foreach ($originalEntryTranslation->contentBlocks as $originalContentBlock) {
                $this->replicateContentBlock($originalContentBlock, $replicatedTranslation);
            }

            // Meta tags
            $this->replicateMetaTags($originalEntryTranslation, $replicatedTranslation);
        }

        return $replicatedEntry;
    }

    /**
     * @param EntryTranslation $originalEntryTranslation
     * @param EntryTranslation $replicatedTranslation
     */
    private function replicateMetaTags(
        EntryTranslation $originalEntryTranslation,
        EntryTranslation $replicatedTranslation
    ) {
        $newMetaTags = $originalEntryTranslation->metaTags->map(function ($originalMetaTag) use ($replicatedTranslation
        ) {
            $replicatedMetaTag = array_only($originalMetaTag->toArray(), [
                'name',
                'property',
                'value'
            ]);
            $replicatedMetaTag['entry_translation_id'] = $replicatedTranslation->id;

            return $replicatedMetaTag;
        })->toArray();

        $table = app()->make(MetaTag::class)->getTable();
        DB::table($table)->insert($newMetaTags);
    }

    /**
     * @param ContentBlock $originalContentBlock
     * @param EntryTranslation $replicatedTranslation
     * @return mixed
     */
    private function replicateContentBlock(ContentBlock $originalContentBlock, EntryTranslation $replicatedTranslation)
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

                $replicatedWidgetBlockItemField->image = $this->replicateFieldAttachment($originalWidgetBlockItemField);
                $replicatedWidgetBlockItemField->save();
            }
        }

        // Content block
        $replicatedContentBlock = $originalContentBlock->replicate();
        $replicatedTranslation = $replicatedTranslation->first();
        $replicatedContentBlock->contentable_id = $replicatedTranslation->id;
        $replicatedContentBlock->data = [
            'widget_block_id' => $replicatedWidgetBlock->id
        ];
        $replicatedContentBlock->save();

        return $replicatedContentBlock;
    }

    /**
     * @param EntryTranslation $originalEntryTranslation
     * @return string
     */
    private function replicateEntryTranslationAttachment(EntryTranslation $originalEntryTranslation)
    {
        $attachmentFileName = $originalEntryTranslation->attachment_file_name;

        if ($attachmentFileName) {

            $source = public_path($originalEntryTranslation->attachment->url());
            $source = urldecode($source);

            $dir = storage_path('module_content');
            if (!is_dir($dir)) {
                mkdir($dir);
            }

            $destination = $dir . '/' . $attachmentFileName;

            if (file_exists($source)) {
                copy($source, $destination);
            }

            return $destination;
        }

        return STAPLER_NULL;
    }

    /**
     * @param WidgetBlockItemField $originalWidgetBlockItemField
     * @return mixed
     */
    private function replicateFieldAttachment(WidgetBlockItemField $originalWidgetBlockItemField)
    {
        $imageFileName = $originalWidgetBlockItemField->image_file_name;

        if ($imageFileName) {

            $source = public_path($originalWidgetBlockItemField->image->url());
            $source = urldecode($source);

            $dir = storage_path('module_content');
            if (!is_dir($dir)) {
                mkdir($dir);
            }

            $destination = $dir . '/' . $imageFileName;

            if (file_exists($source)) {
                copy($source, $destination);
            }

            return $destination;
        }

        return STAPLER_NULL;
    }

    /**
     * @return Entry
     */
    public function restore(): Entry
    {
        $entry = $this->entry;

        $restored = DB::transaction(function () use ($entry) {

            $parent = $entry->parent;

            $revisionIds = $parent->children()->pluck('id')->toArray();

            $replicatedEntry = $entry->revision()->make();
            $replicatedEntry->type = 'current';
            $replicatedEntry->parent_id = null;
            $replicatedEntry->is_homepage = $parent->is_homepage;
            $replicatedEntry->is_active = $parent->is_active;
            $replicatedEntry->save();

            $parent->type = 'revision';
            $parent->is_homepage = 0;
            $parent->created_at = date('Y-m-d H:i:s');
            $parent->parent_id = $replicatedEntry->id;
            $parent->save();

            Entry::whereIn('id', $revisionIds)->update([
                'parent_id' => $replicatedEntry->id
            ]);

            return $replicatedEntry;
        });

        return $restored;
    }
}
