
@php
    $frontendPath = '/assets/content/css/entries/form.css';
    $serverPath = public_path($frontendPath);
    $filemtime = filemtime($serverPath);
    $frontendPath .= '?v=' . $filemtime;
@endphp

<link rel="stylesheet" href="{{ $frontendPath }}">

{{-- @TODO get php out of View  --}}

@php
    $cssFiles = [];
    foreach($widgetData as $data) {
        $file = array_get($data, 'backend_css');
        if( $file && !in_array($file, $cssFiles) ) {
            $cssFiles[] = $file;
        }
    }
@endphp

@foreach($cssFiles as $file)

    @php
        $frontendPath = '/assets/content/css/widgets/' . $file;
        $serverPath = public_path($frontendPath);
        $filemtime = filemtime($serverPath);
        $frontendPath .= '?v=' . $filemtime;
    @endphp

    <link rel="stylesheet" href="{{ $frontendPath }}">
@endforeach

{{-- It's important to have version greater than 0.8.6 because of this PR https://github.com/summernote/summernote/pull/1948 --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.8/summernote.css">
