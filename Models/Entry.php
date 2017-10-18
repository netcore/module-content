<?php

namespace Modules\Content\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Content\PassThroughs\Entry\Storage;
use Modules\Admin\Traits\SyncTranslations;
use Modules\Content\Translations\EntryTranslation;
use Modules\Crud\Traits\CRUDModel;

class Entry extends Model
{
    
    use Translatable, SyncTranslations;

    use CRUDModel;
    
    /**
     * @var string
     */
    protected $table = 'netcore_content__entries';

    /**
     * @var array
     */
    protected $fillable = [
        'channel_id',
        'section_id',
        'is_active',
        'layout'
    ];

    /**
     * @var string
     */
    public $translationModel = EntryTranslation::class;

    /**
     * @var array
     */
    public $translatedAttributes = [
        'title',
        'slug',
        'content'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function contentBlocks()
    {
        return $this->morphMany(ContentBlock::class, 'contentable');
    }

    /**
     * @return Storage
     */
    public function storage()
    {
        return new Storage($this);
    }
}
