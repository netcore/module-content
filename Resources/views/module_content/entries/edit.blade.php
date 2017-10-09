@extends('admin::layouts.master')

@section('styles')
    <style>

        .width-150{
            width: 150px;
        }

        .text-align-right{
            text-align: right;
        }

        .handle{
            cursor: move; /* fallback if grab cursor is unsupported */
            cursor: grab;
            cursor: -moz-grab;
            cursor: -webkit-grab;
        }

        .handle:active{
            cursor: grabbing;
            cursor: -moz-grabbing;
            cursor: -webkit-grabbing;
        }

        #widgets-table tr{
            border:none;
        }

        #widgets-table td{
            border:none;
        }

        #widgets-container .template-container{
            border: 1px solid #d6d6d6;
            border-radius:3px;
            padding:0px;
        }

        #widgets-container .template-container-header{
            border-bottom: 1px solid #d6d6d6;
            padding: 0px 7px;
        }

        #widgets-container .template-container-header a{
            cursor:pointer;
            position: relative;
            top:0px;
            left:0px;
            display:inline-block;
            width:50px;
            text-align: center;
            height:100%;
        }

        #widgets-container .template-container-header .fa-arrows{
            margin-right:6px;
        }

        #widgets-container .template-container-body{
            padding:7px;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(function() {

            var widgets = {!! json_encode($widgetData) !!};

            var initSortable = function(){
                // Orderable shop images
                $('#widgets-table').sortable({
                    containerSelector: 'table',
                    itemPath : '> tbody',
                    itemSelector : 'tr',
                    handle : '.handle',
                    onDrop : function($item, container, _super, event) {

                        $item.removeClass(container.group.options.draggedClass).removeAttr("style");
                        $("body").removeClass(container.group.options.bodyClass);

                        var order = [];
                        var id;

                        $.each( $(container.el).find('tr'), function (i, tr) {
                            if( id = $(tr).data('id') ) {
                                order.push( id );
                            }
                        });

                        //$.post($('#images-table').data('order-route'), { order : order }, function() {

                        console.log(order);

                        $.growl.notice({
                            title : 'Veiksmīgi!',
                            message : 'Secība saglabāta'
                        });
                        //});
                    }
                });
                console.log('Sortable initialised!');
            };

            var hideOrShowCountMessage = function(){
                var count = $('#widgets-table tr').length;
                console.log(count);
                if(!count) {
                    $('#widgets-container #no-widgets').show();
                } else {
                    $('#widgets-container #no-widgets').hide();
                }
            };

            $('body').on('click', '#add-widget-button', function(){
                var key = $('#select-widget option:selected').val();
                var data = widgets[key];

                var id = '';
                var template = data.name;
                var html = '<tr data-id="'+id+'">';
                html += '<td>';
                html += '<div class="template-container">';

                html += '<div class="template-container-header handle">';
                html += '<span class="fa fa-icon fa-arrows"></span>' ;
                html += '<a class="delete-widget">Delete</a>' ;
                html += '</div>';

                html += '<div class="template-container-body">';
                html += template;
                html += '</div>';

                html += '</div>';
                html += '</td>';
                html += '</tr>';

                //var html = '<tr data-id="'+id+'" class="handle"><td><div class="template-container"><span class="fa fa-icon fa-arrows"></span>'+template+'</div></td><td> <a class="btn btn-xs btn-danger">Delete</a> </td></tr>';

                //html += '<tr class="spacer"> <td></td> <td></td> </tr>';

                console.log(template);

                if( $('#widgets-table tbody tr').length ) {
                    console.log('after');
                    $('#widgets-table tbody tr:last').after(html);
                } else {
                    console.log('html');
                    $('#widgets-table tbody').html(html);
                }

                hideOrShowCountMessage();
                initSortable();
            });

        });
    </script>
@endsection

@section('content')
    @include('admin::_partials._messages')

    {!! Form::model($entry, ['url' => crudify_route('update', $entry), 'method' => 'PUT']) !!}

        <div class="p-x-1">

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

                        <div class="form-group">
                            <label>Content</label>
                            <div class="">
                                {!! Form::textarea('translations['.$language->iso_code.'][content]', trans_model((isset($entry) ? $entry : null), $language, 'content'), ['class' => 'summernote']) !!}
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

            <button type="submit" class="btn btn-lg btn-success m-t-3 pull-xs-right">Save</button>

            <a href="{{ route('content::content.index') }}" class="btn btn-lg btn-default m-t-3 m-r-1 pull-xs-right">
                Back
            </a>

        </div>
    {!! Form::close() !!}
@endsection
