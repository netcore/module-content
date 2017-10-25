<?php

namespace Modules\Content\Models;

use Codesleeve\Stapler\ORM\EloquentTrait;
use Codesleeve\Stapler\ORM\StaplerableInterface;
use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Content\PassThroughs\Entry\Storage;
use Modules\Content\Translations\EntryTranslation;
use Modules\Admin\Traits\StaplerAndTranslatable;
use Modules\Admin\Traits\BootStapler;
use Modules\Admin\Traits\SyncTranslations;
use Modules\Crud\Traits\CRUDModel;

class Entry extends Model implements StaplerableInterface
{

    /**
     * Stapler and Translatable traits conflict with each other
     * Thats why we have created custom trait to resolve this conflict
     */
    use StaplerAndTranslatable, BootStapler;

    use Translatable {
        StaplerAndTranslatable::getAttribute insteadof Translatable;
        StaplerAndTranslatable::setAttribute insteadof Translatable;
    }

    use EloquentTrait {
        StaplerAndTranslatable::getAttribute insteadof EloquentTrait;
        StaplerAndTranslatable::setAttribute insteadof EloquentTrait;
        BootStapler::boot insteadof EloquentTrait;
    }

    use SyncTranslations;

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
        'is_homepage',
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
        'content',
        'attachment'
    ];

    /**
     * @var array
     */
    protected $staplerConfig = [
        'attachment' => [
            'default_style' => 'original',
            'url'           => '/uploads/:class/:attachment/:id_partition/:style/:filename'
        ]
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

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->whereIsActive(1);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeHomepage($query)
    {
        return $query->active()->whereIsHomepage(1);
    }
}
