@if(isset($channel) && $channel->allow_attachments)
    <div class="panel">
        <div class="panel-heading">
            <div class="panel-title">
                Attachments
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>Attachment (You can select multiple)</label>
                <br>
                {!! Form::file('attachments[]', [
                    'class' => 'form-control form-input attachment',
                    'multiple'
                ]) !!}

                <span class="error-span"></span>


                @if(isset($entry) && $entry->attachments->count() )
                    <br>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Attachment</th>
                            <th style="width: 15%">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($entry->attachments as $attachment)
                            <tr>
                                <td><img src="{{ $attachment->image->url('original') }}" alt=""
                                            style="max-width: 100px;"></td>
                                <td>
                                    <a
                                            class="btn btn-xs btn-danger confirm-action"
                                            data-title="Confirmation"
                                            data-text="Attachment will be deleted. Are you sure?"
                                            data-confirm-button-text="Delete"
                                            data-method="DELETE"
                                            data-href="{{ route('content::entries.destroy_attachment', $attachment->id) }}"
                                            data-success-title="Success"
                                            data-success-text="Attachment was deleted"
                                            data-refresh-page-on-success
                                    >
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endif