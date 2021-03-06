<?php

namespace app\filterTool;

use pms\FilterTool\FilterTool;

/**
 * 分类编辑的过滤器
 * Class CateEdit
 * @package app\filterTool
 */
class CateEdit extends FilterTool
{

    protected function initialize()
    {
        $this->_Rules = [
            ['id', 'int'],
            ['title', 'string'],
            ['name', 'string'],
            ['pid', 'int'],
            ['meta_title', 'string'],
            ['keywords', 'string'],
            ['description', 'string'],
            ['allow_publish', 'int'],
            ['display', 'int'],
            ['check', 'int'],
        ];
        parent::initialize(); // TODO: Change the autogenerated stub
    }

}