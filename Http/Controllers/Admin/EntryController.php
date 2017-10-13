<?php

namespace Modules\Content\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Content\Datatables\EntryDatatable;
use Modules\Content\Http\Requests\Admin\EntryRequest;
use Modules\Content\Models\Entry;
use Modules\Content\Models\HtmlBlock;
use Netcore\Translator\Helpers\TransHelper;
use ReflectionClass;

class EntryController extends Controller
{
    use EntryDatatable;

    /**
     * @param null $channelId
     * @return mixed
     */
    public function create($channelId = null)
    {
        $languages = TransHelper::getAllLanguages();
        $widgetData = $this->widgets();
        $widgetOptions = collect(config('module_content.widgets'))->pluck('name', 'key');

        return view('content::module_content.entries.create.create', compact(
            'channelId',
            'languages',
            'widgetData',
            'widgetOptions'
        ));
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
        $alteredWidgets = collect(config('module_content.widgets'))->map(function ($widget) {

            $view = array_get($widget, 'backend_template');
            $worker = array_get($widget, 'backend_worker');

            $composed = [];
            if ($worker) {
                $composed = app($worker)->backendTemplateComposer([]);
            }

            if ($view) {
                $widget['backend_template'] = view($view, $composed)->render();
            }

            return $widget;
        });

        $widgetData = [];
        foreach ($alteredWidgets as $alteredWidget) {
            $widgetData[array_get($alteredWidget, 'key')] = $alteredWidget;
        }

        return $widgetData;
    }

    /**
     * @param EntryRequest $request
     * @param null $channelId
     * @return mixed
     */
    public function store(EntryRequest $request, $channelId = null)
    {
        $contentBlocks = json_decode($request->get('widgets', null));
        $contentBlocks = (array) array_map(function ($contentBlock) {
            return (array)$contentBlock;
        }, $contentBlocks);

        $entryTranslations = (array) $request->get('translations', []); // slug, title

        $entryData = [];
        if($channelId) {
            $entryData = [
                'channel_id' => $channelId
            ];
        }

        $entry = Entry::create($entryData);
        $entry->storage()->update(
            $contentBlocks,
            $entryTranslations
        );

        return response()->json([
            'success' => true,
            'redirect_to' => route('content::content.index')
        ]);
    }

    /**
     * @param EntryRequest $entry
     * @param Request $request
     * @return mixed
     */
    public function update(EntryRequest $entry, Request $request)
    {
        $contentBlocks = json_decode($request->get('widgets', null));
        $contentBlocks = (array) array_map(function ($contentBlock) {
            return (array)$contentBlock;
        }, $contentBlocks);
        
        $entryTranslations = (array) $request->get('translations', []); // slug, title
        
        $updatedEntry = $entry->storage()->update(
            $contentBlocks, 
            $entryTranslations
        );

        return response()->json([
            'success' => true
        ]);
    }
}
