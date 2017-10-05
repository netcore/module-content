<?php

namespace Modules\Content\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Content\Traits\SyncTranslations;
use Modules\Content\Translations\PageTranslation;

class Page extends Model
{
    
    use Translatable, SyncTranslations;

    //@TODO: varbūt šis jāiekļauj Admin modulī
    use \Modules\Crud\Traits\CrudifyModel;
    
    /**
     * @var string
     */
    protected $table = 'netcore__pages';

    /**
     * @var array
     */
    protected $fillable = [
        'is_active'
    ];

    /**
     * @var string
     */
    public $translationModel = PageTranslation::class;

    /**
     * @var array
     */
    public $translatedAttributes = [
        'title',
        'slug',
        'content'
    ];
}
