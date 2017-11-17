<?php

namespace Modules\Content\Widgets\BackendWorkers;

use Modules\Content\Models\ContentBlock;
use Netcore\Translator\Models\Language;

interface BackendWorkerInterface
{
    public function getErrors(Array $frontendData);

    public function store(Array $frontendData);

    public function update(Array $frontendData);

    public function delete(ContentBlock $contentBlock);
    
    public function backendTemplateComposer(Array $data, Language $language);
}
