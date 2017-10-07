<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Model;

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
}
