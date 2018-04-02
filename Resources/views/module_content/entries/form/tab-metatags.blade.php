@php
    $existingMetaTags = $entryTranslation->metaTags;
@endphp

@foreach($configuredMetaTags as $configuredMetaTag)

    @php
        $name = array_get($configuredMetaTag, 'name');
        $property = array_get($configuredMetaTag, 'property');
        $type = $name ? 'name' : 'property';
        $typeValue = $name ? $name : $property;

        $existingMetaTag = $existingMetaTags->where($type, $typeValue)->first();
        $value = $existingMetaTag ? $existingMetaTag->value : '';
    @endphp

    <tr>
        <td>
            {{ $typeValue }}
        </td>
        <td class="padding-5">
            {!! Form::text('translations['.$language->iso_code.'][meta_tags]['.$typeValue.']', $value, [
                'class' => 'form-control',
            ]) !!}
        </td>
    </tr>
@endforeach