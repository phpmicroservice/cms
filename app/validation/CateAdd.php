<?php

namespace app\validation;

use app\model\article_category;

class CateAdd extends \pms\Validation
{

    protected function initialize()
    {
        $this->add_uq('name', [
            'message' => 'name-uq',
            'model' => new article_category(),
            'attribute' => 'name'
        ]);
        return parent::initialize();
    }
}