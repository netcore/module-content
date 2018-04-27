<?php

namespace Modules\Content\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Content\Datatables\EntryDatatable;
use Modules\Content\Http\Requests\Admin\EntryRequest;
use Modules\Content\Models\Channel;
use Modules\Content\Models\Entry;
use Modules\Content\Models\EntryAttachment;
use Modules\Content\Modules\Widget;
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

        $widgets = widgets();
        $widgetData = $this->widgets($widgets);
        $widgetOptions = $widgets->pluck('title', 'key');

        $layoutOptions = config('netcore.module-content.layouts', []);

        $channel = $channelId ? Channel::find($channelId) : null;

        return view('content::module_content.entries.create.create',
            compact('channelId', 'channel', 'languages', 'widgetData', 'widgetOptions', 'layoutOptions'));
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

        $makeRevision = false;
        $entry = Entry::create($entryData);
        $entry->storage()->update($requestData, $makeRevision);

        $this->storeEntryFields($requestData, $entry);
        $this->storeAttachments($requestData, $entry);
        $this->saveEntriesToCache();

        $this->updateRequiredLanguages($request, $entry);

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
    public function edit($entry)
    {
        $entry = Entry::with([
            'attachments',
            'translations.contentBlocks.items',
            'translations.fields',
            'translations.metaTags',
            'channel'
        ])->find($entry);

        $channel = $entry->channel;
        $languages = TransHelper::getAllLanguages();

        $widgets = widgets();
        $widgetData = $this->widgets($widgets);
        $widgetOptions = $widgets->pluck('title', 'key');

        $layoutOptions = config('netcore.module-content.layouts', []);
        if (!$entry->layout) {
            $layoutOptions = [null => ''] + $layoutOptions;
        }

        return view('content::module_content.entries.edit.edit',
            compact('entry', 'channel', 'languages', 'widgetData', 'widgetOptions', 'layoutOptions'));
    }

    /**
     * @param EntryRequest $request
     * @param Entry $entry
     * @return mixed
     */
    public function update(EntryRequest $request, Entry $entry)
    {
        $requestData = $request->all();

        $makeRevision = config('netcore.module-content.revisions_enabled', true);
        $entry->storage()->update($requestData, $makeRevision);

        $this->storeEntryFields($requestData, $entry);
        $this->storeAttachments($requestData, $entry);
        $this->saveEntriesToCache();

        $this->updateRequiredLanguages($request, $entry);

        session()->flash('success', 'Page has been updated!');

        return response()->json([
            'success'     => true,
            'redirect_to' => route('content::entries.edit', $entry)
        ]);
    }

    /**
     * @param $request
     * @param $entry
     */
    private function updateRequiredLanguages($request, $entry)
    {
        if ($requiredLanguages = $request->get('is_language_required', null)) {
            foreach ($requiredLanguages as $lang => $value) {
                $entry->translations()->where('locale', $lang)->update(['is_language_required' => $value]);
            }
        }
    }

    /**
     *
     */
    private function saveEntriesToCache()
    {
        cache_content_entries();
    }

    /**
     * @param $requestData
     * @param $entry
     */
    private function storeEntryFields($requestData, $entry)
    {
        if (isset($requestData['global_field'])) {
            foreach ($requestData['global_field'] as $key => $value) {
                $entry->globalFields()->updateOrCreate(['key' => $key], [
                    'key'   => $key,
                    'value' => $value,
                ]);
            }
        }
        if (isset($requestData['translations']['entry'])) {
            foreach ($requestData['translations']['entry'] as $isoCode => $data) {
                $translations = $entry->translations->where('locale', $isoCode)->first();
                if ($translations) {
                    foreach ($data as $key => $value) {
                        $translations->fields()->updateOrCreate(['key' => $key], [
                            'key'   => $key,
                            'value' => $value,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * @param $requestData
     * @param $entry
     */
    private function storeAttachments($requestData, $entry)
    {
        if (isset($requestData['attachments'])) {
            $i = 1;
            $media = '';
            if (isset($requestData['media'])) {
                $media = $requestData['media'];
            }
            foreach ($requestData['attachments'] as $a) {
                $attachment = $entry->attachments()->create([
                    'media' => $media
                ]);

                $attachment->image = $a;
                $attachment->save();
                $i++;
            }
        }
    }

    /**
     * @param null $widgets
     * @return array
     */
    public function widgets($widgets = null)
    {
        $languages = TransHelper::getAllLanguages();

        if (!$widgets) {
            $widgets = widgets();
        }

        $widgetList = [];
        foreach ($widgets as $widget) {
            $fields = [];


            foreach ($widget->fields as $field) {
                $fields[$field->key] = [
                    'type'  => $field->type,
                    'label' => $field->title,
                ];
            }

            $widgetData = $widget->config;

            $view = array_get($widgetData, 'backend_template');
            $worker = array_get($widgetData, 'backend_worker');

            if (!$view) {
                return $widget;
            }

            foreach ($languages as $language) {
                $composed = [];


                if ($worker) {
                    $worker = new $worker($widgetData);
                    $composed = $worker->backendTemplateComposer([], $language);
                }

                if (!is_array($widgetData['backend_template'])) {
                    $widgetData['backend_template'] = [];
                }

                $widgetData['backend_template'][$language->iso_code] = view($view, $composed)->render();

            }


            $widgetList[$widget->key] = $widgetData;

        }


        return $widgetList;
    }

    /**
     * @param Entry $entry
     * @return mixed
     */
    public function revisions(Entry $entry)
    {
        $revisions = $entry
            ->children()
            ->whereType('revision')
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit(100)
            ->get();

        return view('content::module_content.entries.revisions.modal', compact('revisions'));
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
        if (class_exists($menuItemClass)) {
            app($menuItemClass)->whereHas('translations', function ($subq) use ($slug) {
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
     * @param EntryAttachment $entryAttachment
     * @return \Illuminate\Http\JsonResponse
     * @internal param Entry $entry
     * @internal param Language $language
     */
    public function destroyAttachment($entryAttachment)
    {
        $entryAttachment = EntryAttachment::find($entryAttachment);
        $entryAttachment->image = STAPLER_NULL;
        $entryAttachment->save();

        $entryAttachment->delete();

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

    /**
     * @param Request $request
     * @return array
     */
    public function attachmentState(Request $request)
    {
        EntryAttachment::where('id', $request->get('id', null))
            ->update([
                'is_featured' => (int)$request->get('state', 0)
            ]);

        return [
            'state' => 'success'
        ];
    }
}
