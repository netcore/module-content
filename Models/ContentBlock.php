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
        'order'
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
     * @return Config
     */
    public function getConfigAttribute()
    {
        return $this->config();
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
