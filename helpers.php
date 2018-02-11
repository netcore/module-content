<?php
if (!function_exists('content')) {
    /**
     * @return \Illuminate\Foundation\Application
     */
    function content()
    {
        return app('content');
    }
}

if (!function_exists('widgets')) {
    /**
     * @return mixed
     */
    function widgets()
    {
        return cache()->rememberForever('content_widgets', function () {
            return \Modules\Content\Modules\Widget::with('fields')->get();
        });
    }
}