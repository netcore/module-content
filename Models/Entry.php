<?php

namespace Modules\Content\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Content\Traits\SyncTranslations;
use Modules\Content\Translations\EntryTranslation;

class Entry extends Model
{
    
    use Translatable, SyncTranslations;

    //@TODO: varbūt šis jāiekļauj Admin modulī
    use \Modules\Crud\Traits\CrudifyModel;
    
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
        'name',
        'is_active'
    ];

    /**
     * @var string
     */
    public $translationModel = EntryTranslation::class;

    /**
     * @var array
     */
    public $translatedAttributes = [
        'slug'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function contentBlocks()
    {
        return $this->morphMany(ContentBlock::class, 'contentable');
    }
}
