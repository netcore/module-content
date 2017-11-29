<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Traits\BootStapler;
use Codesleeve\Stapler\ORM\EloquentTrait;
use Codesleeve\Stapler\ORM\StaplerableInterface;

class ImageBlockItemField extends Model /*implements StaplerableInterface*/
{

    /*
    use BootStapler;

    use EloquentTrait {
        BootStapler::boot insteadof EloquentTrait;
    }
    */

    /**
     * @var string
     */
    protected $table = 'netcore_content__image_block_item_fields';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
    protected $casts = [
        'json' => 'array'
    ];
     */

    /**
     * @var array
     */
    protected $fillable = [
        'key',
        'value'
        /*
        'image',
        'order',
        'title',
        'subtitle',
        'content',
        'link',
        'json'
        */
    ];
    
    /**
     * @var array
    protected $staplerConfig = [
        'image' => [
            'default_style' => 'original',
            'url' => '/uploads/:class/:attachment/:id_partition/:style/:filename'
        ]
    ];
     */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function imageBlockItem()
    {
        return $this->belongsTo(ImageBlockItem::class);
    }

    /**
     * @return String
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
     */
}
