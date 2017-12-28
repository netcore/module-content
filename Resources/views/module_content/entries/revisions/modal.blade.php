@if($revisions->count())
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Created at</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>
            @foreach($revisions as $revision)
                <tr>
                    <td>
                        {{ $revision->created_at->format('d.m.Y H:i:s') }}
                    </td>
                    <td>

                        {{--
                        <a
                            href="{{ route('content::entries.preview', $revision) }}"
                            target="_blank"
                            class="btn btn-xs btn-info disabled"
                        >
                            Preview
                        </a>
                        --}}

                        <a
                            href="{{ route('content::entries.edit', $revision) }}"
                            class="btn btn-xs btn-success"
                        >
                            Load in editor
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
@else
    <small>
        There are no revisions...
    </small>
@endif
