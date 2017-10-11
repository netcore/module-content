
@php
    $id         = isset($id) ? $id : '{{ id }}';
    $key        = isset($key) ? $key : '{{ key }}';
    $withBorder = isset($withBorder) ? $withBorder : '{{ withBorder }}';
    $template   = isset($template) ? $template : '{{ template }}';
@endphp

<tr data-id="{{ $id }}" data-key="{{ $key }}">
    <td>
        <div class="template-container {{ $withBorder }}">

            <div class="template-container-header handle">
                <span class="fa fa-icon fa-arrows"></span>
                <a class="delete-widget">Delete</a>
            </div>

            <div class="template-container-body">
                {!! $template !!}
            </div>

        </div>
    </td>
</tr>
