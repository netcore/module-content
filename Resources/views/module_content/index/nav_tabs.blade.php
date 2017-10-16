<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">

    @foreach($channels as $channel)
        <li
            role="presentation"
            class="{{ $loop->first ? 'active' : '' }}"
        >
            <a
                href="#channel-{{ $channel->name }}"
                aria-controls="channel-{{ $channel->name }}"
                role="tab"
                data-toggle="tab"
            >
                {{ $channel->name }}
            </a>
        </li>
    @endforeach

    <li role="presentation">
        <a
            href="#single-entries"
            aria-controls="single-entries"
            role="tab"
            data-toggle="tab"
        >
            Single pages
        </a>
    </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">

    @foreach($channels as $channel)
        <div
            role="tabpanel"
            class="tab-pane {{ $loop->first ? 'active' : '' }}"
            id="channel-{{ $channel->name }}"
        >
            <div class="above-table">
                <a {{-- href="{{ route('content::channels.edit', $channel) }}" --}} class="btn btn-primary btn-xs disabled">
                    Edit channel
                </a>

                <a href="{{ route('content::entries.create', $channel) }}" class="btn btn-success btn-xs">
                    Add new page
                </a>
            </div>

            <div class="table-primary">
                <table
                    class="table table-bordered datatable"
                    data-ajax="{{ route('content::entries.pagination') }}?channel_id={{ $channel->id }}"
                    data-caption="Pages in {{ $channel->name }}"
                >
                    <thead>
                    <tr>
                        <th>Created at</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Content</th>
                        <th>Updated at</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

                <br> <br> <br>
            </div>
        </div>
    @endforeach

    <div
        role="tabpanel"
        class="tab-pane {{ $channels->count() == 0 ? 'active' : '' }}"
        id="single-entries"
    >
        <div class="above-table">
            <a href="{{ route('content::entries.create') }}" class="btn btn-success btn-xs">
                Add new page
            </a>
        </div>

        <div class="table-primary">
            <table
                class="table table-bordered datatable"
                data-ajax="{{ route('content::entries.pagination') }}"
                data-caption="Single pages"
            >
                <thead>
                <tr>
                    <th>Created at</th>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Content</th>
                    <th>Updated at</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            <br> <br> <br>
        </div>
    </div>
</div>
