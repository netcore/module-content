<?php

namespace Modules\Content\PassThroughs\ContentBlock;

use Modules\Content\Models\ContentBlock;
use Modules\Content\Modules\Widget;
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
        return array_get($this->all(), $name);
    }

    /**
     * @return mixed
     */
    public function all()
    {
        $widget = widgets()->where('key', $this->contentBlock->widget)->first();
        $config = $widget ? $widget->config : [];

        return $config;
    }

}
