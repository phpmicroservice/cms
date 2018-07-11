<?php

namespace app\task;

use app\model\tmdemo;
use pms\Task\TaskInterface;

class AdemoTx extends \pms\Task\TxTask implements TaskInterface
{

    public function end()
    {

    }

    /**
     * 在依赖处理之前执行,没有返回值
     */
    protected function b_dependenc()
    {
        $data = $this->trueData;
        $this->add_dependenc('article', 'demo', $data['data']);
    }

    /**
     * 事务逻辑内容,返回逻辑执行结果,
     * @return bool false失败,将不会再继续进行;true成功,事务继续进行
     */
    protected function logic(): bool
    {
        $data = $this->trueData;
        $md = new tmdemo();
        $ere = $md->save($data['data']);
        return $ere;

    }

}