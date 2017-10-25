@if($entry->attachment_file_name)
    <a href="{{ $entry->attachment->url() }}" target="_blank">File ({{ $entry->human_attachment_size }})</a>
@else
    None
@endif
