@foreach($languages as $language)

    @if($languages->count() > 1)
        <b>{{ strtoupper($language->iso_code) }}:</b>
    @endif

    @if( $entry->attachments()->hasForLanguage($language) )
        <a href="{{ $entry->attachments()->forLanguage($language)->url() }}" target="_blank">
            File ({{ $entry->attachments()->humanSizeForLanguage($language) }})
        </a>
    @else
        None
    @endif

    <br>
@endforeach
