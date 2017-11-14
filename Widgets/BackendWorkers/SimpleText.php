<?php

namespace Modules\Content\Widgets\BackendWorkers;

use Modules\Content\Models\ContentBlock;
use Modules\Content\Models\HtmlBlock;
use Netcore\Translator\Helpers\TransHelper;
use Netcore\Translator\Models\Language;

class SimpleText implements BackendWorkerInterface
{
    /**
     * @var string "recreate" || "update"
     */
    public $action = 'recreate';

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

        $order = array_get($widget, 'order');
        $data = (array)array_get($widget, 'data');
        $translations = (array)array_get($data, 'translations');
        $configuredFields = array_get($this->config, 'fields');

        $languagesInSystem = TransHelper::getAllLanguages();
        foreach ($languagesInSystem as $languageInSystem) {
            $translationsForThatLanguage = (array)array_get($translations, $languageInSystem->iso_code, []);

            foreach ($translationsForThatLanguage as $field => $value) {

                $fieldConfig = array_get($configuredFields, $field, []);
                $fieldType = array_get($fieldConfig, 'type', 'textarea');

                // strip_tags() is necessary because content can be something like this: '<p><br></p>'
                // In reality it's empty, so testing length of string is not enough
                $value = strip_tags($value);
                $key = 'specificField.' . $order . '.' . $languageInSystem->iso_code . '.' . $field;

                if ($fieldType != 'checkbox' AND !$value) {
                    $errors[$key] = 'Field is required';
                }

                $maxlength = 191;

                if ($fieldType == 'textarea') {
                    $maxlength = 8000000; // 8MB
                }

                if (mb_strlen($value) > $maxlength) {
                    $errors[$key] = 'Data is too long';
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
     * @return array
     */
    public function store(Array $frontendData): Array
    {
        $rawTranslations = (array)array_get($frontendData, 'translations', []);
        $rawTranslations = array_map(function ($translation) {
            return (array)$translation;
        }, $rawTranslations);

        // Format HtmlBlock translations
        $formattedTranslations = [];

        foreach ($rawTranslations as $locale => $attributes) {
            foreach ($attributes as $field => $value) {

                $nonJsonFields = ['content'];
                $isNonJsonField = in_array($field, $nonJsonFields);


                if ($isNonJsonField) {
                    $formattedTranslations[$locale][$field] = $value;
                } else {
                    $formattedTranslations[$locale]['json'][$field] = $value;
                }
            }
        }

        $htmlBlock = HtmlBlock::create([]);
        $htmlBlock->storeTranslations($formattedTranslations);

        return [
            'html_block_id' => $htmlBlock->id
        ];
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
        $htmlBlockId = array_get($contentBlock->data, 'html_block_id');
        if ($htmlBlockId) {
            HtmlBlock::whereId($htmlBlockId)->delete();
        }
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
        //$languages = TransHelper::getAllLanguages();
        $configuredFields = array_get($this->config, 'fields');
        $translations = [];

        $htmlBlockId = array_get($data, 'html_block_id', null);
        $htmlBlock = HtmlBlock::find($htmlBlockId);
        if ($htmlBlock) {
            foreach ($htmlBlock->translations as $translation) {

                $jsonString = $translation->json;
                $jsonDecoded = (array)json_decode($jsonString);

                $translatedItem = [
                    'content' => $translation->content,
                ];

                foreach ($configuredFields as $fieldName => $fieldData) {
                    $translatedItem[$fieldName] = array_get($jsonDecoded, $fieldName);
                }

                $translations[$translation->locale] = $translatedItem;
            }
        }

        $fields = [];
        foreach ($configuredFields as $fieldName => $fieldData) {

            $fieldType = array_get($fieldData, 'type');
            $fieldLabel = array_get($fieldData, 'label');
            $options = (array)array_get($fieldData, 'options', []);

            $fields[] = [
                'name'    => $fieldName,
                'type'    => $fieldType,
                'label'   => $fieldLabel,
                'options' => $options,
            ];
        }

        return compact(
            'htmlBlock',
            'fields',
            'language',
            'translations'
        );
    }
}
