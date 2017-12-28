<?php

namespace Modules\Content\Composers\Admin\Entry;

use Illuminate\View\View;
use Netcore\Translator\Helpers\TransHelper;

class IndexNavTabs
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
    }
}