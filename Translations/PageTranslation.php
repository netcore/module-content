<?php

namespace Modules\Content\Translations;

use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'locale' // This is very important
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

}
