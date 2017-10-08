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

        $widgetData = collect(config('module_content.widgets'))->map(function($widget){
            return $widget;
        });

        $widgetOptions = collect($widgetData)->map(function($widget, $key){
            return [
                'name' => array_get($widget, 'name'),
                'key' => $key
            ];
        })->pluck('name', 'key');

        return view('content::module_content.entries.edit', compact('entry', 'languages', 'widgetData', 'widgetOptions'));
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
