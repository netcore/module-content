<?php

namespace Modules\Content\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Modules\Content\Models\Entry;
use Yajra\Datatables\Datatables;

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

        return datatables()->of($query)
            ->editColumn('slug', function ($entry) {
                return view('content::module_content.entries.tds.slug', compact('entry'))->render();
            })
            ->editColumn('content', function ($entry) {
                return str_limit($entry->content, 100);
            })
            ->editColumn('updated_at', function ($entry) {
                $updatedAt = $entry->updated_at;
                return $updatedAt ? $updatedAt->format('d.m.Y H:i') : '-';
            })
            ->editColumn('is_active', function ($entry) {
                return view('content::module_content.entries.tds.is_active', compact('entry'))->render();
            })
            ->addColumn('action', function ($entry) {
                return view('content::module_content.entries.tds.action', compact('entry'))->render();
            })
            ->rawColumns(['action', 'slug', 'is_active'])
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
        ]);

        if($channelId) {
            $query->whereChannelId($channelId);
        } else {
            $query->whereNull('channel_id');
        }

        return $query;
    }
}