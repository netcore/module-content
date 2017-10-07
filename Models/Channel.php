<?php

namespace Modules\Content\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Content\Traits\SyncTranslations;
use Modules\Content\Translations\ChannelTranslation;

class Channel extends Model
{

    use Translatable, SyncTranslations;

    //@TODO: varbūt šis jāiekļauj Admin modulī
    use \Modules\Crud\Traits\CrudifyModel;

    /**
     * @var string
     */
    protected $table = 'netcore_content__channels';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
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
        'slug'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries()
    {
        return $this->hasMany(Entry::class);
    }
}
