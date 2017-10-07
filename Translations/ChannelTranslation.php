<?php

namespace Modules\Content\Translations;

use Illuminate\Database\Eloquent\Model;

class ChannelTranslation extends Model
{

    /**
     * @var string
     */
    protected $table = 'netcore_content__channel_translations';

    /**
     * @var array
     */
    protected $fillable = [
        'slug',
        'locale' // This is very important
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

}
