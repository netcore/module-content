<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    
    //@TODO: varbūt šis jāiekļauj Admin modulī
    use \Modules\Crud\Traits\CrudifyModel;
    
    /**
     * @var string
     */
    protected $table = 'netcore_content__sections';

    /**
     * @var array
     */
    protected $fillable = [
        'name'
    ];

}
