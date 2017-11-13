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
        'is_active',
        'is_homepage',
        'layout',
        'attachment',
        'published_at'
    ];

    /**
     * @var array
     */
    public $dates = [
        'published_at'
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
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

    /**
     * @return string
     */
    public function preview($length)
    {
        $replaced = str_replace('</p>', ' </p>', $this->content);
        $replaced = html_entity_decode($replaced);

        return str_limit(
            strip_tags($replaced),
            $length
        );
    }

    /**
     * @param $length
     * @return bool
     */
    public function readMoreIfPreviewIs($length)
    {
        $lengthOfPreview = mb_strlen(
            $this->preview($length)
        );

        $lengthOfPreviewPlusOne = mb_strlen(
            $this->preview($length + 1)
        );

        return $lengthOfPreview < $lengthOfPreviewPlusOne;
    }

    /**
     * @return String
     */
    public function getHumanAttachmentSizeAttribute()
    {
        $decimals = 0;
        $bytes = $this->attachment_file_size;

        if (!$bytes) {
            return '0 KB';
        }

        $size = [' B', ' KB', ' MB', ' GB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}
