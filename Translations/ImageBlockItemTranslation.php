<?php

namespace Modules\Content\Translations;

use Illuminate\Database\Eloquent\Model;

class ImageBlockItemTranslation extends Model
{

    /**
     * @var string
     */
    protected $table = 'netcore_content__image_block_item_translations';

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'subtitle',
        'content',
        'json',
        'locale' // This is very important
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

}
