<?php

namespace Modules\Content\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Content\Traits\SyncTranslations;
use Modules\Content\Translations\ImageBlockTranslation;

class ImageBlockItem extends Model
{

    use Translatable, SyncTranslations;

    /**
     * @var string
     */
    protected $table = 'netcore_content__image_block_items';
    
    /**
     * @var array
     */
    protected $fillable = [
        'image',
        'order'
    ];
    
    /**
     * @var string
     */
    public $translationModel = ImageBlockTranslation::class;
    
    /**
     * @var array
     */
    public $translatedAttributes = [
        'title',
        'subtitle',
        'content',
        'json'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function imageBlock()
    {
        return $this->belongsTo(ImageBlock::class);
    }
}
