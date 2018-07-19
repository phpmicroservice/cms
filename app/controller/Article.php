<?php

namespace app\controller;

use app\Controller;

/**
 * 文章控制器
 * Class Article
 * @package app\controller
 */
class Article extends Controller
{

    /**
     * 列表
     */
    public function index()
    {
        $where = [
            'user_id' => $this->getData('pid', 0),
            'cate_id' => $this->getData('cate_id', 0),
            'type_n' => $this->getData('type_n'),
            'search_key' => $this->getData('search_key'),
        ];
        $page = $this->getData('p', 1);
        $rows = $this->getData('rows', 10);
        $server = new \app\logic\Article();
        $re = $server->lists($where, $page, $rows);
        $this->send($re);
    }

    /**
     * 增加
     */
    public function add()
    {
        $data = $this->getData();
        $server = new \app\logic\Article();
        $server->setSwooleServer($this->swoole_server);
        $re = $server->add($this->user_id, $data);
        $this->send($re);
    }


    /**
     * 编辑属性
     */
    public function edit()
    {
        $data = $this->getData();
        $server = new \app\logic\Article();
        $re = $server->edit($this->user_id, $data);
        $this->send($re);
    }

    /**
     *
     */
    public function dele()
    {

    }

    /**
     *
     */
    public function info()
    {
        $id = $this->getData('id');
        $server = new \app\logic\Article();
        $re = $server->info($this->user_id, $id);
        $this->send($re);
    }

    /**
     * 更改状态
     */
    public function demo2()
    {
        $this->connect->send_succee([uniqid()]);
    }

    /**
     * 更改状态
     */
    public function editstatus()
    {

        $data = $this->getData();
        $server = new \app\logic\Article();
        $re = $server->edit_status($this->user_id, $data);
        $this->send($re);
    }
}