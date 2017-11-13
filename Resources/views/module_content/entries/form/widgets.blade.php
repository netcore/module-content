
@php
    if(isset($entry)) {
        $entryTranslation = $entry->translations->where('locale', $language->iso_code)->first();
        $entryTranslation = $entryTranslation ? $entryTranslation : (new \Modules\Content\Translations\EntryTranslation());
    }
@endphp

<div class="widgets-container"> {{-- TODO This used to be id --}}

    <div class="no-widgets" {{-- TODO this used to be ID --}}
        @if(isset($entry))
        {{ $entryTranslation->contentBlocks->count() ? 'hidden' : '' }}
        @endif
    >
        Currently there are no {{ strtoupper($language->iso_code) }} widgets added!
    </div>

    <table
        class="table widgets-table" {{-- TODO this used to be ID --}}
        {{--
        id="widgets-table"
        --}}
    >
        <tbody>
        @if(isset($entry))
            @foreach($entryTranslation->contentBlocks->sortBy('order') as $contentBlock)

                @php
                    $template = $contentBlock->config->name;
                    if($contentBlock->config->backend_template) {
                        $template  = view(
                            $contentBlock->config->backend_template, $contentBlock->compose()->backend($language)
                        )->render();
                    }
                @endphp

                @include('content::module_content.entries.form.widget_tr_template', [
                    'contentBlockId' => $contentBlock->id,
                    'key'            => $contentBlock->config->key,
                    'javascriptKey'  => $contentBlock->config->javascript_key,
                    'withBorder'     => $contentBlock->config->backend_with_border ? 'with-border' : '',
                    'template'       => $template,
                    'widgetName'     => $contentBlock->config->name
                ])
            @endforeach
        @endif
        </tbody>
    </table>
</div>
