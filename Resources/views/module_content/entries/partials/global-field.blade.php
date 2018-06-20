@php
    $fieldType = object_get($field, 'type');
    $fieldName = object_get($field, 'key');
    $fieldLabel = object_get($field, 'title');

@endphp
@if($fieldType == 'file')
    <label for="" class="form-label">{{ ucfirst($fieldLabel) }}</label>
    <div class="form-group no-margin">
        <input
                type="file"
                data-name="html-block-images[]"
                class="form-control form-input inline"
                multiple
        >
    </div>
@else
    <div class="form-group">
        <label for="" class="form-label">{{ ucfirst($fieldLabel) }}</label>

        @if($fieldType == 'textarea')
            <textarea
                    maxlength="8000000"
                    rows="10"
                    name="global_field[{{ $fieldName }}]"
                    class="form-control image-blocks-summernote width-800 js-input"
            >
                {{ isset($entry) ? object_get($entry->globalFields->where('key', $fieldName)->first(), 'value') : null }}
            </textarea>
        @elseif($fieldType == 'checkbox')
            <input
                    type="checkbox"
                    value="1"
                    name="global_field[{{ $fieldName }}]"
                    class="js-input"
            >
        @elseif($fieldType == 'select')
            @php
                $data = json_decode($field->data);
                $selectData = object_get(json_decode($field->data), 'items', []);

                if(isset($data->relation) && $data->relation) {
                    $model = $data->relation_class::get();

                    if(isset($data->relation_options) && isset($data->relation_options->relation)) {
                        $where = $data->relation_options->relation;
                        $model = $data->relation_class::whereHas($data->relation_options->relation->name, function ($q) use ($where) {
                            $q->where($where->where[0], $where->where[1]);
                        })->get();
                    }
                    $selectData = $model->pluck($data->relation_columns[1], $data->relation_columns[0]);
                }


            @endphp
            <select
                    name="global_field[{{ $fieldName }}]"
                    class="form-control js-input"
            >
                @foreach($selectData as $id => $name)
                    <option {{  (isset($entry->globalFields) ? object_get($entry->globalFields->where('key', $fieldName)->first(), 'value', null) : null) == $id ? 'selected' : '' }} value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        @elseif($fieldType == 'number')
            <input
                    type="number"
                    name="global_field[{{ $fieldName }}]"
                    class="form-control"
                    value="{{ isset($entry->globalFields) ? object_get($entry->globalFields->where('key', $fieldName)->first(), 'value') : null }}"
            >
            <div class="error-span"></div>
        @else
            <input
                    type="text"
                    name="global_field[{{ $fieldName }}]"
                    class="form-control"
                    value="{{ isset($entry->globalFields) ? object_get($entry->globalFields->where('key', $fieldName)->first(), 'value') : null }}"
            >
            <div class="error-span"></div>
        @endif
    </div>

@endif