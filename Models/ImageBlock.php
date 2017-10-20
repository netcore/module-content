<?php

namespace Modules\Content\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Traits\SyncTranslations;
use Modules\Content\Translations\ImageBlockTranslation;

class ImageBlock extends Model
{

    // @TODO stapler
    use Translatable, SyncTranslations;

    /**
     * @var string
     */
    protected $table = 'netcore_content__image_blocks';

    /**
     * @var array
     */
    protected $fillable = [

    ];

    /**
     * @var string
     */
    public $translationModel = ImageBlockTranslation::class;

    /**
     * @var array
     */
    public $translatedAttributes = [
        'title'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(ImageBlockItem::class);
    }
}
