<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Traits\BootStapler;
use Codesleeve\Stapler\ORM\EloquentTrait;
use Codesleeve\Stapler\ORM\StaplerableInterface;

class ImageBlockItem extends Model /* implements StaplerableInterface*/
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
    protected $table = 'netcore_content__image_block_items';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        //'image',
        'order',
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
    public function imageBlock()
    {
        return $this->belongsTo(ImageBlock::class);
    }

    /**
     * @return mixed
     */
    public function fields()
    {
        return $this->hasMany(ImageBlockItemField::class);
    }

    /**
     * @param String $key
     * @return mixed
     */
    public function getField(String $key)
    {
        $field = $this->fields->where('key', $key)->first();
        return $field ? $field->value : '';
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
