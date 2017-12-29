@if(count($languages) > 1)
    @include('translate::_partials._nav_tabs', [
        'idPrefix' => 'basic-data-'
    ])

    @include('content::module_content.entries.form.revisions')
@endif

<!-- Tab panes -->
<div class="tab-content {{ count($languages) > 1 ? '' : 'no-padding' }}">
    @foreach($languages as $language)
        <div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}" id="basic-data-{{ $language->iso_code }}">
            @php
                if(isset($entry)) {
                    $entryTranslation = $entry->translations->where('locale', $language->iso_code)->first();
                    $entryTranslation = $entryTranslation ? $entryTranslation : (new \Modules\Content\Translations\EntryTranslation());
                }
            @endphp
            @include('content::module_content.entries.form.header')
            @include('content::module_content.entries.form.widgets')
            @include('content::module_content.entries.form.meta_tags')
        </div>
    @endforeach
</div>

@include('content::module_content.entries.form.footer')
