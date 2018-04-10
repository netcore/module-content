<?php

namespace Modules\Content\Models;

use Codesleeve\Stapler\ORM\EloquentTrait;
use Codesleeve\Stapler\ORM\StaplerableInterface;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Traits\BootStapler;

class EntryField extends Model implements StaplerableInterface
{

    use BootStapler;

    use EloquentTrait {
        BootStapler::boot insteadof EloquentTrait;
    }

    /**
     * @var string
     */
    protected $table = 'netcore_content__entry_fields';

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
    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }
}

