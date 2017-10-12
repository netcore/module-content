<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Crud\Traits\CRUDModel;

class Section extends Model
{
    
    use CRUDModel;
    
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
