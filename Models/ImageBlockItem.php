<?php

namespace Modules\Content\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Content\Translations\ImageBlockItemTranslation;
use Modules\Admin\Traits\StaplerAndTranslatable;
use Modules\Admin\Traits\BootStapler;
use Modules\Admin\Traits\SyncTranslations;
use Codesleeve\Stapler\ORM\EloquentTrait;
use Codesleeve\Stapler\ORM\StaplerableInterface;

class ImageBlockItem extends Model implements StaplerableInterface
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

    /**
     * @var string
     */
    protected $table = 'netcore_content__image_block_items';

    /**
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * @var array
     */
    protected $fillable = [
        'image',
        'order'
    ];
    
    /**
     * @var string
     */
    public $translationModel = ImageBlockItemTranslation::class;
    
    /**
     * @var array
     */
    public $translatedAttributes = [
        'title',
        'subtitle',
        'content',
        'link',
        'json'
    ];

    /**
     * @var array
     */
    protected $staplerConfig = [
        'image' => [
            'default_style' => 'original',
            'url' => '/uploads/:class/:attachment/:id_partition/:style/:filename'
        ]
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function imageBlock()
    {
        return $this->belongsTo(ImageBlock::class);
    }

    /**
     * @return array
     */
    public function getJsonDecodedAttribute()
    {
        return (array) json_decode($this->json);
    }

    /**
     * @return String
     */
    public function getHumanAttachmentSizeAttribute()
    {
        $decimals = 0;
        $bytes = $this->image_file_size;

        if (!$bytes) {
            return '0 KB';
        }

        $size = [' B', ' KB', ' MB', ' GB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}
