<?php

namespace Modules\Content\Widgets\BackendWorkers;

use Modules\Content\Models\ContentBlock;
use Netcore\Translator\Helpers\TransHelper;

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
     * ImageBlock constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
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

        /*
        $order = array_get($widget, 'order');
        $data = (array)array_get($widget, 'data');
        $translations = (array)array_get($data, 'translations');

        $languagesInSystem = TransHelper::getAllLanguages();
        foreach ($languagesInSystem as $languageInSystem) {
            $translationsForThatLanguage = (array)array_get($translations, $languageInSystem->iso_code, []);

            // strip_tags() is necessary because content can be something like this: '<p><br></p>'
            // In reality it's empty, so testing length of string is not enough
            $content = strip_tags(
                array_get($translationsForThatLanguage, 'content')
            );

            if (!$content) {
                $key = 'widgets.' . $order . '.' . $languageInSystem->iso_code . '.content';
                $errors[$key] = 'Content is required';
            }
        }
        */

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
        /*
        $translations = (array)array_get($frontendData, 'translations', []);
        $translations = array_map(function ($translation) {
            return (array)$translation;
        }, $translations);

        $htmlBlock = ImageBlock::create([]);
        $htmlBlock->storeTranslations($translations);

        return [
            'html_block_id' => $htmlBlock->id
        ];
        */
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

        $languages = TransHelper::getAllLanguages();
        $blocks = (array)array_get($frontendData, 'blocks', []);

        foreach ($blocks as $index => $block) {

            $block = (array)$block;
            $attributes = (array)array_get($block, 'attributes');

            // Format ImageBlockItem translations
            $imageBlockItemTranslations = [];
            $fields = array_keys($attributes);
            $locales = $languages->pluck('iso_code')->toArray();
            foreach ($locales as $locale) {
                foreach ($fields as $field) {
                    $fieldData = (array)array_get($attributes, $field, []);
                    $value = array_get($fieldData, $locale, '');
                    $imageBlockItemTranslations[$locale][$field] = $value;
                }
            }

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

            // Translations
            $imageBlockItem->updateTranslations($imageBlockItemTranslations);

            // Image
            // todo
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
            'image_block_id' => $imageBlockId
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
        /*
        $htmlBlockId = array_get($contentBlock->data, 'html_block_id');
        if ($htmlBlockId) {
            ImageBlock::whereId($htmlBlockId)->delete();
        }
        */
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
     * @return mixed
     */
    public function backendTemplateComposer(Array $data): Array
    {
        $languages = TransHelper::getAllLanguages();
        $translations = [];

        $imageBlockId = array_get($data, 'image_block_id', null);
        $imageBlock = \Modules\Content\Models\ImageBlock::find($imageBlockId);
        if ($imageBlockId) {
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
        foreach ($configuredFields as $configuredField) {
            $value = $imageBlock ? object_get($imageBlock, $configuredField) : '';
            $fields[$configuredField] = $value;
        }

        return compact(
            'imageBlock',
            'languages',
            'translations',
            'fields'
        );
    }
}
