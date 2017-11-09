<?php

namespace Modules\Content\Http\Controllers\Client;

use Modules\Content\Models\Entry;

class ApiController
{

    /**
     * Register resolver routes
     *
     * @return void
     */
    public static function entry(Entry $entry)
    {
        $response = [
            'content' => $entry->content
        ];

        return $response;
    }

}