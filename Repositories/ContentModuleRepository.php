<?php

namespace Modules\Content\Repositories;

use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Modules\Content\Models\Channel;
use Modules\Content\Models\Entry;
use Modules\Content\Models\MetaTag;
use Modules\Content\Models\WidgetBlock;
use Modules\Content\Modules\Field;
use Modules\Content\Modules\Widget;
use Modules\Content\Traits\ChannelSeederTrait;

class ContentModuleRepository
{

    use ChannelSeederTrait;

    private $channel = null;
    private $filterByGlobal = null;

    /**
     * @param $pages
     */
    public function storePages($pages)
    {
        foreach ($pages as $channel => $items) {
            foreach ($items as $item) {
                $this->createEntry($channel, $item);
            }
        }
        cache()->forget('content_widgets');
        cache_content_entries();
    }

    /**
     * @param $pages
     */
    public function pagesSeeder($pages)
    {
        $this->storePages($pages);
    }

    /**
     * @param $slug
     * @return $this
     */
    public function channel($slug)
    {
        $this->channel = Channel::with(['entries.globalFields'])->whereHas('translations', function ($q) use ($slug) {
            $q->whereSlug($slug);
        })->first();

        return $this;
    }

    /**
     * @return null
     */
    public function entries()
    {
        if ($this->channel) {
            return $this->channel->entries()->where('is_active', 1)->whereHas('globalFields', function ($q) {
                $q->where('key', $this->filterByGlobal[0])->where('value', $this->filterByGlobal[1], $this->filterByGlobal[2]);
            })->get();
        }

        return null;
    }

    /**
     * @param $conditions
     * @return null
     */
    public function filterByGlobal($conditions)
    {
        if (count($conditions)) {
            $this->filterByGlobal = $conditions;

            return $this;
        }

        return null;
    }

    /**
     * @param null $key
     * @return null
     */
    public function getUrl($key = null)
    {
        $entries = cache()->rememberForever('content_entries', function () {
            return Entry::get();
        });

        if ($key) {
            $entry = $entries->where('key', $key)->first();
            if ($entry) {
                return url($entry->slug);
            }
        }

        return null;
    }

    /**
     * @param $widgets
     */
    public function storeWidgets($widgets)
    {
        foreach ($widgets as $title => $widget) {
            $key = str_slug($title, '_');
            $createdWidget = Widget::firstOrCreate([
                'key' => $key
            ], [
                'key'        => $key,
                'title'      => $title,
                'is_enabled' => $widget['is_enabled'],
                'data'       => json_encode(isset($widget['options']) ? $widget['options'] : [])
            ]);

            if (isset($widget['widget_fields'])) {
                $widgetFields = $this->storeField($widget['widget_fields'], true);
                $createdWidget->fields()->attach($widgetFields);

            }
            if (isset($widget['item_fields'])) {
                $itemFields = $this->storeField($widget['item_fields']);
                $createdWidget->fields()->attach($itemFields);

            }
        }

        cache()->forget('content_widgets');
    }

    /**
     * @param $channels
     */
    public function storeChannels($channels)
    {
        foreach ($channels as $channelData) {
            $channel = Channel::create(array_only($channelData, ['layout', 'is_active', 'allow_attachments']));

            // Translations
            $channelTranslations = $this->translateKeyValuePairsToAllLocales(array_except($channelData,
                ['layout', 'is_active', 'allow_attachments', 'fields']));

            $channel->updateTranslations($channelTranslations);

            if (isset($channelData['fields'])) {
                $fields = $this->storeField($channelData['fields']);

                $channel->fields()->sync($fields);


            }
        }
        cache()->forget('content_widgets');
    }

    /**
     * @param $fields
     * @param bool $isMain
     * @return array
     */
    private function storeField($fields, $isMain = false)
    {
        $fieldsIds = [];
        foreach ($fields as $fieldName => $field) {
            $field['key'] = str_slug($fieldName, '_');
            $field['title'] = $fieldName;
            $field['is_main'] = $isMain;
            $field['is_global'] = $field['is_global'] ?? 0;
            $field['data'] = isset($field['options']) ? json_encode($field['options']) : json_encode([]);

            $createdField = Field::firstOrCreate(array_except($field, ['options']));
            $fieldsIds[] = $createdField->id;
        }
        cache()->forget('content_widgets');

        return $fieldsIds;
    }

    /**
     * @param $channelSlug
     * @param $item
     * @return Entry
     */
    private function createEntry($channelSlug, $item): Entry
    {
        $channel = Channel::wherehas('translations', function ($q) use ($channelSlug) {
            $q->where('slug', $channelSlug);
        })->first();

        $itemName = array_get($item, 'name');

        $entryData = array_except($item, ['type', 'name', 'translations', 'data']);
        $entryData['published_at'] = date('Y-m-d') . ' 00:00:00';
        $entryData['key'] = isset($item['key']) ? $item['key'] : str_slug($item['name']);
        $entryData['channel_id'] = isset($channel) ? $channel->id : null;

        $entry = Entry::forceCreate($entryData);

        // seed attachments
        if (isset($item['data']['attachments'])) {
            foreach ($item['data']['attachments'] as $attachment) {
                if (file_exists($attachment)) {
                    $image = new \Symfony\Component\HttpFoundation\File\File($attachment);
                    $newImage = str_replace('.' . $image->getExtension(), '_copy.' . $image->getExtension(), $image);
                    copy($image, $newImage);

                    $newImage = new \Symfony\Component\HttpFoundation\File\File($newImage);

                    $entry->attachments()->create([
                        'image' => $newImage
                    ]);
                }
            }
        }

        $entryTranslations = $this->translateKeyValuePairsToAllLocales([
            'slug'    => $itemName,
            'title'   => $itemName,
            'content' => '',
        ]);

        $entry->updateTranslations($entryTranslations);

        $this->seedEntryData($entry, $item['data']);

        if (isset($item['data']['entry_data'])) {
            foreach ($item['data']['entry_data'] as $field => $value) {
                $fieldExists = Field::where('key', $field)->whereIsGlobal(1)->first();
                if ($fieldExists) {
                    $entry->globalFields()->updateOrCreate(['key' => $field], [
                        'key'   => $field,
                        'value' => $value,
                    ]);
                }
            }
        }

        foreach ($entry->translations()->get() as $entryTranslationObj) {
            if (isset($item['data']['entry_data'])) {
                foreach ($item['data']['entry_data'] as $field => $value) {
                    $entryTranslationObj->fields()->create([
                        'key'   => $field,
                        'value' => $value
                    ]);
                }
            }
            $metaTags = [
                [
                    'property' => 'og:type',
                    'name'     => '',
                    'value'    => 'Page'
                ],
                [
                    'property' => 'og:title',
                    'name'     => '',
                    'value'    => ''
                ],
                [
                    'property' => 'og:url',
                    'name'     => '',
                    'value'    => ''
                ],
                [
                    'property' => 'og:description',
                    'name'     => '',
                    'value'    => ''
                ],
                [
                    'property' => 'og:image',
                    'name'     => '',
                    'value'    => '',
                ],
                [
                    'property' => 'twitter:card',
                    'name'     => '',
                    'value'    => ''
                ],
                [
                    'property' => 'twitter:site',
                    'name'     => '',
                    'value'    => ''
                ],
                [
                    'property' => 'twitter:title',
                    'name'     => '',
                    'value'    => ''
                ],
                [
                    'property' => 'twitter:description',
                    'name'     => '',
                    'value'    => ''
                ],
                [
                    'property' => 'twitter:image',
                    'name'     => '',
                    'value'    => '',
                ],
                [
                    'property' => '',
                    'name'     => 'keywords',
                    'value'    => ''
                ],
                [
                    'property' => '',
                    'name'     => 'description',
                    'value'    => ''
                ],
            ];

            $mapped = array_map(function ($metaTag) use ($entryTranslationObj) {
                $metaTag['entry_translation_id'] = $entryTranslationObj->id;

                return $metaTag;
            }, $metaTags);

            $table = app()->make(MetaTag::class)->getTable();
            DB::table($table)->insert($mapped);
        }

        return $entry;
    }

    /**
     * @param $entry
     * @param $data
     */
    private function seedEntryData($entry, $data)
    {
        $widgets = $data['widgets'];

        foreach ($widgets as $widgetData) {
            $widget = str_slug($widgetData['widget'], '_');
            $items = isset($widgetData['items']) ? $widgetData['items'] : [];

            $order = 0;
            foreach ($entry->translations()->get() as $entryTranslation) {
                $widgetBlock = WidgetBlock::create([]);

                foreach ($items as $i => $item) {
                    $widgetBlockItem = $widgetBlock->items()->create(['order' => $i]);
                    foreach ($item as $key => $field) {
                        if (file_exists($field)) {
                            $widgetObject = widgets()->where('key', $widget)->first();
                            $fieldObj = $widgetObject->fields->where('key', $key)->first();
                            $fieldOptions = json_decode($fieldObj->data);


                            $image = new \Symfony\Component\HttpFoundation\File\File($field);
                            $newImage = str_replace('.' . $image->getExtension(), '_copy.' . $image->getExtension(),
                                $image);


                            if (isset($fieldOptions->width) && isset($fieldOptions->height)) {
                                Image::make($image->getRealPath())->resize($fieldOptions->width,
                                    $fieldOptions->height)->save($newImage);
                            } else {
                                copy($image, $newImage);
                            }
                            $newImage = new \Symfony\Component\HttpFoundation\File\File($newImage);


                            $widgetBlockItem->fields()->create([
                                'key'   => $key,
                                'value' => null,
                                'image' => $newImage
                            ]);
                        } else {
                            $widgetBlockItem->fields()->create([
                                'key'   => $key,
                                'value' => $field,
                            ]);
                        }
                    }

                }

                $contentBlock = $entryTranslation->contentBlocks()->create([
                    'order'  => $order,
                    'widget' => $widget,
                    'data'   => [
                        'widget_block_id' => $widgetBlock->id,
                    ],
                ]);

                if (isset($widgetData['main_items'])) {
                    foreach ($widgetData['main_items'] as $mainItemKey => $mainItemValue) {
                        if (file_exists($mainItemValue)) {
                            $image = new \Symfony\Component\HttpFoundation\File\File($mainItemValue);
                            $newImage = str_replace('.' . $image->getExtension(), '_copy.' . $image->getExtension(),
                                $image);
                            copy($image, $newImage);

                            $newImage = new \Symfony\Component\HttpFoundation\File\File($newImage);

                            $contentBlock->items()->create([
                                'key'   => $mainItemKey,
                                'value' => null,
                                'image' => $newImage
                            ]);
                        } else {
                            $contentBlock->items()->create([
                                'key'   => $mainItemKey,
                                'value' => $mainItemValue,
                            ]);
                        }
                    }
                }

                $order++;
            }
        }
    }

}