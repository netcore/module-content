@foreach($entry->translations as $translation)
    <b>{{ strtoupper($translation->locale) }}:</b>
    {{ $translation->title }}
    <br>
@endforeach