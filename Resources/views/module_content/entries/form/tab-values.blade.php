@if(isset($channel))
    @foreach($languages as $language)
        <div>
            @php
                if (isset($entry)) {
                    $entryTranslation = $entry->translations->where('locale', $language->iso_code)->first();
                    $entryTranslation = $entryTranslation ? $entryTranslation : (new \Modules\Content\Translations\EntryTranslation());
                }
            @endphp
            <div class="row localization-content locale-{{$language->iso_code}}"
                 @if($loop->iteration != 1) style="display:none;" @endif>
                <div class="col-xs-6">
                    <div class="form-group">
                        <label>Title <span class="label label-light">{{$language->iso_code}}</span></label>
                        {!! Form::text('translations['.$language->iso_code.'][title]', trans_model((isset($entry) ? $entry : null), $language, 'title'), ['class' => 'form-control title','data-language' => $language->iso_code]) !!}
                        <span class="error-span"></span>
                    </div>
                </div>

                <div class="col-xs-6">

                    <div class="form-group">
                        <label>Slug <span class="label label-light">{{$language->iso_code}}</span></label>
                        (Automatically generated if left empty)
                        {!! Form::text('translations['.$language->iso_code.'][slug]', trans_model((isset($entry) ? $entry : null), $language, 'slug'), ['class' => 'form-control slug slug-' . $language->iso_code]) !!}
                        <span class="error-span"></span>
                    </div>
                </div>
            </div>


        </div>
    @endforeach

    @include('content::module_content.entries.form.attachments')
    <div class="panel">
        <div class="panel-heading">
            Translateable values
            <div class="pull-right">
                <button class="btn btn-xs btn-success js-toggle-panel-body">Show/hide</button>
            </div>
        </div>
        <div class="panel-body">
            @php($i=1)
            @foreach($languages as $language)
                @foreach($channel->fields->where('is_global', 0) as $field)
                    <div class="col-md-12 localization-content locale-{{$language->iso_code}}"
                         @if($i != 1) style="display:none;" @endif>
                        @include('content::module_content.entries.partials.field')
                    </div>
                @endforeach
                @php($i++)
            @endforeach
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading">
            Global values
            <div class="pull-right">
                <button class="btn btn-xs btn-success js-toggle-panel-body">Show/hide</button>
            </div>
        </div>
        <div class="panel-body" style="display: none;">
            @foreach($channel->fields->where('is_global', 1) as $field)
                @include('content::module_content.entries.partials.global-field')
            @endforeach
        </div>
    </div>
@endif