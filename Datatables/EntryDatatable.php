<?php

namespace Modules\Content\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Modules\Content\Models\Entry;
use Modules\Content\Translations\EntryTranslation;
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
                $title = trans_model($entry, $language, 'title');
                return str_limit($stripped, 100) ?: $title;
            })
            ->editColumn('updated_at', function ($entry) {
                $updatedAt = $entry->updated_at;
                return $updatedAt ? $updatedAt->format('d.m.Y H:i') : '-';
            })
            ->addColumn('published_at', function ($entry) {
                $publishedAt = $entry->published_at;
                return $publishedAt ? $publishedAt->format('d.m.Y') : '-';
            })
            ->editColumn('is_homepage', function ($entry) {
                return view('content::module_content.entries.tds.is_homepage', compact('entry'))->render();
            })
            ->editColumn('is_active', function ($entry) {
                return view('content::module_content.entries.tds.is_active', compact('entry'))->render();
            })
            ->addColumn('attachment', function ($entry) use ($languages) {
                return view('content::module_content.entries.tds.attachment', compact('entry', 'languages'))->render();
            })
            ->addColumn('action', function ($entry) {
                return view('content::module_content.entries.tds.action', compact('entry'))->render();
            })
            ->rawColumns(['attachment', 'action', 'title', 'slug', 'is_active', 'is_homepage'])
            ->toJson();
    }

    /**
     * @return Builder
     */
    private function getQuery()
    {
        $channelId = request()->get('channel_id');

        $searchData = (array)request()->get('search', []);
        $keyword = (string)array_get($searchData, 'value');

        $query = Entry::orderBy('is_homepage', 'DESC')// Homepage always at the top.
        ->orderBy('published_at', 'DESC')
            ->orderBy('id', 'DESC');

        if ($channelId) {
            $query->whereChannelId($channelId);
        } else {
            $query->whereNull('channel_id');
        }

        if ($keyword) {
            $query->whereHas('translations', function ($subq) use ($keyword) {
                return $subq
                    ->where('netcore_content__entry_translations.title', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('netcore_content__entry_translations.slug', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('netcore_content__entry_translations.content', 'LIKE', '%' . $keyword . '%');
            });
        }

        return $query;
    }
}