<?php

namespace Modules\Content\Repositories;

use Illuminate\Support\Facades\DB;
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

    public function pagesSeeder($pages)
    {
        foreach ($pages as $channel => $items) {
            foreach ($items as $item) {
                $this->createEntry($channel, $item);
            }
        }
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

            $createdField = Field::firstOrCreate($field);
            $fieldsIds[] = $createdField->id;
        }

        return $fieldsIds;
    }

    private function createEntry($channelSlug, $item): Entry
    {
        $channel = Channel::wherehas('translations', function ($q) use ($channelSlug) {
            $q->where('slug', $channelSlug);
        })->first();

        $itemName = array_get($item, 'name');

        $entryData = array_except($item, ['type', 'name', 'translations', 'data']);
        $entryData['published_at'] = date('Y-m-d') . ' 00:00:00';
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

    private function seedEntryData($entry, $data)
    {
        $widgets = $data['widgets'];

        foreach ($widgets as $widgetData) {
            $widget = str_slug($widgetData['widget'], '_');
            $items = $widgetData['items'];

            $order = 0;
            foreach ($entry->translations()->get() as $entryTranslation) {
                $widgetBlock = WidgetBlock::create([]);

                foreach ($items as $i => $item) {
                    $widgetBlockItem = $widgetBlock->items()->create(['order' => $i]);
                    foreach ($item as $key => $field) {
                        if (file_exists($field)) {
                            $image = new \Symfony\Component\HttpFoundation\File\File($field);
                            $newImage = str_replace('.' . $image->getExtension(), '_copy.' . $image->getExtension(),
                                $image);
                            copy($image, $newImage);

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