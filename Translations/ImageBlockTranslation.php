<?php

namespace Modules\Content\Translations;

use Illuminate\Database\Eloquent\Model;

class ImageBlockTranslation extends Model
{

    /**
     * @var string
     */
    protected $table = 'netcore_content__image_block_translations';

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'locale' // This is very important
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

}
