<?php

namespace Modules\Content\Http\Controllers\Admin;

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
    public function update(Entry $entry)
    {
        return redirect()->back()->withSuccess('Page updated!', compact('entry'));
    }
}
