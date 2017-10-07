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
            ->addColumn('action', function ($entry) {
                return view('content::module_content.index.tds.action', compact('entry'))->render();
            })
            ->editColumn('is_active', function ($entry) {
                return view('content::module_content.index.tds.is_active', compact('entry'))->render();
            })
            ->editColumn('content', function ($entry) {
                return str_limit($entry->content, 100);
            })
            ->editColumn('updated_at', function ($advertisement) {
                $updatedAt = $advertisement->updated_at;
                return $updatedAt ? $updatedAt->format('d.m.Y H:i') : '-';
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

        return $query;
    }
}