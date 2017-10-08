<?php

namespace Modules\Content\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Content\Models\Channel;

class ChannelController extends Controller
{

    /**
     * @param Channel $channel
     * @return mixed
     */
    public function show(Channel $channel)
    {
        return view('content::module_content.channels.show', compact('channel'));
    }
}
