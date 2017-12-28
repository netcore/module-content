<?php

namespace Modules\Content\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Content\Datatables\EntryDatatable;
use Modules\Content\Http\Requests\Admin\EntryRequest;
use Modules\Content\Models\Channel;
use Modules\Content\Models\Entry;
use Netcore\Translator\Helpers\TransHelper;
use Netcore\Translator\Models\Language;

class EntryController extends Controller
{
    use EntryDatatable;

    /**
     * @param null $channelId
     * @return mixed
     */
    public function create($channelId = null)
    {
        $languages = TransHelper::getAllLanguages();
        $widgetData = $this->widgets();
        $widgetOptions = collect(config('netcore.module-content.widgets'))->pluck('name', 'key');

        $layoutOptions = config('netcore.module-content.layouts', []);

        $channel = $channelId ? Channel::find($channelId) : null;

        return view('content::module_content.entries.create.create', compact(
            'channelId',
            'channel',
            'languages',
            'widgetData',
            'widgetOptions',
            'layoutOptions'
        ));
    }

    /**
     * @param EntryRequest $request
     * @param null $channelId
     * @return mixed
     */
    public function store(EntryRequest $request, $channelId = null)
    {
        $requestData = $request->all();

        $entryData = [];
        if ($channelId) {
            $entryData = [
                'channel_id' => $channelId
            ];
        }

        $entry = Entry::create($entryData);
        $entry->storage()->update($requestData);

        session()->flash('success', 'Page has been stored!');

        return response()->json([
            'success'     => true,
            'redirect_to' => route('content::content.index')
        ]);
    }

    /**
     * @param Entry $entry
     * @return mixed
     */
    public function edit(Entry $entry)
    {
        $entry->load('translations.contentBlocks');
        $channel = $entry->channel;
        $languages = TransHelper::getAllLanguages();

        $widgetData = $this->widgets();
        $widgetOptions = collect(config('netcore.module-content.widgets'))->pluck('name', 'key');

        $layoutOptions = config('netcore.module-content.layouts', []);
        if (!$entry->layout) {
            $layoutOptions = [null => ''] + $layoutOptions;
        }

        return view('content::module_content.entries.edit.edit', compact(
            'entry',
            'channel',
            'languages',
            'widgetData',
            'widgetOptions',
            'layoutOptions'
        ));
    }

    /**
     * @param EntryRequest $request
     * @param Entry $entry
     * @return mixed
     */
    public function update(EntryRequest $request, Entry $entry)
    {
        $requestData = $request->all();
        $entry->storage()->update($requestData);

        session()->flash('success', 'Page has been updated!');

        return response()->json([
            'success'     => true,
            //'redirect_to' => route('content::content.index')
            'redirect_to' => route('content::entries.edit', $entry)
        ]);
    }

    /**
     * @return array
     */
    public function widgets()
    {
        $languages = TransHelper::getAllLanguages();

        $alteredWidgets = collect(config('netcore.module-content.widgets'))->map(function ($widget) use ($languages) {

            $view = array_get($widget, 'backend_template');
            $worker = array_get($widget, 'backend_worker');

            if (!$view) {
                return $widget;
            }

            foreach($languages as $language) {

                $composed = [];

                if ($worker) {
                    $worker = new $worker($widget);
                    $composed = $worker->backendTemplateComposer([], $language);
                }

                if(!is_array($widget['backend_template'])) {
                    $widget['backend_template'] = [];
                }

                $widget['backend_template'][$language->iso_code] = view($view, $composed)->render();
            }

            return $widget;
        });

        $widgetData = [];
        foreach ($alteredWidgets as $alteredWidget) {
            $widgetData[array_get($alteredWidget, 'key')] = $alteredWidget;
        }

        return $widgetData;
    }

    /**
     * @param Entry $entry
     * @return mixed
     */
    public function revisions(Entry $entry)
    {
        $revisions = $entry->children()
            ->whereType('revision')
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit(100)
            ->get();

        return view('content::module_content.entries.revisions.modal', compact(
            'revisions'
        ));
    }

    /**
     * @param Entry $entry
     * @return mixed
     */
    public function destroy(Entry $entry)
    {
        // Delete content blocks
        $entry->storage()->deleteOldContentBlocks();

        // Grab slug before object is deleted
        $slug = '/' . trim($entry->slug, '/');

        // Delete entry itself
        $entry->delete();

        // Hide/show menu items that link to this entry
        $menuItemClass = '\Modules\Admin\Models\MenuItem';
        if(class_exists($menuItemClass)) {
            app($menuItemClass)->whereHas('translations', function($subq) use ($slug) {
                return $subq->whereValue($slug);
            })->update([
                'is_active' => 0
            ]);
        }

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * @param Entry $entry
     * @param Language $language
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyAttachment(Entry $entry, Language $language)
    {
        $entryTranslation = $entry->translations()
            ->whereLocale($language->iso_code)
            ->firstOrFail();

        $entryTranslation->attachment = STAPLER_NULL;
        $entryTranslation->save();

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * @param Entry $page
     * @return mixed
     */
    public function preview(Entry $page)
    {
        die('Preview');
        $locale = app()->getLocale();
        $template = config('netcore.module-content.resolver_template') ?: 'content::module_content.resolver.page';
        return view($template, compact('page'));
    }

    /**
     * @param Entry $entry
     * @return mixed
     */
    public function createDraft(Entry $entry)
    {
        $draft = $entry->revision()->make('draft');

        return redirect()->route('content::entries.edit', $draft);
    }

    /**
     * @param Entry $entry
     * @return mixed
     */
    public function restoreRevision(Entry $entry)
    {
        $restored = $entry->revision()->restore();

        return redirect()->route('content::entries.edit', $restored);
    }
}
