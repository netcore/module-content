@foreach($languages as $language)

    @if($languages->count() > 1)
        <b>{{ strtoupper($language->iso_code) }}:</b>
    @endif

    {{ trans_model($entry, $language, 'slug') }}
    <br>
@endforeach
