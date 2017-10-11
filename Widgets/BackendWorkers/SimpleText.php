<?php

namespace Modules\Content\Widgets\BackendWorkers;

use Modules\Content\Models\ContentBlock;
use Modules\Content\Models\HtmlBlock;
use Netcore\Translator\Helpers\TransHelper;

class SimpleText implements BackendWorkerInterface
{

    /**
     *
     * parse() method needs to return data that will be json encoded
     * and put into "data" column in "content_blocks" table
     *
     * Additionally, it should put data in any other related tables.
     * For example, if we have widget "gallery_slider", we might store
     * ["gallery_id" => 1] in "data" column and put any actual data
     * in "galleries" table
     *
     * @param $contentBlock
     * @return array
     */
    public function store(Array $frontendData)
    {
        //$htmlBlock = HtmlBlock::create([]);
        //$htmlBlock->storeTranslations($frontendData);
        
        return [
            //'html_block_id' => $htmlBlock->id
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
     * @return mixed
     */
    public function backendTemplateComposer(Array $data)
    {
        $languages = TransHelper::getAllLanguages();
        $value = 'Random <b>bold</b>';

        return [
            'languages' => $languages,
            'value' => $value
        ];
    }
}
