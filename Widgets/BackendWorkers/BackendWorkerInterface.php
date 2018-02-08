<?php

namespace Modules\Content\Widgets\BackendWorkers;

use Modules\Content\Models\ContentBlock;
use Netcore\Translator\Models\Language;

interface BackendWorkerInterface
{
    /**
     * @param array $frontendData
     * @return mixed
     */
    public function getErrors(Array $frontendData);

    /**
     * @param array $frontendData
     * @return mixed
     */
    public function store(Array $frontendData);

    /**
     * @param array $frontendData
     * @return mixed
     */
    public function update(Array $frontendData);

    /**
     * @param ContentBlock $contentBlock
     * @return mixed
     */
    public function delete(ContentBlock $contentBlock);

    /**
     * @param array    $data
     * @param Language $language
     * @return mixed
     */
    public function backendTemplateComposer(Array $data, Language $language);
}
