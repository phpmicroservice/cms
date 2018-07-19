<?php

namespace app\controller;

use app\Controller;

/**
 * 服务间的使用
 * Class Service
 * @package app\controller
 */
class Server extends Controller
{

    /**
     * 验证是否存在
     */
    public function va_ex()
    {
        $id = $this->getData('id');
        $server = new \app\logic\Article();
        $re = $server->va_ex($id);
        $this->send($re);
    }

    /**
     *
     * 获取分类列表
     */
    public function cate_list()
    {
        $where = $this->getData();
        $server = new \app\logic\Cate();
        $re = $server->lists($where);
        $this->send($re);
    }

    public function cover_list()
    {
        $article_id_list = $this->getData('article_id_list');
        $server = new \app\logic\Cover();
        $re = $server->cover_list4id($article_id_list);
        $this->send($re);
    }


    public function article_list()
    {
        $article_id_list = $this->getData('article_id_list');
        $server = new \app\logic\Article();
        $re = $server->ids2list($article_id_list);
        $this->send($re);
    }

    /**
     *  文章列表
     */
    public function article_list4c()
    {
        $now_page = $this->getData('p', 1);
        $rows = $this->getData('r', 10);
        $where = $this->getData('where');
        $server = new \app\logic\Article();
        $re = $server->lists($where, $now_page, $rows);
        $this->send($re);
    }


    public function article_info()
    {
        $article_id = $this->getData('article_id');
        $with_content = $this->getData('with_content', false);
        $server = new \app\logic\Article();
        $re = $server->info2($article_id, $with_content);
        $this->send($re);
    }

}