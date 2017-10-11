<?php

namespace Modules\Content\PassThroughs\ContentBlock;

use Modules\Content\Models\ContentBlock;
use Modules\Content\PassThroughs\PassThrough;

class Config extends PassThrough
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
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return array_get($this->fromConfig(), $name);
    }

    /**
     * @return mixed
     */
    private function fromConfig() {
        $config = collect(config('module_content.widgets'))->where('key', $this->contentBlock->widget)->first();
        return $config;
    }

}
