<?php

namespace Modules\Content\Widgets\BackendWorkers;

use Modules\Content\Models\ContentBlock;

interface BackendWorkerInterface
{
    public function store(Array $frontendData);

    public function delete(ContentBlock $contentBlock);
    
    public function backendTemplateComposer(Array $data);
}
