<?php

namespace Modules\Content\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Content\Models\Channel;
use Modules\Content\Models\Entry;
use Modules\Content\Models\HtmlBlock;
use Modules\Content\Models\Section;
use Netcore\Translator\Models\Language;
use Netcore\Translator\Helpers\TransHelper;

class ExampleDataTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('netcore_content__entries')->delete();
        DB::table('netcore_content__channels')->delete();
        DB::table('netcore_content__sections')->delete();

        $sections = [
            'Blogs' => [
                [
                    'type'      => 'channel',
                    'name'      => 'News',
                    'is_active' => 1,
                    'entries'   => [
                        [
                            'name'      => 'First news entry',
                            'is_active' => 1,
                        ],
                        [
                            'name'      => 'Second news entry',
                            'is_active' => 1,
                        ],
                        [
                            'name'      => 'Third news entry',
                            'is_active' => 1,
                        ]
                    ]
                ],
                [
                    'type'      => 'channel',
                    'name'      => 'Reports',
                    'is_active' => 1,
                    'entries'   => [
                        [
                            'name'      => 'First report',
                            'is_active' => 1,
                        ],
                        [
                            'name'      => 'Second report',
                            'is_active' => 1,
                        ],
                        [
                            'name'      => 'Third report',
                            'is_active' => 1,
                        ]
                    ]
                ],
                [
                    'type'      => 'channel',
                    'name'      => 'Blog',
                    'is_active' => 1,
                    'entries'   => [
                        [
                            'name'      => 'First blogpost',
                            'is_active' => 1,
                        ],
                        [
                            'name'      => 'Second blogpost',
                            'is_active' => 1,
                        ],
                        [
                            'name'      => 'Third blogpost',
                            'is_active' => 1,
                        ]
                    ]
                ],
            ],


            'Static pages' => [
                [
                    'type'      => 'entry',
                    'name'      => 'Homepage',
                    'is_active' => 1,
                ],
                [
                    'type'      => 'entry',
                    'name'      => 'About us',
                    'is_active' => 1,
                ],
                [
                    'type'      => 'entry',
                    'name'      => 'Contacts',
                    'is_active' => 1,
                ],
            ]
        ];

        $sectionOrder = 1;

        foreach ($sections as $sectionName => $sectionData) {

            $section = Section::updateOrCreate([
                'name' => $sectionName
            ], [
                'order' => $sectionOrder
            ]);

            $sectionOrder++;

            foreach ($sectionData as $item) {

                $itemType = array_get($item, 'type');

                if ($itemType == 'channel') {
                    $this->createChannel($item, $section);
                }

                if ($itemType == 'entry') {
                    $this->createEntry($item, $section);
                }
            }
        }
    }

    /**
     * @param $item
     * @param $section
     * @return mixed
     */
    private function createChannel($item, $section)
    {

        $itemName = array_get($item, 'name');

        // Basic data
        $channelData = array_except($item, ['name', 'type', 'translations', 'entries']);
        $channelData['section_id'] = $section->id;

        $channel = Channel::create($channelData);

        // Translations
        $channelTranslations = $this->translateKeyValuePairsToAllLocales([
            'slug' => $itemName,
            'name' => $itemName
        ]);

        $channel->updateTranslations($channelTranslations);

        // Entries
        $entries = array_get($item, 'entries', []);
        foreach ($entries as $item) {
            $item['channel_id'] = $channel->id;
            $this->createEntry($item, $section);
        }

        return $channel;
    }

    /**
     * @param $item
     * @param $section
     * @return mixed
     */
    private function createEntry($item, $section)
    {
        $itemName = array_get($item, 'name');

        $entryData = array_except($item, ['type', 'name', 'translations']);
        $entryData['section_id'] = $section->id;

        $entry = Entry::forceCreate($entryData);

        $exampleContent = 'We have been operating since 2009, and we already employ more than 250 professionals whose job it is to ensure everyone easily accessible and understandable daily financial services. At present, we have introduced and continue to develop advanced process management and automation systems, allowing us to significantly accelerate the examination of applications for customers and a reduction in lending-related financial risks, while providing fast, reliable and accurate lending services to our customers. In order to successfully serve all customers according to their needs and ensure high customer service standards, we manage the largest branch network in Latvia, which also offer Lombard and Western Union money transfer services.';

        $entryTranslations = $this->translateKeyValuePairsToAllLocales([
            'slug'    => $itemName,
            'title'   => $itemName,
            'content' => $exampleContent
        ]);

        $entry->updateTranslations($entryTranslations);

        $htmlBlock = HtmlBlock::create([]);
        $htmlBlock->storeTranslations($this->translateKeyValuePairsToAllLocales([
            'content' => $exampleContent
        ]));

        $entry->contentBlocks()->create([
            'order'  => 1,
            'widget' => 'simple_text',
            'data'   => [
                'html_block_id' => $htmlBlock->id
            ]
        ]);

        return $entry;
    }

    /**
     * @param $keyValuePairs
     * @return array
     */
    private function translateKeyValuePairsToAllLocales($keyValuePairs)
    {
        $locales = collect(TransHelper::getAllLanguages())->pluck('iso_code');

        $result = [];

        foreach ($keyValuePairs as $key => $value) {
            foreach ($locales as $locale) {

                $localizedValue = $value;
                if ($key == 'slug') {
                    $localizedValue = str_slug($value);
                }

                $result[$locale][$key] = $localizedValue;
            }
        }

        return $result;
    }
}
