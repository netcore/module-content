<?php

namespace Modules\Content\Widgets\BackendWorkers;

use Modules\Content\Models\ContentBlock;

interface BackendWorkerInterface
{
    public function store($contentBlock);

    public function delete(ContentBlock $contentBlock);
    
    public function backendTemplateComposer(Array $data);
}
