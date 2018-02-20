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
if (!function_exists('content_crud_route')) {
    function content_crud_route($to, $parameters = null)
    {
        $routeName = request()->route()->getName();
        $namespace = '';
        $hasNamespace = strpos($routeName, '::');
        if ($hasNamespace) {
            $namespaceSegments = explode('::', $routeName);
            $namespace = array_get($namespaceSegments, 0) . '::';
        }
        $routeName = str_replace($namespace, '', $routeName);
        $segments = explode('.', $routeName);
        array_pop($segments);
        $segments[] = $to;
        $routeName = $namespace . implode('.', $segments);
        if ($parameters) {
            return route($routeName, $parameters);
        }

        return route($routeName);
    }
}