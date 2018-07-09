<?php

namespace app\task;

use app\model\tmdemo;
use pms\Task\Task;
use pms\Task\TaskInterface;

class DemoTx extends Task implements TaskInterface
{
    public function run()
    {
        $data = $this->trueData;
        if (isset($data[''])) return false;
        $xid = $data['xid'];
        $db = $this->getDb();
        $tmdata = [
            'xid' => $data['xid'],
            'name' => $data['name']
        ];
        # 处理依赖完成
        $re = $this->getProxyCS()->request_return('tm', '/server/dependency',$tmdata);
        if ($re['e']) {
            # 通知事务协调器 依赖完成
        }
        # 启动事务
        $db->query('XA START ' . $xid);
        $md = new tmdemo();
        if (!$md->save($data['data'])) {
            $db->query('XA END ' . $xid);
            # 保存失败,直接通知事务协调器,事务不能继续
            $re = $this->getProxyCS()->request_return('tm', '/server/rollback');
            if (!$re['e']) {
                # 通知事务协调器成功
            } else {
                # 通知事务协调器失败
            }
            # 不管咋地这个事务都得回滚
            # 事务自动回滚
            $db->query('XA ROLLBACK ' . $xid);
            return false;
            #
        }
        $db->query('XA END ' . $xid);
        # 通知事务协调器事务 构建 完成,可以提交 2
        $re = $this->getProxyCS()->request_return('tm', '/server/end', $tmdata);
        if (!$re['e']) {
            # 成功的通知了 事务协调器
        } else {
            # 没有成功的通知 事务协调器
            # 自动回滚
            $db->query('XA ROLLBACK ' . $xid);
            # 通知事务协调器 我要回滚了
            $re = $this->getProxyCS()->request_return('tm', '/server/rollback');
            return false;
        }
        # 继续往下走准备提交
        try {
            # 进行 准备提交
            $db->query('XA PREPARE ' . $xid);
            $re = $this->getProxyCS()->request_return('tm', '/server/prepare');
            if ($re['e']) {
                $db->query('XA ROLLBACK ' . $xid);
                return false;
            }
            # 进行提交
            $db->query('XA COMMIT ' . $xid);
            $re = $this->getProxyCS()->request_return('tm', '/server/commit');

        } catch (\PDOException $e) {
            $db->query('XA ROLLBACK ' . $xid);
            $re = $this->getProxyCS()->request_return('tm', '/server/rollback');
            return false;
        }

    }

    private function getDb(): \Phalcon\Db\Adapter\Pdo\Mysql
    {
        return \Phalcon\Di::getDefault()->get('db');
    }

    private function getProxyCS(): \pms\bear\ClientSync
    {
        return \Phalcon\Di::getDefault()->get('proxyCS');
    }


    public function end()
    {

    }

}