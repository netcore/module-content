@extends('admin::layouts.master')

@section('styles')
    <style>
    </style>
@endsection

@section('scripts')
    <script>
        $(function() {

            var o = $('#single-entries-datatable');
            var dataTable = $(o).DataTable({
                processing: true,
                serverSide: true,
                ajax: $(o).data('ajax'),
                //sDom: 'lftip',
                responsive: true,
                //order: [[1, "asc"]],
                columns: [

                    { data: 'name', name: 'name', orderable: true, searchable: false},
                    { data: 'slug', name: 'slug', orderable: false, searchable: false},
                    { data: 'content', name: 'content', orderable: false, searchable: false},
                    { data: 'updated_at', name: 'updated_at', orderable: false, searchable: false, class: 'text-center', width: '150px'},
                    { data: 'is_active', name: 'is_active', orderable: false, searchable: false, class: 'text-center', width: '100px'},
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center', width: '150px'},
                ],
                drawCallback: function(){

                    // Init switchery
                    $('.single-entries-changeable-state').each(function(i, switcher) {
                        new Switchery(switcher);
                    });
                }
            });

            $('#single-entries-datatable_wrapper .table-caption').text('Single pages');
        });
    </script>
@endsection

@section('content')
    <ol class="breadcrumb page-breadcrumb">
        <li><a href="{{url('/admin')}}">Admin</a></li>
        <li>
            <a href="{{crudify_route('index')}}">
                Content
            </a>
        </li>
    </ol>

    <div class="page-header">
        <h1>
            <span class="text-muted font-weight-light">
                <i class="page-header-icon ion-ios-keypad"></i>
                Content
            </span>
        </h1>
    </div>

    @include('content::module_content.index.channels_table')

    <hr>

    @include('content::module_content.index.single_entries_table')

    <br> <br> <br>

@endsection
