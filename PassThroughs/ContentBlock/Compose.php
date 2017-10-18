<?php

namespace Modules\Content\PassThroughs\ContentBlock;

use Modules\Content\Models\ContentBlock;
use Modules\Content\PassThroughs\PassThrough;

class Compose extends PassThrough
{
    /**
     * @var ContentBlock
     */
    private $contentBlock;

    /**
     * HasPermissionTo constructor.
     *
     * @param ContentBlock $contentBlock
     */
    public function __construct(ContentBlock $contentBlock)
    {
        $this->contentBlock = $contentBlock;
    }

    /**
     * @return array
     */
    public function backend(): Array
    {
        $contentBlock = $this->contentBlock;

        $templateData = [];
        $backendWorker = $contentBlock->config->backend_worker;
        if($backendWorker) {
            $worker = new $backendWorker($contentBlock->config->all());
            $templateData = $worker->backendTemplateComposer(
                $contentBlock->data
            );
        }

        return $templateData;
    }

}
