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