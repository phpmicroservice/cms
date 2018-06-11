<?php

namespace app\logic;

use app\Base;
use pms\Validation;

/**
 * 封面
 * Class Cover
 * @package app\logic
 */
class Cover extends Base
{

    /**
     * 设置封面
     * @param $article_id
     * @param $file_id
     */
    public function setcover($article_id, $file_id)
    {
        # 读取数据
        $info = $this->info($article_id);
        if (!($info instanceof \app\model\cover)) {
            return false;
        }
        # x先验证
        $va = new Validation();
        $va->add_Validator('file_id', [
            'name' => Validation\Validator\ServerAction::class,
            'server_action' => 'file@/server/ex_array',
            'data' => [
                'array_id' => $info->file_array_id,
                'type' => 'cms',
                'file_id' => $file_id
            ]
        ]);
        if ($va->validate([''])) {

        }
        # 验证完成

        $info = $this->info($article_id);
        if (!($info instanceof \app\model\cover)) {
            return false;
        }
        $info->cover_file_id = $file_id;
        if (!$info->save()) {
            return $info->getMessages();
        }
        return true;
    }

    /**
     * 获取封面你的信息
     * @param $article_id
     */
    public function info($article_id)
    {
        # 查看文章是否存在
        $article = \app\model\article::findFirst([
            'id =:article_id:', 'bind' => [
                'article_id' => $article_id
            ]
        ]);
        if (empty($article)) {
            return false;
        }

        $info = \app\model\cover::findFirst([
            'article_id =:article_id:', 'bind' => [
                'article_id' => $article_id
            ]
        ]);

        if (empty($info)) {
            # 不存在,初始化
            # 创建一个集合
            $data = [
                'user_id' => 0,
                'remark' => 'cover',
                'only' => 0
            ];
            $re = $this->proxyCS->request_return('file', '/server/create_array', $data);
            if (is_array($re) && !$re['e'] && is_int($re['d'])) {
                # 成功创建
                $data = [
                    'article_id' => $article_id,
                    'file_array_id' => $re['d'],
                    'cover_file_id' => 0
                ];
                $model = new \app\model\cover();
                if (!$model->save($data)) {
                    return false;
                }
                return $model;
            } else {
                return false;
            }
        }
        return $info;
    }

}