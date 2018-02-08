<div class="row">
    <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
        <div class="panel">
            <div class="panel-heading">
                <div class="panel-title">
                    Entry data
                </div>
            </div>
            <div class="panel-body">

            @if(count($languages) > 1)
                @include('translate::_partials._nav_tabs', [
                    'idPrefix' => 'basic-data-'
                ])

                @include('content::module_content.entries.form.revisions')
            @endif
            <!-- Tab panes -->
                <div class="tab-content {{ count($languages) > 1 ? '' : 'no-padding' }}">
                    @foreach($languages as $language)
                        <div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}"
                             id="basic-data-{{ $language->iso_code }}">
                            @php
                                if (isset($entry)) {
                                    $entryTranslation = $entry->translations->where('locale', $language->iso_code)->first();
                                    $entryTranslation = $entryTranslation ? $entryTranslation : (new \Modules\Content\Translations\EntryTranslation());
                                }
                            @endphp

                            @include('content::module_content.entries.form.header')
                            @include('content::module_content.entries.form.widgets')
                            @include('content::module_content.entries.form.meta_tags')
                        </div>
                    @endforeach
                </div>

                @include('content::module_content.entries.form.footer')
            </div>
        </div>

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
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
        @include('content::module_content.entries.form.info_panel')
    </div>
</div>