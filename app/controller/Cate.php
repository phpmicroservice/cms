<?php

namespace app\controller;

use app\Controller;

/**
 * 分类管理
 * Class Cate
 * @package app\controller
 */
class Cate extends Controller
{

    public function index()
    {
        $where = $this->getData();
        $server = new \app\logic\Cate();
        $re = $server->lists($where);
        $this->send($re);
    }

    /**
     * 创建一个分类
     */
    public function create()
    {
        $data = $this->getData();
        $server = new \app\logic\Cate();
        $re = $server->add($data);
        $this->send($re);
    }

    public function edit()
    {
        $data = $this->getData();
        $server = new \app\logic\Cate();
        $re = $server->edit($data);
        $this->send($re);
    }

    public function dele()
    {
        $id = $this->getData('id');
        $server = new \app\logic\Cate();
        $re = $server->dele($id);
        $this->send($re);
    }

    public function info()
    {
        $id = $this->getData('id');
        $server = new \app\logic\Cate();
        $re = $server->info($id);
        $this->send($re);
    }
}