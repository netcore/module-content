@if($channel->allow_attachments)
    <div class="panel">
        <div class="panel-heading">
            Attachments
            <div class="pull-right">
                <button class="btn btn-xs btn-success js-toggle-panel-body">Show/hide</button>
            </div>
        </div>
        <div class="panel-body" style="display: none;">
            <div class="form-group">
                <label>Attachment <small>(You can select multiple) or select one for media cover</small></label>
                <br>
                {!! Form::file('attachments[]', [
                    'class' => 'form-control form-input attachment',
                    'multiple'
                ]) !!}

                <span class="error-span"></span>
            </div>
            <div class="form-group">
                <label>Media <small>(Youtube video or iframe)</small></label>
                <br>
                {!! Form::text('media', '', [
                    'class' => 'form-control',
                ]) !!}

                @if(isset($entry) && $entry->attachments->count() )
                    <br>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Attachment/Cover</th>
                            <th>Media</th>
                            <th style="width: 15%;">Is featured</th>
                            <th style="width: 15%">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($entry->attachments as $attachment)
                            <tr>
                                <td>
                                    <img src="{{ $attachment->image->url('original') }}" alt=""
                                         style="max-width: 100px; max-height: 50px;">
                                </td>
                                <td>
                                    {{ $attachment->media }}
                                </td>
                                <td>
                                    <span class="hidden-switchery" hidden>
                                        <input type="checkbox" class="switchery js-toggle-attachment-featured" data-id="{{ $attachment->id }}" value="1" {{ $attachment->is_featured ? 'checked' : '' }}>
                                    </span>
                                </td>
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