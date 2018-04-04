@if(isset($channel))
    @php($i=1)
    @foreach($languages as $language)
        @foreach($channel->fields as $field)
            <div class="col-md-12 localization-content locale-{{$language->iso_code}}" @if($i != 1) style="display:none;" @endif>
                @include('content::module_content.entries.partials.field')
            </div>
        @endforeach
        @php($i++)
    @endforeach
@endif