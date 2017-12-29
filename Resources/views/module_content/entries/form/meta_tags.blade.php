
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#seo-{{ $entryTranslation->id }}" style="position:absolute; bottom:-62px; left:0px;">
    SEO, meta tags
</button>

<!-- Modal -->
<div class="modal fade" id="seo-{{ $entryTranslation->id }}" tabindex="-1" role="dialog" aria-labelledby="seo-{{ $entryTranslation->id }}-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="seo-{{ $entryTranslation->id }}-label">Modal title</h4>
            </div>
            <div class="modal-body">
                <table style="width:100%;">
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
                                    'class' => 'form-control datepicker',
                                    'style' => 'width:100%;',
                                ]) !!}
                            </td>
                        </tr>
                    @endforeach
                </table>

                <br>
                <p>
                    <b style="color:red;">NOTE</b> - after editing fields and closing this modal, you still need to press large "Save" button at bottom-right corner of page.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>
