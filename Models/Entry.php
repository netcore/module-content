<?php

namespace Modules\Content\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Content\PassThroughs\Entry\Attachments;
use Modules\Content\PassThroughs\Entry\Revision;
use Modules\Content\PassThroughs\Entry\Storage;
use Modules\Content\Translations\EntryTranslation;
use Modules\Translate\Traits\SyncTranslations;
use Modules\Crud\Traits\CRUDModel;

class Entry extends Model
{

    use Translatable;
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
        'published_at'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'attachment' // Important. Datatables will hang up if you let them serialize this.
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
        'content',
        'attachment', // Object
        'attachment_file_name',
        'attachment_file_size',
        'attachment_content_type',
        'attachment_updated_at'
    ];

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
     * @return Revision
     */
    public function revision()
    {
        return new Revision($this);
    }

    /**
     * @return Attachments
     */
    public function attachments()
    {
        return new Attachments($this);
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
}
