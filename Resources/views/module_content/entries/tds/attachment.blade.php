@if($entry->attachment_file_name)
    Download
    {{--
    <a href="{{ $entry->attachment->url() }}">Download</a>
    --}}
@else
    No attachment
@endif
