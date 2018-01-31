<div class="panel">
    <div class="panel-heading">
        <h4 class="panel-title">Information</h4>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label>Published?</label><br>
            <span class="hidden-switchery" hidden>
                {!! Form::checkbox('is_active', 1, (isset($entry) ? null : 0), [
                    'class' => 'switchery'
                ]) !!}
            </span>
        </div>

        <div class="form-group">
            <label>Published At</label><br>
            {!! Form::text('published_at', (isset($entry) ? $entry->published_at->format('d.m.Y') : date('d.m.Y')), ['class' => 'form-control datepicker']) !!}
            <span class="error-span"></span>
        </div>

        <div class="form-group">
            @if(!$channel)
                <label>Homepage?</label><br>
                <span class="hidden-switchery" hidden style="margin-right:10px;">
                 {!! Form::checkbox('is_homepage', 1, (isset($entry) ? null : 0), [
                     'class' => 'switchery'
                 ]) !!}
                </span>
            @endif
        </div>

        @php
            $multipleLayouts = count($layoutOptions) > 1;;
        @endphp

        <div class="form-group" {{ $multipleLayouts ? '' : 'hidden' }}
        >
            <label>Layout</label><br>
            {!! Form::select('layout', $layoutOptions, null, ['class' => 'form-control']) !!}
            <span class="error-span"></span>
        </div>

        @if(isset($entry) && config('netcore.module-content.revisions_enabled', true))

            @php
                $countOfRevisions = $entry->children()->whereType('revision')->count();
            @endphp

            Revisions: <strong>{{ $countOfRevisions }}</strong>
            <a data-href="{{ route('content::entries.revisions', $entry) }}"
               data-remote="false"
               data-toggle="modal" data-target="#revisions-modal"
               class="trigger-revisions-modal cursor-pointer btn btn-default btn-xs">
                <i class="fa fa-folder-open"></i> Browse
            </a>
        @endif

        <hr>

        @if (isset($entry))
            @if($entry->type == 'current')
                <a
                        class="btn btn-md btn-success pull-right submit-button"
                        data-save-as="current"
                >
                            <span class="loading" hidden>
                                <span class="fa fa-gear fa-spin"> </span>
                                Please wait...
                            </span>

                    <span class="not-loading">
                                <i class="fa fa-save"></i> Save
                            </span>
                </a>

                <a href="{{ route('content::content.index') }}{{ $channel ? '?channel='.$channel->slug : '' }}"
                   class="btn btn-md btn-default">
                    <i class="fa fa-undo"></i> Back
                </a>

            @elseif($entry->type == 'draft')

                <a
                        class="btn btn-md btn-success pull-right submit-button"
                        data-save-as="draft"
                >
                            <span class="loading" hidden>
                                <span class="fa fa-gear fa-spin"> </span>
                                Please wait...
                            </span>

                    <span class="not-loading">
                                <i class="fa fa-save"></i> Save, don't publish
                            </span>
                </a>

                <a
                        class="btn btn-md btn-danger pull-right submit-button"
                        data-save-as="current"
                >
                            <span class="loading" hidden>
                                <span class="fa fa-gear fa-spin"> </span>
                                Please wait...
                            </span>

                    <span class="not-loading">
                                <i class="fa fa-save"></i> Save and Publish
                            </span>
                </a>

                <a href="{{ route('content::content.index') }}{{ $channel ? '?channel='.$channel->slug : '' }}"
                   class="btn btn-md btn-default">
                    <i class="fa fa-undo"></i> Back
                </a>
            @endif
        @else
            <a
                    class="btn btn-md btn-success pull-right submit-button"
                    data-save-as="current"
            >
                <i class="fa fa-save"></i> Save
            </a>

            <a href="{{ route('content::content.index') }}{{ $channel ? '?channel='.$channel->slug : '' }}"
               class="btn btn-md btn-default">
                <i class="fa fa-undo"></i> Back
            </a>
        @endif
    </div>
</div>
