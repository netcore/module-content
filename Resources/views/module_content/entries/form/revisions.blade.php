
@if(isset($entry))

    <div id="revisions-info-container">
        @if($entry->type == 'revision')
            <b class="error-span">ATTENTION - </b>
            You are viewing a revision of
            <a href="{{ route('content::entries.edit', $entry->parent_id) }}">
                this page
            </a>
            from {{ $entry->created_at->format('d.m.Y H:i:s') }}
        @else

            @php
                $countOfRevisions = $entry->children()->whereType('revision')->count();
            @endphp

            <!-- Link trigger modal -->
            <a data-href="{{ route('content::entries.revisions', $entry) }}" data-remote="false" data-toggle="modal" data-target="#revisions-modal" class="trigger-revisions-modal cursor-pointer">
                Revisions ({{ $countOfRevisions }})
            </a>
        @endif
    </div>

    <!-- Default bootstrap modal example -->
    <div class="modal fade" id="revisions-modal" tabindex="-1" role="dialog" aria-labelledby="revisions-modal-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="revisions-modal-label">Revisions</h4>
                </div>
                <div class="modal-body">
                    <span class="fa fa-gear fa-spin"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    {{--
                    <button type="button" class="btn btn-primary">Save changes</button>
                    --}}
                </div>
            </div>
        </div>
    </div>
@endif
