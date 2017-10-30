<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">

    <li
        role="presentation"
        class="active"
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
        <li role="presentation">
            <a
                href="#channel-{{ $channel->id }}"
                aria-controls="channel-{{ $channel->id }}"
                role="tab"
                data-toggle="tab"
            >
                {{ trans_model($channel, $firstLanguage, 'name') }}
            </a>
        </li>
    @endforeach
</ul>

<!-- Tab panes -->
<div class="tab-content">

    @foreach($channels as $channel)
        <div
            role="tabpanel"
            class="tab-pane"
            id="channel-{{ $channel->id }}"
        >
            <div class="above-table">
                <a href="{{ route('content::channels.edit', $channel) }}" class="btn btn-primary btn-xs">
                    Edit channel
                </a>

                <a href="{{ route('content::entries.create', $channel) }}" class="btn btn-success btn-xs">
                    Add new {{ trans_model($channel, $firstLanguage, 'name') }} page
                </a>
            </div>

            <div class="table-primary">
                <table
                    class="table table-bordered datatable"
                    data-ajax="{{ route('content::entries.pagination') }}?channel_id={{ $channel->id }}"
                    data-caption="Pages in {{ trans_model($channel, $firstLanguage, 'name') }}"
                    id="channel-{{ $channel->id }}-datatable"
                >
                    <thead>
                    <tr>
                        <th>Published</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Content</th>
                        <th>Attachment</th>
                        <th>Updated at</th>
                        <th>Homepage?</th>
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
        class="tab-pane active"
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
                id="single-entries-datatable"
            >
                <thead>
                <tr>
                    <th>Published</th>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Content</th>
                    <th>Attachment</th>
                    <th>Updated at</th>
                    <th>Homepage?</th>
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
