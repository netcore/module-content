<div class="table-basic">
    <table
            class="table table-bordered datatable"
            data-ajax="{{ route('content::entries.pagination') }}{{ isset( $channel ) ? '?channel_id=' . $channel->id : '' }}"
            data-caption="{{ isset( $channel ) ? 'Items in ' . trans_model($channel, $firstLanguage, 'name') : 'Single pages' }}"
            data-allow-attachment="{{ $allowAttachment ? 1 : 0 }}"
            id="{{isset( $channel ) ? 'channel-'.$channel->id.'-datatable' : 'single-entries-datatable'}}"
    >
        <thead>
        <tr>
            <th>Published</th>
            <th>Title</th>
            <th>URI</th>
            <th>Content</th>
            @if($allowAttachment)
                <th>Attachment</th>
            @endif
            <th>Updated at</th>
            {{-- <th>Homepage?</th> --}}
            <th>Active</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <br> <br> <br>
</div>