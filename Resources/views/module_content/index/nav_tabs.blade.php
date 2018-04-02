@php
    $activeChannel = request()->get('channel');
@endphp

<div class="row">
    <div class="col-md-2">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-stacked" role="tablist" {{ $channels->count() ? '' : 'hidden' }}>

            <li
                    role="presentation"
                    class="{{ $activeChannel ? '' : 'active' }}"
            >
                <a
                        href="#single-entries"
                        aria-controls="single-entries"
                        role="tab"
                        data-toggle="tab"
                >
                    Single pages
                </a>
            </li>

            @foreach($channels as $channel)
                <li
                        role="presentation"
                        class="{{ $activeChannel == $channel->slug ? 'active' : '' }}"
                >
                    <a
                            href="#channel-{{ $channel->id }}"
                            aria-controls="channel-{{ $channel->id }}"
                            role="tab"
                            data-toggle="tab"
                    >
                        {{ trans_model($channel, $firstLanguage, 'name') }} <span class="badge badge-danger">{{$channel->entries->count()}}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="text-center m-t-2">
            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-configure">Configure content fields</button>
        </div>

        @include('content::module_content.index.configure-modal')

    </div>

    <div class="col-md-10">
        <!-- Tab panes -->
        <div class="tab-content">

            @foreach($channels as $channel)
                <div
                        role="tabpanel"
                        class="tab-pane {{ $activeChannel == $channel->slug ? 'active' : '' }}"
                        id="channel-{{ $channel->id }}"
                >
                    <div class="text-right m-b-2">
                        <a href="{{ route('content::channels.edit', $channel) }}" class="btn btn-primary btn-xs">
                            <i class="fa fa-edit"></i> Edit channel
                        </a>

                        <a href="{{ route('content::entries.create', $channel) }}" class="btn btn-success btn-xs">
                            <i class="fa fa-plus"></i> Add new {{ strtolower(trans_model($channel, $firstLanguage, 'name')) }} item
                        </a>
                    </div>

                    @include('content::module_content.index.table',['channel' => $channel])
                </div>
            @endforeach

            <div
                    role="tabpanel"
                    class="tab-pane {{ $activeChannel ? '' : 'active' }}"
                    id="single-entries"
            >
                <div class="text-right m-b-2 {{ $channels->count() ? '' : 'without-channels' }}">
                    <a href="{{ route('content::entries.create') }}" class="btn btn-success btn-xs">
                        <i class="fa fa-plus"></i> Add new page
                    </a>
                </div>
                @include('content::module_content.index.table',['channel' => null])

            </div>
        </div>

    </div>
</div>