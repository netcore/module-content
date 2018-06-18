<?php

namespace Modules\Content\Widgets\BackendWorkers;

use Illuminate\Support\Collection;
use Intervention\Image\Facades\Image;
use Modules\Content\Models\ContentBlock;
use Modules\Content\Models\WidgetBlockItemField;
use Netcore\Translator\Helpers\TransHelper;
use Netcore\Translator\Models\Language;

class WidgetBlock implements BackendWorkerInterface
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
    private static $cachedWidgetBlocks = [];

    /**
     * WidgetBlock constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->languages = TransHelper::getAllLanguages();
    }

    /**
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
                        if (substr($serverMimeType, 0, 5) == 'image' OR // Here we trust admins not to upload malicious .svg file. That's odd, but at the moment I dont's see a way to validate .svg images.
                            ($serverMimeType == 'text/html' AND $clientMimeType == 'image/svg+xml')) {
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
     * store() method needs to return data that will be json encoded
     * and put into "data" column in "content_blocks" table
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
     * update() method needs to return data that will be json encoded
     * and put into "data" column in "content_blocks" table
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
        $widgetBlockId = array_get($frontendData, 'widgetBlockId');
        $requestedData = request()->all();

        if (is_numeric($widgetBlockId)) {
            // Real id. Already exists in DB.
            $widgetBlock = \Modules\Content\Models\WidgetBlock::with('items')->find($widgetBlockId);
        } else {
            // $widgetBlockId is random string, something like "Mfjxi"

            $widgetBlock = \Modules\Content\Models\WidgetBlock::create([]);
            // todo - translatable title
        }



        $existingItemIds = $widgetBlock->items()->pluck('id')->toArray();
        $receivedItemIds = [];

        $blocks = (array)array_get($frontendData, 'blocks', []);

        $contentBlock = ContentBlock::find(array_get($frontendData, 'contentBlockId'));

        if($contentBlock) {
            $widget = widgets()->where('key', $contentBlock->widget)->first();
        }

        foreach ($blocks as $index => $block) {

            $block = (array)$block;
            $attributes = (array)array_get($block, 'attributes');

            $widgetBlockItemId = array_get($block, 'widgetBlockItemId');

            if (is_numeric($widgetBlockItemId)) {

                // Real id. Already exists in DB.
                $receivedItemIds[] = $widgetBlockItemId;

                $widgetBlockItem = $widgetBlock->items->where('id', $widgetBlockItemId)->first();

                if (!$widgetBlockItem) {
                    continue;
                }

                // Order
                $widgetBlockItem->update([
                    'order' => ($index + 1)
                ]);

            } else {

                // Create new entry
                // $widgetBlockItemId is random string, something like "Mfjxi"
                $widgetBlockItem = $widgetBlock->items()->create([
                    'order' => ($index + 1)
                ]);
            }

            $configuredFields = array_get($this->config, 'fields');

            // Format WidgetBlockItem attributes
            $widgetBlockItemFields = [];
            $fields = array_keys($attributes);
            foreach ($fields as $field) {

                $config = array_get($configuredFields, $field, []);
                $type = array_get($config, 'type');

                if ($type == 'image') {
                    continue;
                }

                $fieldData = (array)array_get($attributes, $field, []);
                $fieldData = array_flatten($fieldData);
                $value = array_get($fieldData, 0, '');

                $widgetBlockItemFields[] = [
                    //'widget_block_item_id' => $widgetBlockItem->id,
                    'key'   => $field,
                    'value' => $value,
                    'type'  => $type,
                ];
            }

            $existingFields = $widgetBlockItem->fields()->get();
            foreach ($widgetBlockItemFields as $inMemoryField) {

                $key = array_get($inMemoryField, 'key');
                $value = array_get($inMemoryField, 'value');
                $type = array_get($inMemoryField, 'type');

                $dbData = compact('key', 'value');

                if ($type == 'file') {
                    $imageAttribute = (array)array_get($attributes, $key, []);
                    if ($imageAttribute) {
                        $name = array_get($imageAttribute, 'file');
                        $uploadedFile = request()->file($name);
                        if ($name AND $uploadedFile) {
                            $dbData['value'] = null;
                            if(isset($widget)) {
                                $widgetField = $widget->fields->where('key', $key)->first();
                                $fieldOptions = json_decode($widgetField->data);
                                if (in_array($uploadedFile->getClientOriginalExtension(), ['png', 'jpg', 'jpeg', 'gif']) && isset($fieldOptions->width) && isset($fieldOptions->height)) {
                                    if(!file_exists(public_path('uploads/temp/'))) {
                                        mkdir(public_path('uploads/temp/'), 0755, true);
                                    }
                                    $newImage  = public_path('uploads/temp/' .str_random(8) . '.' . $uploadedFile->getClientOriginalExtension());
                                    Image::make($uploadedFile->getRealPath())->resize($fieldOptions->width, $fieldOptions->height)->save($newImage);
                                } else {
                                    $newImage = $uploadedFile;
                                }
                                $dbData['image'] = $newImage;
                            }
                            if(!isset($dbData['image'])) {
                                $dbData['image'] = $uploadedFile;
                            }
                        }
                    }
                }

                $existingField = $existingFields->where('key', $key)->first();

                if ($existingField) {
                    $existingField->update($dbData);
                } else {
                    $widgetBlockItem->fields()->create($dbData);
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

        $deletableItems = $widgetBlock->items()->whereIn('id', $deletableItemIds)->get();
        foreach ($deletableItems as $deletableItem) {
            if ($deletableItem->image) {
                $deletableItem->image = STAPLER_NULL;
                $deletableItem->save();
            }
        }
        $widgetBlock->items()->whereIn('id', $deletableItemIds)->delete();

        return [
            'widget_block_id' => $widgetBlock->id
        ];
    }

    /**
     * delete() gets called right before we execute $contentBlock->delete()
     * This is a good place to remove data in other related tables.
     * For example, if we have ["gallery_id" => 1] in $contentBlock->data,
     * then we should delete that gallery here
     *
     * @param ContentBlock $contentBlock
     */
    public function delete(ContentBlock $contentBlock)
    {
    }

    /**
     * backendTemplateComposer() takes data from "data" column in content_blocks table
     * and transforms to structure that will be injected in widget's backend template
     * For example, there might be ["gallery_id" => 1] in "data" column (content_blocks table)
     * This function would do something like Gallery::find(array_get($data, 'gallery_id'))
     * And then return it.
     *
     * @param          $data
     * @param Language $language
     * @return mixed
     */
    public function backendTemplateComposer(Array $data, Language $language): Array
    {
        $translations = [];
        $widgetBlock = null;

        $widgetBlockId = array_get($data, 'widget_block_id', null);

        $cached = isset(self::$cachedWidgetBlocks[$widgetBlockId]);
        if ($widgetBlockId AND !$cached) {
            self::$cachedWidgetBlocks[$widgetBlockId] = \Modules\Content\Models\WidgetBlock::with([
                'items.fields'
            ])->find($widgetBlockId);
        }

        if ($widgetBlockId) {
            $widgetBlock = array_get(self::$cachedWidgetBlocks, $widgetBlockId);
        }

        if ($widgetBlock) {
            foreach ($widgetBlock->items as $widgetBlockItem) {
                foreach ($widgetBlockItem->fields as $widgetBlockItemField) {
                    $translations[$language->iso_code][$widgetBlockItemField->key] = $widgetBlockItemField->value;
                }
            }
        }

        $configuredFields = array_get($this->config, 'fields');
        $configuredMainFields = array_get($this->config, 'main_fields');
        $fields = [];
        foreach ($configuredFields as $fieldName => $fieldData) {

            $fieldType = array_get($fieldData, 'type');
            $fieldLabel = array_get($fieldData, 'label');
            $styles = array_get($fieldData, 'styles');
            $options = (array)array_get($fieldData, 'options', []);

            $value = $widgetBlock ? object_get($widgetBlock, $fieldName) : '';
            $fieldDatas = [
                'name'    => $fieldName,
                'type'    => $fieldType,
                'label'   => $fieldLabel,
                'styles'  => $styles,
                'options' => $options,
                'value'   => $value,
            ];

            if($fieldType == 'select') {
                if(isset($options['relation']) && $options['relation']) {
                    $selectData = $options['relation_model']::get()->pluck($options['relation_columns'][1], $options['relation_columns'][0])->toArray();
                    $fieldDatas['select_data'] = $selectData;
                } else {
                    $fieldDatas['select_data'] = array_get($options, 'items', []);
                }
            }

            $fields[] = $fieldDatas;
        }

        $mainFields = [];
        foreach ($configuredMainFields as $fieldName => $fieldData) {

            $fieldType = array_get($fieldData, 'type');
            $fieldLabel = array_get($fieldData, 'label');
            $styles = array_get($fieldData, 'styles');
            $options = (array)array_get($fieldData, 'options', []);

            $value = $widgetBlock ? object_get($widgetBlock, $fieldName) : '';
            $fieldDatas = [
                'name'    => $fieldName,
                'type'    => $fieldType,
                'label'   => $fieldLabel,
                'styles'  => $styles,
                'options' => $options,
                'value'   => $value,
            ];

            if($fieldType == 'select') {
                if(isset($options['relation']) && $options['relation']) {
                    $selectData = $options['relation_model']::get()->pluck($options['relation_columns'][1], $options['relation_columns'][0])->toArray();
                    $fieldDatas['select_data'] = $selectData;
                } else {
                    $fieldDatas['select_data'] = array_get($options, 'items', []);
                }
            }

            $mainFields[] = $fieldDatas;
        }

        $maxItemsCount = array_get($this->config, 'max_items_count') ?: 0;

        $contentBlock = null;
        if($widgetBlock) {
            $contentBlock = ContentBlock::with('items')->whereData('{"widget_block_id":' . $widgetBlock->id . '}')->first();
        }

        return compact('widgetBlock', 'language', 'translations', 'fields', 'maxItemsCount', 'mainFields', 'contentBlock');
    }
}
