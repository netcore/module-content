@foreach($entry->translations as $translation)
    <b>{{ strtoupper($translation->locale) }}:</b>
    {{ $translation->slug }}
    <br>
@endforeach