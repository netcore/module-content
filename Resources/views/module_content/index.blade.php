@extends('admin::layouts.master')

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

    <div class="panel">
        <div class="panel-heading">
            <div class="panel-title">
                Channels
            </div>
        </div>
        <div class="panel-body">
            <div class="table-primary">
                <table
                    class="table table-bordered datatable"
                    id="channels-datatable"
                >
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Active</th>
                        <th>Slug</th>
                        <th>Entries</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($channels as $channel)
                            <tr>
                                <td>
                                    {{ $channel->name }}
                                </td>
                                <td>
                                    <input
                                        class="changable-state"
                                        type="checkbox"
                                        data-render="switchery"
                                        data-theme="default"
                                        data-id="{{ $channel->id }}"
                                        data-model="{{ \Modules\Content\Models\Channel::class }}"
                                        data-switchery="true"
                                        {{ $channel->is_active ? 'checked' : '' }}
                                    />
                                </td>
                                <td>
                                    @foreach($channel->translations as $translation)
                                        <b>{{ strtoupper($translation->locale) }}:</b>
                                        {{ $translation->slug }}
                                        <br>
                                    @endforeach
                                </td>
                                <td>
                                    {{ $channel->entries()->count() }}
                                </td>
                                <td>
                                    <a href="{{ route('content::channels.edit', $channel) }}" class="btn btn-primary btn-xs">
                                        Edit channel
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{--
    @foreach($sections->sortBy('order') as $section)
        <div class="panel">
            <div class="panel-heading">
                <div class="panel-title">
                    {{ $section->name }}
                </div>
            </div>
            <div class="panel-body">
                <div class="table-primary">
                    <table
                        class="table table-bordered datatable"
                        data-ajax="{{ route('content::sections.pagination') }}"
                    >
                        <thead>
                            <tr>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
    --}}

@endsection

@section('scripts')
    <script>
        $(function() {

            return;

            $('.datatable').each(function(i, o){

                var table = $(o).dataTable({
                    columnDefs: [
                        { orderable: false, targets: -1 }
                    ]
                });

                var dataTable = $(o).DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: $(o).data('ajax'),
                    sDom: 'lftip',
                    responsive: true,
                    //order: [[5, "desc"]],
                    columns: [

                            // channel vai arī entry
                            // name, updated_at, actions

                        { data: 'name', name: 'name'},
                        { data: 'last_name', name: 'last_name'},
                        { data: 'phone', name: 'phone'},
                        { data: 'email', name: 'email' },
                        { data: 'status', name: 'status' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false, width: '10%', class: 'text-center' }
                    ]
                });

            });

            /*
            var table = $('#datatables').dataTable({
                columnDefs: [
                    { orderable: false, targets: -1 }
                ]

            });

            $('#datatables_wrapper .table-caption').text('Some header text');
            $('#datatables_wrapper .dataTables_filter input').attr('placeholder', 'Search...');
            */
        });
    </script>
@endsection