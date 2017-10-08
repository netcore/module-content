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
    public function edit(Entry $entry)
    {
        $languages = TransHelper::getAllLanguages();
        return view('content::module_content.entries.edit', compact('entry', 'languages'));
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
