<?php

namespace Modules\Content\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Modules\Content\Models\Entry;

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

        try {
            $pages = Entry::with([
                'attachments',
                'translations.contentBlocks.items',
                'translations.fields'
            ])->active()->get()->map(function ($item) use ($locale) {
                return $item->formatResponse($locale);
            });

            return response()->json([
                'success' => true,
                'data'    => $pages
            ]);
        } catch (\Exception $e) {
            logger()->error($e);

            return response()->json([
                'success' => false,
                'data'    => []
            ]);
        }
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

        try {
            $page = Entry::with([
                'attachments',
                'translations.contentBlocks.items',
                'translations.fields'
            ])->where('key', $key)->active()->first();

            if (!$page) {
                return response()->json([
                    'success' => false,
                    'data'    => []
                ], 404);
            }

            $data = $page->formatResponse($locale);

            return response()->json([
                'success' => true,
                'data'    => $data
            ]);
        } catch (\Exception $e) {
            logger()->error($e);
            
            return response()->json([
                'success' => false,
                'data'    => []
            ]);
        }
    }
}
