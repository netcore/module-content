
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
