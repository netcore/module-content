$(function() {

    var onTabChange = function(activeTab){

        // Entries datatable
        var selector = '.tab-pane.active .datatable';
        if ( $.fn.DataTable.isDataTable(selector) ) {
            return;
        }

        var caption = $(selector).data('caption');

        var dataTable = $(selector).DataTable({
            processing: true,
            serverSide: true,
            ajax: $(selector).data('ajax'),
            //sDom: 'lftip',
            responsive: true,
            //order: [[1, "asc"]],
            columns: [

                { data: 'created_at', name: 'created_at', orderable: false, searchable: false, class: 'vertical-align-middle width-150'},
                { data: 'title', name: 'title', orderable: false, searchable: false, class: 'width-250'},
                { data: 'slug', name: 'slug', orderable: false, searchable: false, class: 'width-250'},
                { data: 'content', name: 'content', orderable: false, searchable: false, class: ''},
                { data: 'updated_at', name: 'updated_at', orderable: false, searchable: false, class: 'text-center vertical-align-middle width-150'},
                { data: 'is_active', name: 'is_active', orderable: false, searchable: false, class: 'text-center vertical-align-middle width-100'},
                { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center vertical-align-middle width-150'}
            ],
            drawCallback: function(){

                // Init switchery
                $('.changeable-state:visible').each(function(i, switcher) {
                    new Switchery(switcher);
                });
            }
        });

        var selector = '.tab-pane.active .table-header .table-caption';
        $(selector).text(caption);

        var selector = '.tab-pane.active input[type=search]';
        $(selector).attr('placeholder', 'Search...');
    };

    // When page loads, we initialize first tab
    var activeTab = $('.nav-tabs li.active:first');
    onTabChange(activeTab);

    // When tabs are clicked, we load info there
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var activeTab = $('.nav-tabs li.active:first');
        onTabChange(activeTab);
    });
});
