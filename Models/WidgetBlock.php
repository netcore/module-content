<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetBlock extends Model
{

    /**
     * @var string
     */
    protected $table = 'netcore_content__widget_blocks';

    /**
     * @var array
     */
    protected $fillable = [

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fields()
    {
        return $this->hasMany(WidgetBlockField::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(WidgetBlockItem::class);
    }
}
