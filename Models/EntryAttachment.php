<?php

namespace Modules\Content\Models;

use Codesleeve\Stapler\ORM\EloquentTrait;
use Codesleeve\Stapler\ORM\StaplerableInterface;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Traits\BootStapler;
use Modules\Content\Models\Entry;

class EntryAttachment extends Model implements StaplerableInterface
{

    use BootStapler;

    use EloquentTrait {
        BootStapler::boot insteadof EloquentTrait;
    }

    /**
     * @var string
     */
    protected $table = 'netcore_content__entry_attachments';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'image',
        'is_featured',
        'media',
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

    /**
     * @param $query
     * @return mixed
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured');
    }
}
