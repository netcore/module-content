<?php

namespace Modules\Content\Composers\Admin\Entry;

use Illuminate\View\View;
use Netcore\Translator\Helpers\TransHelper;

class Revisions
{
    /**
     * Compose the view
     *
     * @param View $view
     * @return mixed
     */
    public function compose(View $view)
    {
        $revisionsEnabled = config('netcore.module-content.revisions_enabled', true);

        $view->with(compact('revisionsEnabled'));
    }
}