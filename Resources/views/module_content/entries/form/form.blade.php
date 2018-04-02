

<div class="row">
    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
        <div class="panel">
            <div class="panel-heading">
                <div class="panel-title">
                    Edit page
                </div>
            </div>
            <div class="panel-body position-relative">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active">
                        <a href="#tab-content" role="tab" data-toggle="tab">
                            Content
                        </a>
                    </li>

                    <li>
                        <a href="#tab-values" role="tab" data-toggle="tab">
                            Values
                        </a>
                    </li>

                    <li>
                        <a href="#tab-seo" role="tab" data-toggle="tab">
                            SEO
                        </a>
                    </li>

                    @if(isset($entry) && $revisionsEnabled)
                        <li>
                            <a href="#tab-history" role="tab" data-toggle="tab">
                                History
                            </a>
                        </li>
                    @endif
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tab-content">
                        @include('content::module_content.entries.form.tab-content')
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab-values">
                        @include('content::module_content.entries.form.tab-values')
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab-seo">
{{--                        @include('content::module_content.entries.form.tab-metatags')--}}
                    </div>

                    @if(isset($entry) && $revisionsEnabled)
                        <div role="tabpanel" class="tab-pane" id="tab-history">
                            @include('content::module_content.entries.form.tab-history')
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
        @include('content::module_content.entries.form.info_panel')
    </div>
</div>