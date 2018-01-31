<?php

namespace Modules\Content\PassThroughs\Entry;

use Modules\Content\Models\Entry;
use Modules\Content\PassThroughs\PassThrough;
use Netcore\Translator\Models\Language;

class Attachments extends PassThrough
{
    /**
     * @var Entry
     */
    private $entry;

    /**
     * Storage constructor.
     *
     * @param Entry $entry
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @param Language $language
     * @return bool
     */
    public function hasForLanguage(Language $language): Bool
    {
        $entry = $this->entry;
        $entryTranslation = $entry->translations->where('locale', $language->iso_code)->first();

        if (!$entryTranslation) {
            return false;
        }

        return $entryTranslation->attachment_file_name ? true : false;
    }

    /**
     * @param Language $language
     * @return bool
     */
    public function forLanguage(Language $language)
    {
        $entry = $this->entry;
        $entryTranslation = $entry->translations->where('locale', $language->iso_code)->first();

        return $entryTranslation ? $entryTranslation->attachment : null;
    }

    /**
     * @param Language $language
     * @return String
     */
    public function humanSizeForLanguage(Language $language): String
    {
        $entry = $this->entry;
        $entryTranslation = $entry->translations->where('locale', $language->iso_code)->first();

        if (!$entryTranslation) {
            return '0 KB';
        }

        $decimals = 0;
        $bytes = $entryTranslation->attachment_file_size;

        if (!$bytes) {
            return '0 KB';
        }

        $size = [' B', ' KB', ' MB', ' GB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}
