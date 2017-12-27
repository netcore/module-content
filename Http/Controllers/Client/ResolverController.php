<?php

namespace Modules\Content\Http\Controllers\Client;

use Illuminate\Routing\Controller;
use Modules\Content\Models\Channel;
use Modules\Content\Models\Entry;

class ResolverController extends Controller
{

    /**
     * @param null $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function resolve($slug = null)
    {
        $template = config('netcore.module-content.resolver_template') ?: 'content::module_content.resolver.page';
        $page = null;

        if (!$slug) {
            $page = Entry::homepage()->first();

            if(!$page) { // Home page is not set
                abort(404);
            }

        } else {

            $exploded = explode('/', $slug);

            if (count($exploded) == 1) {

                // Single page (or channel)

                $channel = Channel::active()->whereHas('translations', function ($q) use ($slug) {
                    return $q->whereSlug($slug);
                })->first();

                if($channel) {
                    // @TODO - its not a valid approach to use $channel->slug. Must come up with something better.
                    $channelTemplate = config('netcore.module-content.channels.'.$channel->slug.'.template');
                    if($channelTemplate) {
                        return view($channelTemplate, compact('channel'));
                    }
                }

                $page = Entry::active()->currentRevision()->whereHas('translations', function ($q) use ($slug) {
                    return $q->whereSlug($slug);
                })->first();

            } elseif (count($exploded) == 2) {

                // Channel -> page

                $channelSlug = array_get($exploded, 0);
                $entrySlug = array_get($exploded, 1);

                $channel = Channel::active()->whereHas('translations', function ($q) use ($channelSlug) {
                    return $q->whereSlug($channelSlug);
                })->first();

                if (!$channel) {
                    return redirect()->to('/');
                }

                $page = Entry::active()->whereChannelId($channel->id)
                    ->whereHas('translations', function ($q) use ($entrySlug) {
                        return $q->whereSlug($entrySlug);
                    })->first();
            }
        }

        if (!$page) {
            return redirect()->to('/');
        }

        return view($template, compact('page'));
    }

}
