<?php

namespace Modules\Content\Widgets\BackendWorkers;

use Illuminate\Support\Collection;
use Modules\Content\Models\ContentBlock;
use Netcore\Translator\Helpers\TransHelper;
use Netcore\Translator\Models\Language;

class ImageBlock implements BackendWorkerInterface
{
    /**
     * @var string "recreate" || "update"
     */
    public $action = 'update';

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var Collection
     */
    private $languages;

    /**
     * @var array
     */
    private static $cachedImageBlocks = [];

    /**
     * ImageBlock constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->languages = TransHelper::getAllLanguages();
    }

    /**
     *
     * getErrors() method accept widget's data as it comes from frontend
     * and then returns any validation errors
     *
     * @param array $widget
     * @return array
     */
    public function getErrors(Array $widget): Array
    {
        $errors = [];

        $contentBlockIndex = array_get($widget, 'order');
        $data = (array)array_get($widget, 'data');
        $blocks = (array)array_get($data, 'blocks');

        foreach ($blocks as $trIndex => $block) {
            $block = (array)$block;
            $attributes = (array)array_get($block, 'attributes', []);

            // Validate length of attributes that are not images
            $configuredFields = array_get($this->config, 'fields');
            foreach ($attributes as $field => $data) {

                if (!$data) {
                    continue;
                }

                $data = (array)$data;

                foreach ($data as $isoCode => $value) {

                    $fieldConfig = array_get($configuredFields, $field, []);
                    $fieldType = array_get($fieldConfig, 'type', 'text');

                    $maxlength = 191;
                    if ($fieldType == 'textarea') {
                        $maxlength = 8000000; // 8MB
                    }

                    if (mb_strlen($value) > $maxlength) {

                        $tdId = $trIndex . '-' . $field;
                        $key = 'tableCell.' . $contentBlockIndex . '.' . $tdId;

                        $errors[$key] = 'Data is too long for this field';
                    }
                }
            }

            $imageAttribute = (array)array_get($attributes, 'image', []);

            if ($imageAttribute) {
                $name = array_get($imageAttribute, 'file');
                if ($name) {
                    $uploadedFile = request()->file($name);
                    if ($uploadedFile) {

                        $serverMimeType = $uploadedFile->getMimeType();
                        $clientMimeType = $uploadedFile->getClientMimeType();

                        $isImage = false;
                        if (
                            substr($serverMimeType, 0, 5) == 'image'
                            OR
                            // Here we trust admins not to upload malicious .svg file. That's odd, but at the moment I dont's see a way to validate .svg images.
                            ($serverMimeType == 'text/html' AND $clientMimeType == 'image/svg+xml')
                        ) {
                            $isImage = true;
                        }

                        if (!$isImage) {

                            $tdId = $trIndex . '-image';
                            $key = 'tableCell.' . $contentBlockIndex . '.' . $tdId;

                            $errors[$key] = 'Must be an image';
                        }
                    }
                }
            }
        }

        return $errors;
    }

    /**
     *
     * store() method needs to return data that will be json encoded
     * and put into "data" column in "content_blocks" table
     *
     * Additionally, it should put data in any other related tables.
     * For example, if we have widget "gallery_slider", we might store
     * ["gallery_id" => 1] in "data" column and put any actual data
     * in "galleries" table
     *
     * @param $frontendData
     * @return Array
     */
    public function store(Array $frontendData): Array
    {
        // update() is used in this widget, not store()
    }

    /**
     *
     * update() method needs to return data that will be json encoded
     * and put into "data" column in "content_blocks" table
     *
     * Additionally, it should put data in any other related tables.
     * For example, if we have widget "gallery_slider", we might store
     * ["gallery_id" => 1] in "data" column and put any actual data
     * in "galleries" table
     *
     * @param $frontendData
     * @return Array
     */
    public function update(Array $frontendData): Array
    {
        $imageBlockId = array_get($frontendData, 'imageBlockId');

        if (is_numeric($imageBlockId)) {
            // Real id. Already exists in DB.
            $imageBlock = \Modules\Content\Models\ImageBlock::with('items')->find($imageBlockId);
        } else {
            // $imageBlockId is random string, something like "Mfjxi"
            $imageBlock = \Modules\Content\Models\ImageBlock::create([]);
            // todo - translatable title
        }

        $existingItemIds = $imageBlock->items()->pluck('id')->toArray();
        $receivedItemIds = [];

        $blocks = (array)array_get($frontendData, 'blocks', []);

        foreach ($blocks as $index => $block) {

            $block = (array)$block;
            $attributes = (array)array_get($block, 'attributes');

            $imageBlockItemId = array_get($block, 'imageBlockItemId');

            if (is_numeric($imageBlockItemId)) {

                // Real id. Already exists in DB.
                $receivedItemIds[] = $imageBlockItemId;

                $imageBlockItem = $imageBlock->items->where('id', $imageBlockItemId)->first();

                if (!$imageBlockItem) {
                    continue;
                }

                // Order
                $imageBlockItem->update([
                    'order' => ($index + 1)
                ]);

            } else {

                // Create new entry
                // $imageBlockItemId is random string, something like "Mfjxi"
                $imageBlockItem = $imageBlock->items()->create([
                    'order' => ($index + 1)
                ]);
            }

            // Format ImageBlockItem attributes
            $imageBlockItemAttributes = [];
            $fields = array_keys($attributes);
            foreach ($fields as $field) {

                if ($field == 'image') {
                    continue;
                }

                $fieldData = (array)array_get($attributes, $field, []);
                $fieldData = array_flatten($fieldData);
                $value = array_get($fieldData, 0, '');

                $nonJsonFields = ['title', 'subtitle', 'content', 'link'];
                $isNonJsonField = in_array($field, $nonJsonFields);
                if ($isNonJsonField) {
                    $imageBlockItemAttributes[$field] = $value;
                } else {
                    $imageBlockItemAttributes['json'][$field] = $value;
                }
            }

            $imageBlockItem->update($imageBlockItemAttributes);

            // Image
            $imageAttribute = (array)array_get($attributes, 'image', []);
            if ($imageAttribute) {
                $name = array_get($imageAttribute, 'file');
                $uploadedFile = request()->file($name);
                if ($name AND $uploadedFile) {
                    $imageBlockItem->image = $uploadedFile;
                    $imageBlockItem->save();
                }
            }
        }

        // Delete items that we didnt receive
        $deletableItemIds = [];
        foreach ($existingItemIds as $existingItemId) {
            if (!in_array($existingItemId, $receivedItemIds)) {
                $deletableItemIds[] = $existingItemId;
            }
        }

        $deletableItems = $imageBlock->items()->whereIn('id', $deletableItemIds)->get();
        foreach ($deletableItems as $deletableItem) {
            if ($deletableItem->image) {
                $deletableItem->image = STAPLER_NULL;
                $deletableItem->save();
            }
        }
        $imageBlock->items()->whereIn('id', $deletableItemIds)->delete();

        return [
            'image_block_id' => $imageBlock->id
        ];
    }

    /**
     *
     * delete() gets called right before we execute $contentBlock->delete()
     * This is a good place to remove data in other related tables.
     *
     * For example, if we have ["gallery_id" => 1] in $contentBlock->data,
     * then we should delete that gallery here
     *
     * @param ContentBlock $contentBlock
     */
    public function delete(ContentBlock $contentBlock)
    {
    }

    /**
     *
     * backendTemplateComposer() takes data from "data" column in content_blocks table
     * and transforms to structure that will be injected in widget's backend template
     *
     * For example, there might be ["gallery_id" => 1] in "data" column (content_blocks table)
     * This function would do something like Gallery::find(array_get($data, 'gallery_id'))
     * And then return it.
     *
     * @param $data
     * @param Language $language
     * @return mixed
     */
    public function backendTemplateComposer(Array $data, Language $language): Array
    {
        $translations = [];
        $imageBlock = null;

        $imageBlockId = array_get($data, 'image_block_id', null);

        $cached = isset(self::$cachedImageBlocks[$imageBlockId]);
        if($imageBlockId AND !$cached) {
            self::$cachedImageBlocks[$imageBlockId] = \Modules\Content\Models\ImageBlock::with([
                'items'
            ])->find($imageBlockId);
        }

        if($imageBlockId) {
            $imageBlock = array_get(self::$cachedImageBlocks, $imageBlockId);
        }

        if ($imageBlock) {
            foreach ($imageBlock->translations as $translation) {
                $translations[$translation->locale] = [
                    'title'    => $translation->title,
                    'subtitle' => $translation->subtitle,
                    'content'  => $translation->content,
                    'json'     => $translation->json
                ];
            }
        }

        $configuredFields = array_get($this->config, 'fields');
        $fields = [];
        foreach ($configuredFields as $fieldName => $fieldData) {

            $fieldType = array_get($fieldData, 'type');
            $fieldLabel = array_get($fieldData, 'label');
            $styles = array_get($fieldData, 'styles');
            $options = (array)array_get($fieldData, 'options', []);

            $value = $imageBlock ? object_get($imageBlock, $fieldName) : '';
            $fields[] = [
                'name'    => $fieldName,
                'type'    => $fieldType,
                'label'   => $fieldLabel,
                'styles'  => $styles,
                'options' => $options,
                'value'   => $value,
            ];
        }

        $maxItemsCount = array_get($this->config, 'max_items_count') ?: 0;

        return compact(
            'imageBlock',
            'language',
            'translations',
            'fields',
            'maxItemsCount'
        );
    }
}
