@if(isset($channel))
    @foreach($languages as $language)
        @foreach($channel->fields as $field)
            <div class="col-md-12 localization-content locale-{{$language->iso_code}}">
                @include('content::module_content.entries.partials.field')
            </div>
        @endforeach
    @endforeach
@endif