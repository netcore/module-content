<?php

namespace Modules\Content\Composers\Admin\Entry;

use Illuminate\View\View;
use Netcore\Translator\Helpers\TransHelper;

class MetaTags
{
    /**
     * Compose the view
     *
     * @param View $view
     * @return mixed
     */
    public function compose(View $view)
    {
        $configuredMetaTags = config('netcore.module-content.meta_tags', []);

        $view->with(compact('configuredMetaTags'));
    }
}