@php
    $name = isset($name) ? $name : null;
    $value = isset($value) ? $value : null;
@endphp

<div class="form-group no-margin-bottom">
    <div class="">
        {!! Form::textarea(
            $name,
            $value,
            ['class' => 'summernote']
        ) !!}
    </div>
</div>
