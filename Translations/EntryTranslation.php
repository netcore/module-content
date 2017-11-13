<?php

namespace Modules\Content\Translations;

use Illuminate\Database\Eloquent\Model;
use Modules\Content\Models\ContentBlock;

class EntryTranslation extends Model
{


    /**
     * @var string
     */
    protected $table = 'netcore_content__entry_translations';

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'locale', // This is very important
        'content'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function contentBlocks()
    {
        return $this->morphMany(ContentBlock::class, 'contentable');
    }
}
