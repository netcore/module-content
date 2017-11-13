<div class="row">
    <div class="col-xs-4">
        <div class="form-group">
            <label>Title</label>
            {!! Form::text('translations['.$language->iso_code.'][title]', trans_model((isset($entry) ? $entry : null), $language, 'title'), ['class' => 'form-control title']) !!}
            <span class="error-span"></span>
        </div>
    </div>
    <div class="col-xs-4">
        <div class="form-group">
            <label>Slug</label>
            (Automatically generated if left empty)
            {!! Form::text('translations['.$language->iso_code.'][slug]', trans_model((isset($entry) ? $entry : null), $language, 'slug'), ['class' => 'form-control slug']) !!}
            <span class="error-span"></span>
        </div>
    </div>
    <div class="col-xs-4">
        <div class="form-group">
            <label>Attachment</label>
            <br>
            {!! Form::file('attachment', [
                'class' => 'form-control form-input',
                'style' => ((isset($entry) && $entry->attachment_file_name) ? 'max-width:250px;display:inline;' : '' )
            ]) !!}

            <span class="error-span"></span>

            @if(isset($entry) && $entry->attachment_file_name)
                <a
                        class="btn btn-xs btn-danger confirm-action"
                        data-title="Confirmation"
                        data-text="Attachment will be deleted. Are you sure?"
                        data-confirm-button-text="Delete"
                        data-method="DELETE"
                        data-href="{{ route('content::entries.destroy_attachment', $entry) }}"
                        data-success-title="Success"
                        data-success-text="Attachment was deleted"
                        data-refresh-page-on-success
                >
                    Delete
                </a>

                <a href="{{ $entry->attachment->url() }}" target="_blank">Download ({{ $entry->human_attachment_size }})</a>
            @endif
        </div>
    </div>
</div>
