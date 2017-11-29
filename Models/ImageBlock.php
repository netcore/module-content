<?php

namespace Modules\Content\Models;

use Illuminate\Database\Eloquent\Model;

class ImageBlock extends Model
{

    /**
     * @var string
     */
    protected $table = 'netcore_content__image_blocks';

    /**
     * @var array
     */
    protected $fillable = [

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(ImageBlockItem::class);
    }
}
