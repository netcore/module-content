@extends($page->layout)

@php
    $entryTranslation = $page->translateOrNew(app()->getLocale());
@endphp

@section('meta')
    @foreach($entryTranslation->metaTags as $metaTag)
        @if($metaTag->value)
            <meta @if($metaTag->property)property="{{ $metaTag->property }}"@endif @if($metaTag->name)name="{{ $metaTag->name }}"@endif value="{{ $metaTag->value }}">
        @endif
    @endforeach
@endsection

@section('content')
    @foreach($entryTranslation->contentBlocks->sortBy('order') as $contentBlock)

        @php
        $frontendTemplate = $contentBlock->config->frontend_template;
        @endphp

        @include($frontendTemplate, [
            'contentBlock' => $contentBlock
        ])
    @endforeach
@endsection
