<div class="" style="padding: 10px;">
    @if($config['is_empty'] == true && isset($config['content']))
        <div class="alert alert-info">
            {!! $config['content'] !!}
        </div>
    @endif

    @if(isset($mainFields) && count($mainFields))
        <div class=" js-main-fields-block clearfix">
            @foreach($mainFields as $field)
                @php
                    $fieldName = array_get($field, 'name');
                    $fieldLabel = array_get($field, 'label');
                    $fieldType = array_get($field, 'type');
                    $fieldValue = isset($contentBlock) ? ($fieldType === 'file' ? $contentBlock->getStaplerObj($fieldName) : $contentBlock->getField($fieldName)) : '';
                    $fieldStyles = array_get($field, 'styles');
                    $fieldOptions = (array) array_get($field, 'options');

                    $imageWidth = array_get($fieldStyles, 'image_width');
                    $notRequired = array_get($fieldStyles, 'not_required', 0);
                @endphp
                <div class="form-group col-md-12">
                    <div class="col-md-2 text-align-right">
                        <label for="" class="form-label">{{ ucfirst($fieldLabel) }}</label>
                    </div>
                    <div class="col-md-8">
                        @if($fieldType === 'file')
                            <input
                                    type="file"
                                    data-name="{{ $fieldName }}"
                                    data-field="{{ $fieldName }}"
                                    class="form-control js-input js-block-file"
                                    data-image-width="{{ $imageWidth }}"
                                    data-not-required="{{ $notRequired }}"
                            >
                            @if($fieldValue)
                                <img src="{{ $fieldValue->url('original') }}"
                                     alt="{{ ucfirst($fieldLabel) }}"
                                     style="max-width: 100%; max-height: 100px; margin-top: 10px;">
                            @endif
                        @elseif($fieldType === 'textarea')
                            <textarea
                                    maxlength="8000000"
                                    data-field="{{ $fieldName }}"
                                    data-locale="{{ $language->iso_code }}"
                                    data-not-required="{{ $notRequired }}"
                                    data-name="{{ $fieldName }}"
                                    class="form-control image-blocks-summernote width-800 js-input"
                            >
                                @if($fieldValue)
                                    {!! $fieldValue !!}
                                @endif
                            </textarea>
                        @elseif($fieldType === 'checkbox')
                            <input
                                    type="checkbox"
                                    value="1"
                                    data-field="{{ $fieldName }}"
                                    data-locale="{{ $language->iso_code }}"
                                    data-not-required="{{ $notRequired }}"
                                    data-name="{{ $fieldName }}"
                                    @if($fieldValue && (int)$fieldValue === 1)
                                    checked
                                    @endif
                                    class=" js-input"
                            >
                        @elseif($fieldType === 'select')
                            <select
                                    data-field="{{ $fieldName }}"
                                    data-locale="{{ $language->iso_code }}"
                                    data-not-required="{{ $notRequired }}"
                                    data-name="{{ $fieldName }}"
                                    class="form-control js-input"
                            >
                                @php
                                    $selectData = array_get($field, 'select_data');
                                @endphp
                                @foreach($selectData as $id => $name)
                                    <option {{ (int)$fieldValue === $id ? 'selected' : '' }} value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        @else
                            <input
                                    type="text"
                                    maxlength="191"
                                    data-field="{{ $fieldName }}"
                                    data-locale="{{ $language->iso_code }}"
                                    data-not-required="{{ $notRequired }}"
                                    data-name="{{ $fieldName }}"
                                    class="form-control js-input"
                                    value="@if($fieldValue){!! $fieldValue !!}@endif"
                            >
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($fields)
        <div class="panel" style="margin: 5px;">
            <div class="panel-heading">
                <div class="panel-title">
                    Blocks
                </div>
            </div>
            @include('content::module_content.widgets.widget_blocks.backend_list')
        </div>
        @include('content::module_content.widgets.widget_blocks.backend_form')
    @endif
</div>
