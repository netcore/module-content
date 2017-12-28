<div class="row">
    <div class="col-xs-{{ $allowAttachment ? 4 : 6 }}">
        <div class="form-group">
            <label>Title</label>
            {!! Form::text('translations['.$language->iso_code.'][title]', trans_model((isset($entry) ? $entry : null), $language, 'title'), ['class' => 'form-control title']) !!}
            <span class="error-span"></span>
        </div>
    </div>
    <div class="col-xs-{{ $allowAttachment ? 4 : 6 }}">
        <div class="form-group">
            <label>Slug</label>
            (Automatically generated if left empty)
            {!! Form::text('translations['.$language->iso_code.'][slug]', trans_model((isset($entry) ? $entry : null), $language, 'slug'), ['class' => 'form-control slug']) !!}
            <span class="error-span"></span>
        </div>
    </div>

    @if($allowAttachment)
        <div class="col-xs-4">
            <div class="form-group">
                <label>Attachment</label>
                <br>
                {!! Form::file('translations['.$language->iso_code.'][attachment]', [
                    'class' => 'form-control form-input attachment',
                    'style' => ((isset($entry) && $entry->attachments()->hasForLanguage($language)) ? 'max-width:250px;display:inline;' : '' )
                ]) !!}

                <span class="error-span"></span>

                @if(isset($entry) && $entry->attachments()->hasForLanguage($language) )
                    <a
                        class="btn btn-xs btn-danger confirm-action"
                        data-title="Confirmation"
                        data-text="Attachment will be deleted. Are you sure?"
                        data-confirm-button-text="Delete"
                        data-method="DELETE"
                        data-href="{{ route('content::entries.destroy_attachment', [$entry, $language]) }}"
                        data-success-title="Success"
                        data-success-text="Attachment was deleted"
                        data-refresh-page-on-success
                    >
                        Delete
                    </a>

                    <a href="{{ $entry->attachments()->forLanguage($language)->url() }}" target="_blank">
                        Download ({{ $entry->attachments()->humanSizeForLanguage($language) }})
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
