<?php

namespace Modules\Content\Traits;

use Netcore\Translator\Helpers\TransHelper;

trait ChannelSeederTrait {
    /**
     * Translate data for all locales.
     *
     * @param $keyValuePairs
     * @return array
     */
    private function translateKeyValuePairsToAllLocales($keyValuePairs): array
    {
        $locales = collect(TransHelper::getAllLanguages())->pluck('iso_code');

        $result = [];

        foreach ($keyValuePairs as $key => $value) {
            foreach ($locales as $locale) {

                $localizedValue = $value;
                if ($key == 'slug') {
                    $localizedValue = str_slug($value);
                }

                $result[$locale][$key] = $localizedValue;
            }
        }

        return $result;
    }
}