<?php

namespace Modules\Content\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Content\PassThroughs\Channel\Storage;
use Modules\Admin\Traits\SyncTranslations;
use Modules\Content\Translations\ChannelTranslation;
use Modules\Crud\Traits\CRUDModel;

class Channel extends Model
{
    use Translatable, SyncTranslations, CRUDModel;

    /**
     * @var string
     */
    protected $table = 'netcore_content__channels';

    /**
     * @var array
     */
    protected $fillable = [
        'layout',
        'is_active'
    ];

    /**
     * @var string
     */
    public $translationModel = ChannelTranslation::class;

    /**
     * @var array
     */
    public $translatedAttributes = [
        'slug',
        'name'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries()
    {
        return $this->hasMany(Entry::class);
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
}
