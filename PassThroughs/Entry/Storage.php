<?php

namespace Modules\Content\PassThroughs\Entry;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Content\Models\ContentBlock;
use Modules\Content\Models\Entry;
use Modules\Content\Models\HtmlBlock;
use Modules\Content\Models\MetaTag;
use Modules\Content\Modules\Widget;
use Modules\Content\PassThroughs\PassThrough;
use Modules\Content\Translations\EntryTranslation;
use Netcore\Translator\Helpers\TransHelper;

class Storage extends PassThrough
{

    /**
     * @var Entry
     */
    private $entry;

    /**
     * @var Collection
     */
    private $languages;

    /**
     * Storage constructor.
     *
     * @param Entry $entry
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
        $this->languages = TransHelper::getAllLanguages();
    }

    /**
     * @param array $requestData
     * @return Entry
     */
    public function update(Array $requestData, Bool $makeRevision): Entry
    {
        $entry = DB::transaction(function () use ($requestData, $makeRevision) {
            return $this->transaction($requestData, $makeRevision);
        });

        return $entry;
    }

    /**
     * @param array $requestData
     * @param bool $makeRevision
     * @return Entry
     */
    private function transaction(Array $requestData, Bool $makeRevision): Entry
    {
        $entry = $this->entry;
        $currentType = $entry->type;
        $saveAs = array_get($requestData, 'save_as');

        if ($makeRevision) {
            $revision = $entry->revision()->make();
        }

        if ($currentType == 'draft') {
            if ($saveAs == 'draft') {
                // Do nothing
            } elseif ($saveAs == 'current') {
                $entry->type = 'current';
                $entry->save();
            }
        }

        /**
         * Regular data
         */
        $publishedAt = array_get($requestData, 'published_at');
        $publishedAtCarbon = Carbon::createFromFormat('d.m.Y', $publishedAt)->startOfDay();
        $publishedAtFormatted = $publishedAtCarbon ? $publishedAtCarbon->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');

        $isActive = array_has($requestData, 'is_active');

        $isHomepage = array_has($requestData, 'is_homepage');
        $entry->update([
            'layout'       => array_get($requestData, 'layout'),
            'published_at' => $publishedAtFormatted,

            // Checkboxes user array_has
            'is_active'    => $isActive,
            'is_homepage'  => $isHomepage,
        ]);

        // If this is homepage, then mark other pages as regular ones
        if ($isHomepage) {
            Entry::where('id', '!=', $entry->id)->update([
                'is_homepage' => 0
            ]);
        }

        // Store/update translations
        $entryTranslations = (array)array_get($requestData, 'translations', []);
        $this->updateEntryTranslations($entryTranslations);

        $this->processContentBlocks($requestData);

        // This must be after processContentBlocks
        $this->updateEntryContentTranslation($entry);

        // Hide/show menu items that link to this entry
        $menuItemClass = '\Modules\Admin\Models\MenuItem';
        if (class_exists($menuItemClass)) {
            $slug = '/' . trim($entry->slug, '/');
            app($menuItemClass)->whereHas('translations', function ($subQuery) use ($slug) {
                return $subQuery->whereValue($slug);
            })->update([
                'is_active' => $isActive
            ]);
        }

        return $entry;
    }

    /**
     * @param array $requestData
     */
    private function processContentBlocks(Array $requestData): void
    {
        $entry = $this->entry;

        /**
         * Widgets
         */
        $contentBlocks = json_decode(array_get($requestData, 'widgets', null));
        $contentBlocks = (array)array_map(function ($contentBlock) {
            return (array)$contentBlock;
        }, $contentBlocks);


        foreach ($this->languages as $language) {
            $entry->load('translations');
            $entryTranslation = $entry->translateOrNew($language->iso_code);

            $filteredContentBlocks = array_filter($contentBlocks, function ($contentBlock) use ($language) {
                $locale = array_get($contentBlock, 'locale');

                return $locale == $language->iso_code;
            });

            $this->processEntryTranslation($entryTranslation, $filteredContentBlocks);
        }
    }

    /**
     * @param EntryTranslation $entryTranslation
     * @param array $contentBlocks
     */
    private function processEntryTranslation(EntryTranslation $entryTranslation, Array $contentBlocks): void
    {
        $existingContentBlockIds = $entryTranslation->contentBlocks()->pluck('id')->toArray();
        $receivedContentBlockIds = [];


        foreach ($contentBlocks as $index => $contentBlock) {

            $contentBlockId = array_get($contentBlock, 'contentBlockId');
            $widget = array_get($contentBlock, 'widget');

            if (is_integer($contentBlockId)) {
                // $contentBlockId is real id in DB.

                $receivedContentBlockIds[] = $contentBlockId;

                $existingContentBlock = $entryTranslation->contentBlocks()->find($contentBlockId);
                if (!$existingContentBlock) {
                    continue;
                }

                $existingContentBlock->update([
                    'order' => ($index + 1)
                ]);

                $this->storeNewContentBlock($existingContentBlock, $contentBlock, $index, $contentBlockId);

            } else {
                // $contentBlockId is random string, something like "Mfjxi"


                $newContentBlock = $entryTranslation->contentBlocks()->create([
                    'widget' => $widget,
                    'order'  => ($index + 1),
                    'data'   => []
                ]);


                $this->storeNewContentBlock($newContentBlock, $contentBlock, $index, $contentBlockId);
            }
        }


        // Delete blocks that we didnt receive
        $deletableContentBlockIds = [];
        foreach ($existingContentBlockIds as $existingContentBlockId) {
            if (!in_array($existingContentBlockId, $receivedContentBlockIds)) {
                $deletableContentBlockIds[] = $existingContentBlockId;
            }
        }

        $deletableContentBlocks = $entryTranslation->contentBlocks()->whereIn('id', $deletableContentBlockIds)->get();
        foreach ($deletableContentBlocks as $deletableContentBlock) {
            $key = $deletableContentBlock->widget;
            $config = collect(config('netcore.module-content.widgets'))->where('key', $key)->first();
            $backendWorker = array_get($config, 'backend_worker');
            if ($backendWorker) {
                $backendWorker = new $backendWorker($config);
                $backendWorker->delete($deletableContentBlock); // Delete data in related tables
            }
            $deletableContentBlock->delete();
        }
    }

    /**
     * @param ContentBlock $existingContentBlock
     * @param array $contentBlock
     * @param Int $index
     * @param $contentBlockId
     */
    private function storeNewContentBlock(
        ContentBlock $existingContentBlock,
        Array $contentBlock,
        Int $index,
        $contentBlockId
    ) {
        // Save widgets and their data
        // 1. Put data in content_blocks table
        // 1.1 Put data in additional tables, according to each specific widget

        $locale = array_get($contentBlock, 'locale');
        $entry = $this->entry;
        $entryTranslation = $entry->translateOrNew($locale);

        $key = array_get($contentBlock, 'widget');
        $widget = Widget::where('key', $key)->first();
        $config = $widget->config;
        $backendWorker = array_get($config, 'backend_worker');
        $requestedData = request()->all();
        $contentBlockItems = isset($requestedData['main_fields'][$contentBlockId]) ? $requestedData['main_fields'][$contentBlockId] : [];
        $currentContentBlock = null;

        if ($backendWorker) {
            $backendWorker = new $backendWorker($config);
            $action = $backendWorker->action;

            if ($action == 'update') {
                $frontendData = (array)array_get($contentBlock, 'data', []);

                $frontendData['contentBlockId'] = $contentBlock['contentBlockId'];
                $data = $backendWorker->update($frontendData);


                $existingContentBlock->update([
                    'order'  => ($index + 1),
                    'widget' => $key,
                    'data'   => $data
                ]);


            }

            if ($action == 'recreate') {
                // Delete data in related tables
                $backendWorker->delete($existingContentBlock);
                $existingContentBlock->delete();

                $frontendData = (array)array_get($contentBlock, 'data', []);
                $data = $backendWorker->store($frontendData);

                $contentBlockData = [
                    'order'  => ($index + 1),
                    'widget' => $key,
                    'data'   => $data
                ];


                $existingContentBlock = $entryTranslation->contentBlocks()->create($contentBlockData);
            }

            if ($existingContentBlock) {
                foreach ($contentBlockItems as $field => $value) {
                    $existingField = $existingContentBlock->items->where('key', $field)->first();
                    $contentBlockItemData = [];
                    $contentBlockItemData['key'] = $field;

                    if(is_object($value)) {
                        $contentBlockItemData['value'] = null;
                        $contentBlockItemData['image'] = $value;

                    } else {
                        $contentBlockItemData['value'] = $value;
                    }

                    if ($existingField) {
                        $existingField->update($contentBlockItemData);
                    } else {
                        $existingContentBlock->items()->create($contentBlockItemData);
                    }

                }

            }
        }
    }

    /**
     *
     */
    public function deleteOldContentBlocks()
    {
        $entry = $this->entry;

        foreach ($entry->translations as $entryTranslation) {
            foreach ($entryTranslation->contentBlocks as $contentBlock) {

                $key = $contentBlock->widget;
                $config = collect(config('netcore.module-content.widgets'))->where('key', $key)->first();
                $backendWorker = array_get($config, 'backend_worker');
                $backendWorker = new $backendWorker($config);

                // Delete data in related tables
                $backendWorker->delete($contentBlock);
                $contentBlock->delete();
            }
        }
    }

    /**
     * @param array $entryTranslations
     */
    private function updateEntryTranslations(Array $entryTranslations)
    {
        $entry = $this->entry;

        $mappedEntryTranslations = collect($entryTranslations)->map(function ($translations, $locale) use ($entry) {

            if (strlen(array_get($translations, 'slug')) == 0) {
                $slug = str_slug(array_get($translations, 'title'));
            } else {
                $slug = str_slug(array_get($translations, 'slug'));
            }

            $channelId = $entry->channel_id;
            $translations['slug'] = $this->uniqueSlug($slug, $locale, $channelId);
            $translations['content'] = ''; // Default value

            $translations = array_except($translations, 'meta_tags');

            return $translations;
        })->toArray();

        // Save translations
        $entry->updateTranslations($mappedEntryTranslations);

        // Save meta tags
        $this->updateMetaTags($entryTranslations);
    }

    /**
     * @param array $entryTranslations
     * @return bool
     */
    private function updateMetaTags(Array $entryTranslations)
    {
        $entry = $this->entry;
        $entryTranslationObjects = $entry->translations()->get();

        $configuredMetaTags = collect(config('netcore.module-content.meta_tags', []));
        $newMetaTags = [];

        foreach ($entryTranslations as $locale => $translations) {

            $entryTranslationObject = $entryTranslationObjects->where('locale', $locale)->first();

            if (!$entryTranslationObject) {
                return true;
            }

            $entryTranslationObject->metaTags()->delete();

            $metaTags = (array)array_get($translations, 'meta_tags', []);
            foreach ($metaTags as $nameProperty => $value) {

                $value = $value ?: ''; // Value cannot be null

                if (!$value) {
                    continue;
                }

                $isProperty = $configuredMetaTags->where('property', $nameProperty)->count();
                $isName = $configuredMetaTags->where('name', $nameProperty)->count();
                if ($isProperty) {
                    $newMetaTags[] = [
                        'entry_translation_id' => $entryTranslationObject->id,
                        'name'                 => '',
                        'property'             => $nameProperty,
                        'value'                => $value,
                    ];
                }

                if ($isName) {
                    $newMetaTags[] = [
                        'entry_translation_id' => $entryTranslationObject->id,
                        'name'                 => $nameProperty,
                        'property'             => '',
                        'value'                => $value,
                    ];
                }
            }
        }

        $table = app()->make(MetaTag::class)->getTable();
        DB::table($table)->insert($newMetaTags);

        return true;
    }

    /**
     * @param Entry $entry
     */
    private function updateEntryContentTranslation(Entry $entry)
    {
        foreach ($entry->translations as $entryTranslation) {
            $contentBlocks = $entryTranslation->contentBlocks()->where('data', 'LIKE', '%html_block_id%')->get();

            $content = '';

            foreach ($contentBlocks as $contentBlock) {

                $htmlBlockId = array_get($contentBlock->data, 'html_block_id', null);
                if (!$htmlBlockId) {
                    continue;
                }

                $htmlBlock = HtmlBlock::find($htmlBlockId);

                if (!$htmlBlock) {
                    continue;
                }

                foreach ($htmlBlock->translations as $translation) {
                    if (!isset($entryTranslations[$translation->locale]['content'])) {
                        $entryTranslations[$translation->locale]['content'] = '';
                    }

                    $content .= $translation->content;
                }
            }

            $entryTranslation->update(compact('content'));
        }
    }

    /**
     * @param $originalSlug
     * @param $locale
     * @param $channelId
     * @return mixed
     */
    private function uniqueSlugCount($originalSlug, $locale, $channelId)
    {
        return Entry::join('netcore_content__entry_translations', 'netcore_content__entries.id', '=',
            'netcore_content__entry_translations.entry_id')
            ->where('netcore_content__entry_translations.slug', $originalSlug)
            ->where('netcore_content__entry_translations.locale', $locale)
            ->where('netcore_content__entries.channel_id', $channelId)
            ->where('netcore_content__entries.type', '=', 'active')
            ->where('netcore_content__entries.id', '!=', $this->entry->id)
            ->count();
    }

    /**
     * @param $originalSlug
     * @param $locale
     * @param $channelId
     * @return string
     */
    private function uniqueSlug($originalSlug, $locale, $channelId)
    {
        $slug = $originalSlug;

        $count = $this->uniqueSlugCount($originalSlug, $locale, $channelId);
        if ($count) {
            $count++; // This will generate test and test-2, not test and test-1
            $slug = $originalSlug . '-' . $count;
        }

        while ($this->uniqueSlugCount($slug, $locale, $channelId) > 0) {
            $count++;
            $slug = $originalSlug . '-' . $count;
        }

        return $slug;
    }

}
