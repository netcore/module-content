<?php

namespace Modules\Content\Composers\Admin\Entry;

use Illuminate\View\View;
use Netcore\Translator\Helpers\TransHelper;

class FormHeader
{
    /**
     * Compose the view
     *
     * @param View $view
     * @return mixed
     */
    public function compose(View $view)
    {
        $allowAttachment = config('netcore.module-content.allow_attachments', false);

        $view->with(compact('allowAttachment'));

        $configuredMetaTags = config('netcore.module-content.meta_tags', []);

        $view->with(compact('configuredMetaTags'));

        $revisionsEnabled = config('netcore.module-content.revisions_enabled', true);

        $view->with(compact('revisionsEnabled'));
    }
}