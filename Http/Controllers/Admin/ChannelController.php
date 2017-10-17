<?php

namespace Modules\Content\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Content\Http\Requests\Admin\ChannelRequest;
use Modules\Content\Models\Channel;
use Netcore\Translator\Helpers\TransHelper;

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

    /**
     * @param Channel $channel
     * @return mixed
     */
    public function edit(Channel $channel)
    {
        $languages = TransHelper::getAllLanguages();
        return view('content::module_content.channels.edit', compact('channel', 'languages'));
    }

    /**
     * @param Channel $channel
     * @param ChannelRequest $request
     * @return mixed
     */
    public function update(Channel $channel, ChannelRequest $request)
    {
        $requestData = $request->all();
        $channel->storage()->update($requestData);

        return redirect()->route('content::content.index')
            ->withSuccess('Channel has been updated!');
    }

}
