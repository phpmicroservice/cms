<?php

namespace app\controller;

use app\Controller;

/**
 * 服务间的使用
 * Class Service
 * @package app\controller
 */
class Service extends Controller
{


    /**
     * 验证是否存在
     */
    public function va_ex()
    {
        $id=$this->getData('id');
        $server = new \app\logic\Article();
        $re = $server->va_ex($id);
        $this->send($re);
    }

}