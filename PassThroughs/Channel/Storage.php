<?php

namespace Modules\Content\PassThroughs\Channel;

use Modules\Content\Models\Channel;
use Modules\Content\PassThroughs\PassThrough;

class Storage extends PassThrough
{
    /**
     * @var Channel
     */
    private $channel;

    /**
     * Storage constructor.
     *
     * @param Channel $channel
     */
    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    public function update(Array $requestData): Channel
    {
        $channel = $this->channel;

        $channel->update(
            array_only($requestData, ['layout'])
        );

        $channelTranslations = array_get($requestData, 'translations', []);

        $channelTranslations = collect($channelTranslations)->map(function ($translations, $locale) {

            if (strlen(array_get($translations, 'slug')) == 0) {
                $slug = str_slug(
                    array_get($translations, 'name')
                );
            } else {
                $slug = str_slug(
                    array_get($translations, 'slug')
                );
            }

            $translations['slug'] = $this->uniqueSlug($slug, $locale);

            return $translations;
        })->toArray();

        $channel->updateTranslations(
            $channelTranslations
        );

        return $channel;
    }

    /**
     * @param $originalSlug
     * @param $locale
     * @return string
     */
    private function uniqueSlug($originalSlug, $locale)
    {
        $slug = $originalSlug;

        $count = $this->uniqueSlugCount($originalSlug, $locale);
        if ($count) {
            $count++; // This will generate test and test-2, not test and test-1
            $slug = $originalSlug . '-' . $count;
        }

        while ($this->uniqueSlugCount($slug, $locale) > 0) {
            $count++;
            $slug = $originalSlug . '-' . $count;
        }

        return $slug;
    }

    /**
     * @param $originalSlug
     * @param $locale
     * @return mixed
     */
    private function uniqueSlugCount($originalSlug, $locale)
    {
        return Channel::join('netcore_content__channel_translations', 'netcore_content__channels.id', '=',
            'netcore_content__channel_translations.channel_id')
            ->where('netcore_content__channel_translations.slug', $originalSlug)
            ->where('netcore_content__channel_translations.locale', $locale)
            ->where('netcore_content__channels.id', '!=', $this->channel->id)
            ->count();
    }

}
