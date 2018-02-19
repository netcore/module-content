<?php

namespace Modules\Content\Modules;

use Illuminate\Database\Eloquent\Model;
use Netcore\Translator\Helpers\TransHelper;

class Widget extends Model
{

    private static $staticFieldsCache = [];

    /**
     * @var string
     */
    protected $table = 'netcore_content__widgets';

    /**
     * @var array
     */
    protected $fillable = ['title', 'key', 'is_enabled', 'data'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fields()
    {
        return $this->belongsToMany(Field::class, 'netcore_content__widget_field', 'widget_id', 'field_id');
    }

    public function getOptionsAttribute()
    {
        return json_decode($this->data);
    }

    /**
     *
     */
    public function getConfigAttribute()
    {
        $fields = [];
        $mainFields = [];
        foreach ($this->fields->where('is_main', 0) as $field) {
            $fields[$field->key] = [
                'type'  => $field->type,
                'label' => $field->title,
            ];
        }

        foreach ($this->fields->where('is_main', 1) as $field) {
            $mainFields[$field->key] = [
                'type'  => $field->type,
                'label' => $field->title,
                'options' => (array) json_decode($field->data)
            ];
        }

        $widgetData = [
            'name'                => $this->title,
            'key'                 => $this->key,
            'frontend_template'   => 'widgets.' . $this->key . '.frontend',
            'backend_template'    => 'content::module_content.widgets.widget_blocks.backend',
            'backend_with_border' => false,
            "backend_javascript"  => "widget_blocks.js",
            "javascript_key"      => "widget_blocks",
            "backend_css"         => "widget_blocks.css",
            'max_items_count'     => 0,
            "backend_worker"      => \Modules\Content\Widgets\BackendWorkers\WidgetBlock::class,
            'fields'              => $fields,
            'main_fields'         => $mainFields
        ];

        if($this->options) {
            foreach($this->options as $index => $value) {
                $widgetData[$index] = $value;
            }
        }

        return $widgetData;
    }
}
