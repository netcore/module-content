<?php

namespace Modules\Content\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Content\Models\Page;

class PageController extends Controller
{
    //@TODO: varbūt šis jāiekļauj Admin modulī
    use \Modules\Crud\Traits\CrudifyController;

    /**
     * @var
     */
    protected $model;

    /**
     * PageController constructor.
     */
    public function __construct()
    {
        $this->model = app(Page::class);
    }

}
