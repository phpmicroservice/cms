<?php

namespace app\controller;

/**
 * 测试
 * Class Demo
 * @package app\controller
 */
class Demo extends \app\Controller
{


    /**
     * 测试的
     * @param $data
     */
    public function index($data)
    {
        $this->connect->send_succee([
            $data,
            "我是" . SERVICE_NAME . "分组",
            '当前登陆的用户是：' . $this->session->get('user_id'),
            mt_rand(1, 99999)
        ]);
    }

    public function tm()
    {
        var_dump(30);
        $re = $this->proxyCS->request('tm',
            '/service/create', [
            'server'=>'cms',
            'tx_name'=>'demo',
            'tx_data'=>[
                'name'=>time(),
                'title' => uniqid()
            ]
        ]);
        var_dump($re);
        $this->connect->send_succee($re);

    }

    public function tm2()
    {

        $this->swoole_server->task(['demo30', 'arg'], -1);
//        $this->swoole_server->task(['demo30','arg'], -1, function ($server,$task_id,$data) {
//            output(["这是在创建任务的时候定义的回调函数",get_class($server),$task_id,$data]);
//       });
        output(["这是控制器发送消息之前"]);
        $this->connect->send_succee([123]);
    }

}