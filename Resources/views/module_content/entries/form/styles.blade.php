
<link rel="stylesheet" href="/assets/content/css/entries/form.css">

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
    <link rel="stylesheet" href="/assets/content/css/widgets/{{ $file }}">
@endforeach
