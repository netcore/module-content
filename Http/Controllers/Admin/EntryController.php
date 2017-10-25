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
        $widgetOptions = collect(config('netcore.module-content.widgets'))->pluck('name', 'key');

        $layoutOptions = config('netcore.module-content.layouts', []);

        return view('content::module_content.entries.create.create', compact(
            'channelId',
            'languages',
            'widgetData',
            'widgetOptions',
            'layoutOptions'
        ));
    }

    /**
     * @param EntryRequest $request
     * @param null $channelId
     * @return mixed
     */
    public function store(EntryRequest $request, $channelId = null)
    {
        $requestData = $request->all();

        $entryData = [];
        if ($channelId) {
            $entryData = [
                'channel_id' => $channelId
            ];
        }

        $entry = Entry::create($entryData);
        $entry->storage()->update($requestData);

        session()->flash('success', 'Page has been stored!');

        return response()->json([
            'success'     => true,
            'redirect_to' => route('content::content.index')
        ]);
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
        $widgetOptions = collect(config('netcore.module-content.widgets'))->pluck('name', 'key');

        $layoutOptions = config('netcore.module-content.layouts', []);
        if (!$entry->layout) {
            $layoutOptions = [null => ''] + $layoutOptions;
        }

        return view('content::module_content.entries.edit.edit', compact(
            'entry',
            'languages',
            'widgetData',
            'widgetOptions',
            'layoutOptions'
        ));
    }

    /**
     * @param EntryRequest $request
     * @param Entry $entry
     * @return mixed
     */
    public function update(EntryRequest $request, Entry $entry)
    {
        $requestData = $request->all();
        $entry->storage()->update($requestData);

        session()->flash('success', 'Page has been updated!');

        return response()->json([
            'success'     => true,
            //'redirect_to' => route('content::content.index')
            'redirect_to' => route('content::entries.edit', $entry)
        ]);
    }

    /**
     * @return array
     */
    public function widgets()
    {
        $alteredWidgets = collect(config('netcore.module-content.widgets'))->map(function ($widget) {

            $view = array_get($widget, 'backend_template');
            $worker = array_get($widget, 'backend_worker');

            $composed = [];
            if ($worker) {
                $worker = new $worker($widget);
                $composed = $worker->backendTemplateComposer([]);
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
     * @param Entry $entry
     * @return mixed
     */
    public function destroy(Entry $entry)
    {
        // Delete content blocks
        $entry->storage()->deleteOldContentBlocks();

        // Delete entry itself
        $entry->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
