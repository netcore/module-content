<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Content\PassThroughs\ContentBlock\Compose;
use Modules\Content\PassThroughs\ContentBlock\Config;

class ContentBlock extends Model
{
    
    //@TODO: varbūt šis jāiekļauj Admin modulī
    use \Modules\Crud\Traits\CrudifyModel;
    
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

    public function compose()
    {
        return new Compose($this);
    }

}
