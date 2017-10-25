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
        $page = null;

        if (!$slug) {
            $page = Entry::homepage()->with('contentBlocks')->first();
        } else {

            $exploded = explode('/', $slug);

            if (count($exploded) == 1) {

                // Single page (or channel)

                $channel = Channel::whereHas('translations', function ($q) use ($slug) {
                    return $q->whereSlug($slug);
                })->first();

                if($channel) {
                    // @TODO - its not a valid approach to use $channel->slug. Must come up with something better.
                    $channelTemplate = config('netcore.module-content.channels.'.$channel->slug.'.template');
                    if($channelTemplate) {
                        return view($channelTemplate, compact('channel'));
                    }
                }

                $page = Entry::whereHas('translations', function ($q) use ($slug) {
                    return $q->whereSlug($slug);
                })->first();

            } elseif (count($exploded) == 2) {

                // Channel -> page

                $channelSlug = array_get($exploded, 0);
                $entrySlug = array_get($exploded, 1);

                $channel = Channel::whereHas('translations', function ($q) use ($channelSlug) {
                    return $q->whereSlug($channelSlug);
                })->first();

                if (!$channel) {
                    abort(404);
                }

                $page = Entry::whereChannelId($channel->id)
                    ->whereHas('translations', function ($q) use ($entrySlug) {
                        return $q->whereSlug($entrySlug);
                    })->first();
            }
        }

        if (!$page) {
            abort(404);
        }

        return view('content::module_content.resolver.page', compact('page'));
    }

}
