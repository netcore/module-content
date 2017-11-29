@extends($page->layout)
@section('content')

    @foreach($page->translateOrNew(app()->getLocale())->contentBlocks->sortBy('order') as $contentBlock)

        @php
        $frontendTemplate = $contentBlock->config->frontend_template;
        @endphp

        @include($frontendTemplate, [
            'contentBlock' => $contentBlock
        ])
    @endforeach

@endsection
