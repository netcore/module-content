<?php

namespace Modules\Content\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Modules\Content\Models\Entry;
use Yajra\Datatables\Datatables;
use Netcore\Translator\Helpers\TransHelper;

trait EntryDatatable
{

    /**
     * Prepare data for jQuery datatable
     *
     * @return mixed
     */
    public function pagination()
    {
        $query = $this->getQuery();

        $languages = TransHelper::getAllLanguages();

        return datatables()->of($query)
            ->editColumn('title', function ($entry) use ($languages) {
                return view('content::module_content.entries.tds.title', compact('entry', 'languages'))->render();
            })
            ->editColumn('slug', function ($entry) use ($languages) {
                return view('content::module_content.entries.tds.slug', compact('entry', 'languages'))->render();
            })
            ->editColumn('content', function ($entry) use ($languages) {
                $language = $languages->first();
                $translated = trans_model($entry, $language, 'content');
                $stripped = strip_tags($translated);
                return str_limit($stripped, 100);
            })
            ->editColumn('updated_at', function ($entry) {
                $updatedAt = $entry->updated_at;
                return $updatedAt ? $updatedAt->format('d.m.Y H:i') : '-';
            })
            ->editColumn('created_at', function ($entry) {
                $createdAt = $entry->created_at;
                return $createdAt ? $createdAt->format('d.m.Y H:i') : '-';
            })
            ->editColumn('is_active', function ($entry) {
                return view('content::module_content.entries.tds.is_active', compact('entry'))->render();
            })
            ->addColumn('action', function ($entry) {
                return view('content::module_content.entries.tds.action', compact('entry'))->render();
            })
            ->rawColumns(['action', 'title', 'slug', 'is_active'])
            ->toJson();
    }

    /**
     * @return Builder
     */
    private function getQuery()
    {
        $channelId = request()->get('channel_id');

        $searchData = (array) request()->get('search', []);
        $keyword = (string) array_get($searchData, 'value');

        $query = Entry::with([
            'contentBlocks'
        ])->orderBy('id', 'DESC');

        if($channelId) {
            $query->whereChannelId($channelId);
        } else {
            $query->whereNull('channel_id');
        }

        return $query;
    }
}