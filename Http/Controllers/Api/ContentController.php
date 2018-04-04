<?php

namespace Modules\Content\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Content\Models\Entry;
use Modules\Content\Models\WidgetBlock;

class ContentController extends Controller
{

    /**
     * @param null $locale
     * @return mixed
     */
    public function getPages($locale = null)
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }
        $pages = Entry::with([
            'attachments',
            'translations.contentBlocks.items',
            'translations.fields'
        ])->active()->get()->map(function ($item) use ($locale) {
            return $item->formatResponse($locale);
        });

        return response()->json([
            'status' => 'success',
            'data'   => $pages
        ]);
    }

    /**
     * @param null $key
     * @param null $locale
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPage($key = null, $locale = null)
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        $page = Entry::with([
            'attachments',
            'translations.contentBlocks.items',
            'translations.fields'
        ])
            ->where('key', $key)
            ->active()
            ->first();

        if($page) {
            $data = $page->formatResponse($locale);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $data ?? null
        ]);
    }
}
