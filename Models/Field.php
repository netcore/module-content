<?php

namespace Modules\Content\Modules;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    /**
     * @var string
     */
    protected $table = 'netcore_content__fields';

    /**
     * @var array
     */
    protected $fillable = ['title', 'key', 'type', 'data', 'is_main', 'is_global'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function widgets()
    {
        return $this->belongsToMany(Widget::class, 'netcore_content__widget_field');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeWithoutMain($query)
    {
        return $query->where('is_main', 0);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeWithMain($query)
    {
        return $query->where('is_main', 1);
    }
}
