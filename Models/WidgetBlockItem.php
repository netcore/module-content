<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Traits\BootStapler;
use Codesleeve\Stapler\ORM\EloquentTrait;
use Codesleeve\Stapler\ORM\StaplerableInterface;

class WidgetBlockItem extends Model
{

    /**
     * @var string
     */
    protected $table = 'netcore_content__widget_block_items';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'order',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function widgetBlock()
    {
        return $this->belongsTo(WidgetBlock::class);
    }

    /**
     * @return mixed
     */
    public function fields()
    {
        return $this->hasMany(WidgetBlockItemField::class);
    }

    /**
     * @param String $key
     * @return mixed
     */
    public function getField(String $key)
    {
        $field = $this->fields->where('key', $key)->first();

        return $field ? $field->value : '';
    }

    /**
     * @return mixed
     */
    public function getFieldAttribute()
    {
        return $this->fields->mapWithKeys(function ($item) {
            if($item->image_file_name) {
                return [
                    $item->key => $item->image->url()
                ];
            }
            return [
                $item->key => $item->value
            ];
        });
    }

    /**
     * @param String $key
     * @return mixed
     */
    public function getStaplerObj(String $key)
    {
        $field = $this->fields->where('key', $key)->first();

        return $field ? $field->image : null;
    }
}
