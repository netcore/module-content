<?php

namespace Modules\Content\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Content\PassThroughs\Entry\Revision;
use Modules\Content\PassThroughs\Entry\Storage;
use Modules\Content\Translations\EntryTranslation;
use Modules\Crud\Traits\CRUDModel;
use Modules\Form\Models\Form;
use Modules\Translate\Traits\SyncTranslations;

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function globalFields()
    {
        return $this->hasMany(EntryField::class);
    }

    /**
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getUrlAttribute()
    {
        $url = $this->slug;
        if ($this->channel) {
            $url = $this->channel->slug . '/' . $this->slug;
        }

        return url($url);
    }

    /**
     * @return mixed
     */
    public function getMediaAttribute()
    {
        return $this->attachments->where('is_featured', 0)->map(function ($item) {
            return [
                'attachment'  => $item->image_file_name ? $item->image->url() : '',
                'media'       => $item->media,
                'is_featured' => $item->is_featured,
            ];
        });
    }

    /**
     * @return mixed
     */
    public function getFeaturedMediaAttribute()
    {
        return $this->attachments->where('is_featured', 1)->map(function ($item) {
            return [
                'attachment'  => $item->image_file_name ? $item->image->url() : '',
                'media'       => $item->media,
                'is_featured' => $item->is_featured,
            ];
        });
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
     * @param String $key
     * @return string
     */
    public function getGlobalField(String $key)
    {
        $field = $this->items->where('key', $key)->first();

        return $field ? $field->value : '';
    }

    /**
     * @param String $key
     * @return mixed
     */
    public function getGlobalStaplerObj(String $key)
    {
        $field = $this->items->where('key', $key)->first();

        return $field ? $field->image : null;
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

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getFieldsAttribute()
    {
        $translation = $this->translations->where('locale', $this->locale())->first();
        if ($translation) {
            return $translation->fields;
        }

        return collect([]);
    }

    /**
     * @return mixed
     */
    public function getGlobalFieldAttribute()
    {
        return $this->globalFields->mapWithKeys(function ($item) {
            if ($item->image_file_name) {
                return [
                    $item->key => $item->image->url()
                ];
            }

            return [
                $item->key => $item->value
            ];
        });
    }

    /**
     * @return mixed
     */
    public function getFieldAttribute()
    {
        return $this->fields->mapWithKeys(function ($item) {
            if ($item->image_file_name) {
                return [
                    $item->key => $item->image->url()
                ];
            }

            return [
                $item->key => $item->value
            ];
        });
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
     * @param bool $withContent
     * @return array
     */
    public function formatResponse($locale = null, $withContent = true)
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
                if ($field->type !== 'file') {
                    $entryFields[$field->key] = $item->getField($field->key);
                }
            }
        }

        $widgets = [];
        if ($withContent) {
            foreach ($translation->contentBlocks->sortBy('order') as $w => $contentBlock) {
                $widgetKey = $contentBlock->widget;
                $widget = widgets()->where('key', $widgetKey)->first();
                $widgetFields = $widget ? $widget->fields->groupBy('is_main') : [];
                $widgets[$w]['widget'] = $widgetKey;
                $widgets[$w]['fields'] = [];
                $widgets[$w]['items'] = [];

                if (isset($widgetFields['1'])) {
                    foreach ($widgetFields['1'] as $field) {
                        if ($field->key === 'form') {
                            $widgets[$w]['fields'][$field->key] = $contentBlock->getField($field->key);
                            $widgets[$w]['fields']['form'] = ($form = Form::find($contentBlock->getField($field->key))) ? $form->formatResponse($locale) : [];
                        } elseif ($field->type !== 'file') {
                            $widgets[$w]['fields'][$field->key] = $contentBlock->getField($field->key);
                        } else {
                            $f = $contentBlock->items->where('key', $field->key)->first();
                            if ($f) {
                                if ($f->image_file_name) {
                                    $widgets[$w]['fields'][$field->key] = (object)[
                                        'original' => [
                                            'path' => $f->image->path(),
                                            'url'  => url($f->image->url())
                                        ]
                                    ];
                                } else {
                                    $widgets[$w]['fields'][$field->key] = (object)[
                                        'original' => [
                                            'path' => null,
                                            'url'  => null
                                        ]
                                    ];
                                }
                            } else {
                                $widgets[$w]['fields'][$field->key] = (object)[
                                    'original' => [
                                        'path' => null,
                                        'url'  => null
                                    ]
                                ];
                            }
                        }
                    }
                }

                if (isset($widgetFields['0'])) {
                    $widgetBlock = WidgetBlock::with('items.fields')->find(array_get($contentBlock->data,
                        'widget_block_id'));

                    $items = [];
                    foreach ($widgetBlock->items->sortBy('order') as $i => $widgetItem) {
                        foreach ($widgetFields['0'] as $field) {
                            if ($field->type !== 'file') {
                                $items[$i][$field->key] = $widgetItem->getField($field->key);
                            } else {
                                $f = $widgetItem->fields->where('key', $field->key)->first();
                                if ($f) {
                                    if ($f->image_file_name) {
                                        $items[$i][$field->key] = (object)[
                                            'original' => [
                                                'path' => $f->image->path(),
                                                'url'  => url($f->image->url())
                                            ]
                                        ];
                                    } else {
                                        $items[$i][$field->key] = (object)[
                                            'original' => [
                                                'path' => null,
                                                'url'  => null
                                            ]
                                        ];
                                    }
                                } else {
                                    $items[$i][$field->key] = (object)[
                                        'original' => [
                                            'path' => null,
                                            'url'  => null
                                        ]
                                    ];
                                }
                            }
                        }
                    }

                    $widgets[$w]['items'] = $items;
                }
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
