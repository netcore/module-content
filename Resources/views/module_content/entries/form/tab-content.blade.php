@foreach($languages as $language)
    <div>
        @php
            if (isset($entry)) {
                $entryTranslation = $entry->translations->where('locale', $language->iso_code)->first();
                $entryTranslation = $entryTranslation ? $entryTranslation : (new \Modules\Content\Translations\EntryTranslation());
            }
        @endphp

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