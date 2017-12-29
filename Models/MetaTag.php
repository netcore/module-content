<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Content\Translations\EntryTranslation;

class MetaTag extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'netcore_content__meta_tags';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'entry_translation_id',
        'name',
        'property',
        'value'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entryTranslation()
    {
        return $this->belongsTo(EntryTranslation::class);
    }

}
