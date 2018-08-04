<?php

namespace app\task;

/**
 * ArticleAdd 文章增加
 * Class ArticleAddTx
 * @package app\task
 */
class ArticleAddTx extends \pms\Task\TxTask
{

    public function end()
    {

    }

    /**
     * 在依赖处理之前执行,没有返回值
     */
    protected function b_dependenc()
    {
        $data = $this->getData();
        var_dump($data);
        $user_id = $data['user_id'];
        # 进行关联更新
        $data_dependenc = [
            'server_name' => 'cms',
            'id' => $data['content'],
            'type' => 'cms',
            'user_id' => $user_id
        ];
        $this->add_dependenc('article', 'ArticleCorrelation', $data_dependenc);
    }

    /**
     * 事务逻辑内容,返回逻辑执行结果,
     * @return bool false失败,将不会再继续进行;true成功,事务继续进行
     */
    protected function logic(): bool
    {
        $data = $this->getData();
        if (!isset($data['user_id'])) return false;
        //验证通过 进行插入
        $ArticleModel = new \app\model\article();
        if (!$ArticleModel->save($data)) {
            return $ArticleModel->getMessage();
        }
        $this->trueData['id'] = $ArticleModel->id;
        return true;
    }


}