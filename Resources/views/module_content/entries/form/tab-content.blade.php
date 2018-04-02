@foreach($languages as $language)
    <div>
        @php
            if (isset($entry)) {
                $entryTranslation = $entry->translations->where('locale', $language->iso_code)->first();
                $entryTranslation = $entryTranslation ? $entryTranslation : (new \Modules\Content\Translations\EntryTranslation());
            }
        @endphp


        <div class="row localization-content locale-{{$language->iso_code}}">
            <div class="col-xs-{{ $allowAttachment ? 4 : 6 }}">
                <div class="form-group">
                    <label>Title <span class="label label-light">{{$language->iso_code}}</span></label>
                    {!! Form::text('translations['.$language->iso_code.'][title]', trans_model((isset($entry) ? $entry : null), $language, 'title'), ['class' => 'form-control title','data-language' => $language->iso_code]) !!}
                    <span class="error-span"></span>
                </div>
            </div>

            <div class="col-xs-{{ $allowAttachment ? 4 : 6 }}">

                <div class="form-group">
                    <label>Slug <span class="label label-light">{{$language->iso_code}}</span></label>
                    (Automatically generated if left empty)
                    {!! Form::text('translations['.$language->iso_code.'][slug]', trans_model((isset($entry) ? $entry : null), $language, 'slug'), ['class' => 'form-control slug slug-' . $language->iso_code]) !!}
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

                    </div>
                </div>
            @endif
        </div>

        @include('content::module_content.entries.form.widgets')

    </div>
@endforeach

<div class="pull-left">
    {!! Form::select(null, $widgetOptions, null, [
        'class' => 'form-control width-150 inline',
        'id' => 'select-widget'
    ]) !!}

    <a class="btn btn-md btn-success" id="add-widget-button"><i class="fa fa-plus"></i> Add widget</a>
</div>