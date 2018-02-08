<?php

namespace Modules\Content\Models;

use Codesleeve\Stapler\ORM\EloquentTrait;
use Codesleeve\Stapler\ORM\StaplerableInterface;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Traits\BootStapler;

class ContentBlockItem extends Model implements StaplerableInterface
{

    use BootStapler;

    use EloquentTrait {
        BootStapler::boot insteadof EloquentTrait;
    }

    /**
     * @var string
     */
    protected $table = 'netcore_content__content_block_items';

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
    public function contentBlock()
    {
        return $this->belongsTo(ContentBlock::class);
    }
}
