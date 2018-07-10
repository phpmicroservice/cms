<?php

namespace app\task;

use app\model\tmdemo;
use pms\Task\Task;
use pms\Task\TaskInterface;

class AdemoTx extends TxTask implements TaskInterface
{

    public function end()
    {

    }

    protected function b_dependenc()
    {
        $data = $this->trueData;
        $this->add_dependenc('article', 'demo', $data['data']);
    }

    protected function logic(): bool
    {
        $data = $this->trueData;
        $md = new tmdemo();
        $ere = $md->save($data['data']);
        return $ere;

    }

}