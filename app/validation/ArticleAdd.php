<?php

namespace app\validation;

use pms\Validation;

/**
 * 文章增加验证
 * Class ArticleAdd
 * @package app\validation
 */
class ArticleAdd extends Validation
{
    //定义验证规则
    protected $rule = [
        'name' => [
            'stringLength' => [
                "message" => "name",
                'min' => 0,
                'max' => 15
            ]
        ],
        'title' => [
            'required' => [
                "message" => "title",
            ],
            'stringLength' => [
                "message" => "stringLength",
                'min' => 2,
                'max' => 20
            ]
        ],
        'category_id' => [
            'required' => [
                "message" => "required",
            ],
            'Validator' => [
                'name' => 'app\validator\CateValidator',
                "message" => "category_id"
            ],
        ],
        'description' => [
            'required' => [
                "message" => "required",
            ],
            'stringLength' => [
                "message" => "stringLength",
                'min' => 1,
                'max' => 50
            ]
        ],

        'can_reply' => [
            'required' => [
                "message" => "can_reply",
            ],
            'in' => [
                'domain' => [1, 0],
                "message" => "can_reply",
            ]
        ]
    ];

}