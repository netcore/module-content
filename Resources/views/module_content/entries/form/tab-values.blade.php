@if(isset($channel))
    <div class="panel">
        <div class="panel-heading">
            Translateable values
            <div class="pull-right">
                <button class="btn btn-xs btn-success js-toggle-panel-body">Show/hide</button>
            </div>
        </div>
        <div class="panel-body">
            @php($i=1)
            @foreach($languages as $language)
                @foreach($channel->fields->where('is_global', 0) as $field)
                    <div class="col-md-12 localization-content locale-{{$language->iso_code}}"
                         @if($i != 1) style="display:none;" @endif>
                        @include('content::module_content.entries.partials.field')
                    </div>
                @endforeach
                @php($i++)
            @endforeach
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading">
            Global values
            <div class="pull-right">
                <button class="btn btn-xs btn-success js-toggle-panel-body">Show/hide</button>
            </div>
        </div>
        <div class="panel-body" style="display: none;">
            @foreach($channel->fields->where('is_global', 1) as $field)
                @include('content::module_content.entries.partials.global-field')
            @endforeach
        </div>
    </div>
@endif