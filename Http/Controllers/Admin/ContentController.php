<?php

namespace Modules\Content\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Content\Models\Channel;
use Modules\Content\Models\Section;

class ContentController extends Controller
{
    /**
     *
     * @var String
     */
    private $viewNamespace = 'content::module_content';

    public function index()
    {
        $sections = Section::orderBy('order')->get();
        $channels = Channel::orderBy('name')->get();
        return view($this->viewNamespace . '.index', compact('sections', 'channels'));
    }

}
