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
        'key',
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
     * @return mixed
     */
    public function parent()
    {
        return $this->belongsTo(Entry::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments()
    {
        return $this->hasMany(EntryAttachment::class);
    }

    /**
     * @return mixed
     */
    public function children()
    {
        return $this->hasMany(Entry::class, 'parent_id');
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
    public function scopeCurrentRevision($query)
    {
        return $query->whereType('current');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeHomepage($query)
    {
        return $query->active()->currentRevision()->whereIsHomepage(1);
    }

    /**
     * @return string
     */
    public function preview($length)
    {
        $replaced = str_replace('</p>', ' </p>', $this->content);
        $replaced = html_entity_decode($replaced);

        return str_limit(strip_tags($replaced), $length);
    }

    /**
     * @param $length
     * @return bool
     */
    public function readMoreIfPreviewIs($length)
    {
        $lengthOfPreview = mb_strlen($this->preview($length));

        $lengthOfPreviewPlusOne = mb_strlen($this->preview($length + 1));

        return $lengthOfPreview < $lengthOfPreviewPlusOne;
    }

    public function getFieldsAttribute()
    {
        $translation = $this->translations->where('locale', $this->locale())->first();
        if ($translation) {
            return $translation->fields;
        }

        return collect([]);
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
     * @param null $locale
     * @return array
     */
    public function formatResponse($locale = null)
    {
        $item = $this;

        $channel = $item->channel;
        $translation = $item->translateOrNew($locale);

        $entryFields = [
            'title' => $translation->title
        ];

        if ($channel) {
            $entryFieldList = $channel->fields;

            foreach ($entryFieldList as $field) {
                if ($field->type != 'file') {
                    $entryFields[$field->key] = $item->getField($field->key);
                }
            }
        }


        $widgets = [];
        foreach ($translation->contentBlocks->sortBy('order') as $contentBlock) {
            $widgetKey = $contentBlock->widget;
            $widget = widgets()->where('key', $contentBlock->widget)->first();
            $widgetFields = $widget->fields->groupBy('is_main');
            $widgets[$widgetKey]['fields'] = [];
            $widgets[$widgetKey]['items'] = [];

            if (isset($widgetFields['1'])) {
                foreach ($widgetFields['1'] as $field) {
                    if ($field->type != 'file') {
                        $widgets[$widgetKey]['fields'][$field->key] = $contentBlock->getField($field->key);
                    } else {
                        $widgets[$widgetKey]['fields'][$field->key] = $contentBlock->getStaplerObj($field->key);
                    }
                }
            }

            if (isset($widgetFields['0'])) {
                $widgetBlock = WidgetBlock::with('items.fields')->find(array_get($contentBlock->data,
                    'widget_block_id'));


                $items = [];
                $i = 0;
                foreach ($widgetBlock->items->sortBy('order') as $widgetItem) {
                    foreach ($widgetFields['0'] as $field) {
                        if ($field->type != 'file') {
                            $items[$i][$field->key] = $widgetItem->getField($field->key);
                        } else {
                            $items[$i][$field->key] = $widgetItem->getStaplerObj($field->key);
                        }
                    }
                    $i++;
                }
                $widgets[$widgetKey]['items'][] = $items;

            }
        }

        return [
            'key'         => $item->key,
            'slug'        => $item->slug,
            'is_homepage' => $item->is_homepage,
            'page_data'   => $entryFields,
            'widgets'     => $widgets,
        ];
    }
}
