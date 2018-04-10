{{--@if(isset($entryTranslation))--}}
@if(isset($entry))
    @php
        $i=1;
    @endphp
    @foreach($languages as $language)
        <div class="col-md-12 localization-content locale-{{$language->iso_code}}"
            @if($i != 1) style="display:none;" @endif>


            @php
                $entryTranslation = $entry->translations->where('locale', $language->iso_code)->first();
                if(!$entryTranslation) {
                    continue;
                }
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

                <div class="form-group">
                    <label for="{{ $typeValue }}"><span class="label">{{$language->iso_code}}</span> {{ $typeValue }}</label>

                    {!! Form::text('translations['.$language->iso_code.'][meta_tags]['.$typeValue.']', $value, [
                        'class' => 'form-control',
                        'id' => $typeValue
                    ]) !!}
                </div>
            @endforeach
        </div>
        @php($i++)
    @endforeach
@else
    <div class="alert alert-info">
        You need to create entry in order to add meta tags
    </div>
@endif

{{--@endif--}}