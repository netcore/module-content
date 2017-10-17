
{{-- foreach existing blocks --}}
{{-- @TODO --}}

{{-- form to add new block (image,  --}}
{{-- @TODO --}}


<div class="form-control">
    <input type="file" name="images[]" id="image-upload-input" class="form-input" multiple>
</div>

<br>

@if( $imageBlock && $imageBlock->items->count() )

    <p style="margin-top:10px;">
        Drag'n'drop to change order
    </p>

    <table
        class="table table-bordered"
    >
        <thead>
        <tr>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach( $imageBlock->items->sortBy('order') as $model )
            <tr
                class="fade-out-{{ $model->id }}"
                data-id="{{ $model->id }}"
            >
                <td class="handle">
                    <img
                        src="{{ $model->image->url('medium') }}"
                        alt=""
                        class="img-responsive"
                    >
                </td>
                <td style="text-align:center;vertical-align:middle;">
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
                        data-fade-out-selector=".fade-out-{{ $model->id }}"
                    >
                        Delete
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@else
    <p style="margin-top:10px;">
        Currently there are no items added
    </p>
@endif



