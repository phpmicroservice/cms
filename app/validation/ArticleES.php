<?php

namespace app\validation;

use app\model\article;
use pms\Validation;

class ArticleES extends Validation
{
    //定义验证规则
    protected $rule = [
        'status' => [
            'in' => [
                "domain" => [1, 0, -1],
            ]
        ],
    ];

    protected function initialize()
    {
        $this->add_exist('id', [
            'class_name_list' => new article()
        ]);
        return parent::initialize();
    }


}