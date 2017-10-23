<?php

namespace Modules\Content\Translations;

use Illuminate\Database\Eloquent\Model;

class HtmlBlockTranslation extends Model
{

    /**
     * @var string
     */
    protected $table = 'netcore_content__html_block_translations';

    /**
     * @var array
     */
    protected $fillable = [
        'content',
        'json',
        'locale' // This is very important
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     *
     * Make sure data is casted to JSON if its array
     * NOTE - we cannot use Laravel's $casts array, because
     * we want string to be returned when we do $item->json
     * That's because trans_model() function is typehinted to
     * only return String
     *
     * @param $attribute
     */
    public function setJsonAttribute($attribute)
    {
        if(is_array($attribute)) {
            $attribute = json_encode($attribute);
        }

        $this->attributes['json'] = $attribute;
    }
}
