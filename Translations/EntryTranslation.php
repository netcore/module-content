<?php

namespace Modules\Content\Translations;

use Codesleeve\Stapler\ORM\EloquentTrait;
use Codesleeve\Stapler\ORM\StaplerableInterface;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Traits\BootStapler;

class EntryTranslation extends Model implements StaplerableInterface
{

    use BootStapler;
    use EloquentTrait;

    use EloquentTrait {
        BootStapler::boot insteadof EloquentTrait;
    }

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
        'attachment',
        'locale', // This is very important
        'content'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $staplerConfig = [
        'attachment' => [
            'default_style' => 'original',
            'url' => '/uploads/:class/:attachment/:id_partition/:style/:filename'
        ]
    ];

}
