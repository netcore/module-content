<?php

namespace Modules\Content\Composers\Admin\Entry;

use Illuminate\View\View;
use Netcore\Translator\Helpers\TransHelper;

class EntryTitle
{
    /**
     * Compose the view
     *
     * @param View $view
     * @return mixed
     */
    public function compose(View $view)
    {
        $languages = TransHelper::getAllLanguages();
        
        $view->with(compact('languages'));
    }
}