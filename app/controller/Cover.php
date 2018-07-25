<?php

namespace app\controller;

use app\Controller;

/**
 * 封面相关 和 主图
 * Class Cover
 * @package app\controller
 */
class Cover extends Controller
{

    /**
     * 设置封面
     */
    public function setcover()
    {
        $file_id = $this->getData('file_id');
        $article_id = $this->getData('article_id');
        $server = new \app\logic\Cover();
        $re = $server->setcover($article_id, $file_id);
        $this->send($re);
    }

    /**
     * 获取封面信息
     */
    public function info()
    {
        $article_id = $this->getData('article_id');
        $server = new \app\logic\Cover();
        $re = $server->info($article_id);
        $this->send($re);

    }
}