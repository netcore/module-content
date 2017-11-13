@if(count($languages) > 1)
    @include('translate::_partials._nav_tabs', [
        'idPrefix' => 'basic-data-'
    ])
@endif

{{--
<div class="row">
    <div class="col-xs-12">

        <div class="form-group">
            <label>Date</label>
            {!! Form::text('published_at', (isset($entry) ? $entry->published_at->format('d.m.Y') : date('d.m.Y')), ['class' => 'form-control datepicker']) !!}
            <span class="error-span"></span>
        </div>

    </div>

    <div class="col-xs-6">

    </div>
</div>
--}}

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
