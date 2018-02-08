<?php

namespace Modules\Content\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Content\Models\Channel;
use Modules\Content\Modules\Field;
use Modules\Content\Modules\Widget;
use Modules\Content\Traits\ChannelSeederTrait;

class TestWidgetTableSeeder extends Seeder
{

    use ChannelSeederTrait;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->fieldsSeeder();

        $this->widgetsSeeder();

        $this->channelsSeeder(); // articles, faq channel
    }

    private function fieldsSeeder()
    {
        $fields = [
            'Title'              => [
                'type'    => 'text',
                'is_main' => 0
            ],
            'Text'               => [
                'type'    => 'textarea',
                'is_main' => 0
            ],
            'Description'        => [
                'type'    => 'textarea',
                'is_main' => 0
            ],
            'Image'              => [
                'type'    => 'file',
                'is_main' => 0
            ],
            'Widget title'       => [
                'type'    => 'text',
                'is_main' => 1
            ],
            'Widget description' => [
                'type'    => 'textarea',
                'is_main' => 1
            ],
            'Widget background' => [
                'type'    => 'file',
                'is_main' => 1
            ],
        ];

        foreach ($fields as $title => $field) {
            $field['key'] = str_slug($title, '_');
            $field['title'] = $title;

            Field::firstOrCreate(['key' => $field['key']], $field);
        }
    }

    /**
     *
     */
    private function widgetsSeeder()
    {
        $widgets = [
            'Title with text widget' => [
                'is_enabled' => 1,
                'fields'     => ['title', 'text', 'image', 'widget_title', 'widget_description', 'widget_background']
            ]
        ];

        foreach ($widgets as $title => $widget) {
            $key = str_slug($title, '_');
            $w = Widget::firstOrCreate([
                'key' => $key
            ], [
                'key'        => $key,
                'title'      => $title,
                'is_enabled' => $widget['is_enabled'],
            ]);

            $fields = Field::whereIn('key', $widget['fields'])->pluck('id')->toArray();

            $w->fields()->sync($fields);
        }
    }

    /**
     *
     */
    private function channelsSeeder()
    {
        $channels = [
            [
                'slug' => 'articles',
                'name' => 'Articles',
            ],
            [
                'slug' => 'faq',
                'name' => 'FAQ',
            ],
        ];

        foreach ($channels as $channelData) {
            $channel = Channel::create([
                'layout'            => 'layouts.main',
                'is_active'         => 1,
                'allow_attachments' => ($channelData['name'] == 'Articles' ? 1 : 0)
            ]);

            // Translations
            $channelTranslations = $this->translateKeyValuePairsToAllLocales($channelData);

            $channel->updateTranslations($channelTranslations);

            $this->channelFieldsSeeder($channel);
        }

    }

    /**
     * @param $channel
     */
    private function channelFieldsSeeder($channel)
    {
        $fieldList = [
            'description',
        ];

        $fields = Field::whereIn('key', $fieldList)->pluck('id')->toArray();

        $channel->fields()->sync($fields);
    }
}
