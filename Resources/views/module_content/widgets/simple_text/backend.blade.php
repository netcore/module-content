
<div class="form-group no-margin-bottom">
    {{--
    <label>Text</label>
    --}}
    <div class="">

        @php
            $name = null;
            $value = null;
        @endphp

        {!! Form::textarea(
            $name,
            $value,
            ['class' => 'summernote']
        ) !!}
    </div>
</div>
