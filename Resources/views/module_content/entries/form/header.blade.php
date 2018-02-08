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

    @if(isset($channel))
        @foreach($channel->fields as $field)
            <div class="col-md-12">
                @include('content::module_content.entries.partials.field')
            </div>
        @endforeach
    @endif
</div>
