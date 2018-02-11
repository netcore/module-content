<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Content\PassThroughs\ContentBlock\Compose;
use Modules\Content\PassThroughs\ContentBlock\Config;
use Modules\Crud\Traits\CRUDModel;

class ContentBlock extends Model
{

    use CRUDModel;

    /**
     * @var string
     */
    protected $table = 'netcore_content__content_blocks';

    /**
     * @var array
     */
    protected $fillable = [
        'widget',
        'data',
        'order',
        'contentable_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function contentable()
    {
        return $this->morphTo('contentable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(ContentBlockItem::class);
    }

    /**
     * @return Config
     */
    public function getConfigAttribute()
    {
        return $this->config();
    }

    /**
     * @param String $key
     * @return mixed
     */
    public function getField(String $key)
    {
        $field = $this->items->where('key', $key)->first();

        return $field ? $field->value : '';
    }

    /**
     * @param String $key
     * @return mixed
     */
    public function getStaplerObj(String $key)
    {
        $field = $this->items->where('key', $key)->first();

        return $field ? $field->image : null;
    }

    /**
     * @return Config
     */
    public function config()
    {
        return new Config($this);
    }

    /**
     * @return Compose
     */
    public function compose()
    {
        return new Compose($this);
    }

}
