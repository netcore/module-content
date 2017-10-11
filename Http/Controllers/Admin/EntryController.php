<?php

namespace Modules\Content\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Content\Datatables\EntryDatatable;
use Modules\Content\Models\Entry;
use Netcore\Translator\Helpers\TransHelper;
use ReflectionClass;

class EntryController extends Controller
{
    use EntryDatatable;

    /**
     * @param Entry $entry
     * @return mixed
     */
    public function create()
    {

        return redirect()->back();

        $languages = TransHelper::getAllLanguages();
        return view('content::module_content.entries.create', compact('languages'));
    }

    /**
     * @param Entry $entry
     * @return mixed
     */
    public function edit(Entry $entry)
    {
        $entry->load('contentBlocks');
        $languages = TransHelper::getAllLanguages();

        $widgetData = $this->widgets();
        $widgetOptions = collect(config('module_content.widgets'))->pluck('name', 'key');

        return view('content::module_content.entries.edit.edit', compact(
            'entry',
            'languages',
            'widgetData',
            'widgetOptions'
        ));
    }

    /**
     * @return array
     */
    public function widgets()
    {
        $alteredWidgets = collect(config('module_content.widgets'))->map(function($widget){

            $view = array_get($widget, 'backend_template');
            if($view) {
                $widget['backend_template'] = view($view)->render();
            }

            return $widget;
        });

        $widgetData = [];
        foreach($alteredWidgets as $alteredWidget) {
            $widgetData[array_get($alteredWidget, 'key')] = $alteredWidget;
        }

        return $widgetData;
    }

    /**
     * @param Entry $entry
     * @return mixed
     */
    public function update(Entry $entry, Request $request)
    {
        $entryTranslations = $request->get('translations'); // slug, title
        $contentBlocks = json_decode($request->get('widgets', null));
        $contentBlocks = array_map(function($contentBlock){
            return (array) $contentBlock;
        }, $contentBlocks);
        
        // Save translations
        $entry->updateTranslations($entryTranslations);

        // Save widgets and their data
        // 1. Put data in $entry->content_blocks table
        // 1.1 Put data in additional tables, according to each specific widget

        foreach($entry->contentBlocks as $oldContentBlock) {

            $key = $oldContentBlock->widget;
            $config = collect(config('module_content.widgets'))->where('key', $key)->first();
            $backendWorker = array_get($config, 'backend_worker');

            // Delete data in related tables
            if($backendWorker AND method_exists($backendWorker, 'delete')) {
                app($backendWorker)->delete($oldContentBlock);
            }

            $oldContentBlock->delete();
        }

        foreach($contentBlocks as $index => $contentBlock) {

            $key = array_get($contentBlock, 'widget');
            $config = collect(config('module_content.widgets'))->where('key', $key)->first();
            $backendWorker = array_get($config, 'backend_worker');

            $data = [];
            if($backendWorker AND method_exists($backendWorker, 'store')) {
                $data = app($backendWorker)->store($contentBlock);
            }

            $contentBlockData = [
                'order'  => ($index+1),
                'widget' => $key,
                'data'   => json_encode($data)
            ];

            $entry->contentBlocks()->create($contentBlockData);
        }

        return response()->json([
            'success' => true
        ]);
    }
}
