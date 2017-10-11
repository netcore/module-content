@foreach($languages as $language)

    @if($languages->count() > 1)
        <b>{{ strtoupper($translation->locale) }}:</b>
    @endif

    {{ trans_model($entry, $language, 'title') }}
    <br>
@endforeach
