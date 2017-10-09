@include('crud::nav_tabs')

<!-- Tab panes -->
<div class="tab-content">
    @foreach($languages as $language)
        <div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{ $language->iso_code }}">

            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        <label>Title</label>
                        <div class="">
                            {!! Form::text('translations['.$language->iso_code.'][title]', trans_model((isset($entry) ? $entry : null), $language, 'title'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group{{ $errors->has('slug') ? ' has-error' : '' }}">
                        <label>Slug</label>
                        (Automatically generated if left empty)
                        <div class="">
                            {!! Form::text('translations['.$language->iso_code.'][slug]', trans_model((isset($entry) ? $entry : null), $language, 'slug'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Content blocks --}}
            <div id="widgets-container">

                <div id="no-widgets">
                    Currently there is no content. Please add at least one block!
                </div>

                <table
                        class="table"
                        id="widgets-table"
                >
                    <tbody>

                    @php
                        $ids = [];
                    @endphp

                    @foreach( $ids as $id )
                        <tr
                                data-id="{{ $id }}"
                        >
                            <td class="handle">
                                Widget here
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>

            {!! Form::select(null, $widgetOptions, null, [
                'class' => 'form-control width-150 inline',
                'id' => 'select-widget'
            ]) !!}
            <a class="btn btn-xs btn-success" id="add-widget-button">Add widget</a>
        </div>
    @endforeach
</div>
