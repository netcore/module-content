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
        'link',
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
