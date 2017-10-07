<div class="table-primary">

    <table
        class="table table-bordered datatable"
        id="single-entries-datatable"
        data-ajax="{{ route('content::entries.pagination') }}"
    >
        <thead>
        <tr>
            <th>Name</th>
            <th>Slug</th>
            <th>Content</th>
            <th>Updated at</th>
            <th>Active</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
