<a
    class="btn btn-xs btn-danger confirm-action"
    data-title="Confirmation"
    data-text="Do you really want to delete this block?"
    data-confirm-button-text="Delete"
    {{--
        Should be without ajax
        data-method="DELETE"
        data-href="{{ route('admin.advertisements.images.destroy', [$advertisement, $model]) }}"
    --}}
    data-success-title="Success"
    data-success-text="Block was deleted"
    data-fade-out-selector=".fade-out-{{ $modelId }}"
    data-hide-on-empty=".widget-tr"
>
    Delete
</a>
