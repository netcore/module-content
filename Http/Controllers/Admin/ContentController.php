<?php

namespace Modules\Content\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Content\Models\Channel;
use Modules\Content\Models\Section;
use Netcore\Translator\Helpers\TransHelper;

class ContentController extends Controller
{
    /**
     *
     * @var String
     */
    private $viewNamespace = 'content::module_content';

    public function index()
    {
        $languages = TransHelper::getAllLanguages();
        $firstLanguage = $languages->first();
        $channels = Channel::orderBy('id')->get();
        return view($this->viewNamespace . '.index.index', compact(
            'channels',
            'firstLanguage'
        ));
    }

}
