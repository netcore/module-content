<?php

namespace Modules\Content\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Traits\SyncTranslations;
use Modules\Content\Translations\HtmlBlockTranslation;

class HtmlBlock extends Model
{
    
    use Translatable, SyncTranslations;

    /**
     * @var string
     */
    protected $table = 'netcore_content__html_blocks';
    
    /**
     * @var array
     */
    protected $fillable = [
        
    ];
    
    /**
     * @var string
     */
    public $translationModel = HtmlBlockTranslation::class;
    
    /**
     * @var array
     */
    public $translatedAttributes = [
        'content',
        'json'
    ];

    /**
     * @return array
     */
    public function getJsonDecodedAttribute()
    {
        return (array) json_decode($this->json);
    }
}
