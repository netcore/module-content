<?php

namespace Modules\Content\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Models\Menu;
use Netcore\Translator\Helpers\TransHelper;

class MenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $menus = [
            'leftAdminMenu' => [
                [
                    'name'            => 'Content',
                    'icon'            => 'ion-ios-paper',
                    'type'            => 'route',
                    'is_active'       => 1,
                    'value'           => 'content::content.index',
                    'active_resolver' => 'content::content.*,content::entries.*,content::channels.*',
                    'module'          => 'Content',
                    'parameters'      => json_encode([])
                ]
            ]
        ];

        foreach ($menus as $name => $items) {
            $menu = Menu::firstOrCreate([
                'name' => $name
            ]);

            foreach ($items as $item) {
                $row = $menu->items()->firstOrCreate(array_except($item, ['name', 'value', 'parameters']));

                $translations = [];
                foreach (TransHelper::getAllLanguages() as $language) {
                    $translations[$language->iso_code] = [
                        'name'       => $item['name'],
                        'value'      => $item['value'],
                        'parameters' => $item['parameters']
                    ];
                }
                $row->updateTranslations($translations);
            }
        }
    }
}
