@if(count($languages) > 1)
    @include('translate::_partials._nav_tabs', [
        'idPrefix' => 'basic-data-'
    ])
@endif

<!-- Tab panes -->
<div class="tab-content {{ count($languages) > 1 ? '' : 'no-padding' }}">
    @foreach($languages as $language)
        <div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}" id="basic-data-{{ $language->iso_code }}">
            @include('content::module_content.entries.form.header')
            @include('content::module_content.entries.form.widgets')
        </div>
    @endforeach
</div>

@include('content::module_content.entries.form.footer')
