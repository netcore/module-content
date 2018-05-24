<?php

namespace Modules\Content\Http;

use Illuminate\Routing\Router;

class ResolverRouter
{

    /**
     * Register resolver routes
     *
     * @return void
     */
    public static function register(Router $router)
    {
        $router->group([
            'as'         => 'content::',
            'middleware' => ['web'],
            'namespace'  => 'Modules\Content\Http\Controllers\Client'
        ], function (Router $router) {

            $router->get('/{slug}', [
                'uses'       => 'ResolverController@resolve',
                'as'         => 'resolver',
                'middleware' => []
            ])->where([
                'slug' => '^(?!_debugbar)(?!admin).*$'
            ]);
        });
    }

}