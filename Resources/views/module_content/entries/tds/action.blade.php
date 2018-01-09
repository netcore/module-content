<a href="{{ route('content::entries.edit', $entry) }}" class="btn btn-primary btn-xs">
    <i class="fa fa-edit"></i> Edit
</a>

<a
    class="btn btn-xs btn-danger confirm-action"
    data-title="Confirmation"
    data-text="Page will be deleted. Are you sure?"
    data-confirm-button-text="Delete"
    data-method="DELETE"
    data-href="{{ route('content::entries.destroy', $entry) }}"
    data-success-title="Success"
    data-success-text="Page was deleted"
    data-refresh-datatable="#{{ $entry->channel_id ? 'channel-'.$entry->channel_id.'-datatable' : 'single-entries-datatable' }}"
>
    <i class="fa fa-trash"></i> Delete
</a>
