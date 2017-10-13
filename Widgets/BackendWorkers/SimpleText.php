<?php

namespace Modules\Content\Widgets\BackendWorkers;

use Modules\Content\Models\ContentBlock;
use Modules\Content\Models\HtmlBlock;
use Netcore\Translator\Helpers\TransHelper;

class SimpleText implements BackendWorkerInterface
{

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
        $translations = (array)array_get($frontendData, 'translations', []);
        $translations = array_map(function ($translation) {
            return (array)$translation;
        }, $translations);

        $htmlBlock = HtmlBlock::create([]);
        $htmlBlock->storeTranslations($translations);

        return [
            'html_block_id' => $htmlBlock->id
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
     * @return mixed
     */
    public function backendTemplateComposer(Array $data): Array
    {
        $languages = TransHelper::getAllLanguages();
        $translations = [];

        $htmlBlockId = array_get($data, 'html_block_id', null);
        if ($htmlBlockId) {
            $htmlBlock = HtmlBlock::find($htmlBlockId);
            foreach ($htmlBlock->translations as $translation) {
                $translations[$translation->locale] = [
                    'content' => $translation->content
                ];
            }
        }

        return [
            'languages'    => $languages,
            'translations' => $translations
        ];
    }
}
