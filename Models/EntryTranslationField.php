<?php

namespace Modules\Content\Models;

use Codesleeve\Stapler\ORM\EloquentTrait;
use Codesleeve\Stapler\ORM\StaplerableInterface;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Traits\BootStapler;
use Modules\Content\Translations\EntryTranslation;

class EntryTranslationField extends Model implements StaplerableInterface
{

    use BootStapler;

    use EloquentTrait {
        BootStapler::boot insteadof EloquentTrait;
    }

    /**
     * @var string
     */
    protected $table = 'netcore_content__entry_translation_fields';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'image',
        'is_main',
    ];

    /**
     * @var array
     */
    protected $staplerConfig = [
        'image' => [
            'default_style' => 'original',
            'url'           => '/uploads/:class/:attachment/:id_partition/:style/:filename'
        ]
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entryTranslation()
    {
        return $this->belongsTo(EntryTranslation::class);
    }
}
