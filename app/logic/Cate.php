<?php

namespace app\logic;

use app\Base;
use app\model\article_category;

class Cate extends Base
{


    /**
     * 转换分类的name 为分类的id
     * @param $name
     * @return int
     */
    public static function name2id($name): int
    {
        $model = article_category::findFirstByname($name);
        if (!($model instanceof article_category)) {
            return 0;
        }
        return (int)$model->id;

    }

    /**
     * 获取列表
     * @param $where
     * @return mixed
     */
    public function lists($where)
    {

        $modelsManager = \Phalcon\Di::getDefault()->get('modelsManager');
        $builder = $modelsManager->createBuilder()
            ->from(article_category::class)
            ->orderBy("id");

        $builder = $this->call_where($builder, $where);
        $list = $builder->getQuery()->execute();
        return $list->toArray();
    }

    /**
     * 处理where条件
     * @param \Phalcon\Mvc\Model\Query\Builder $builder
     * @param $where
     */
    protected function call_where(\Phalcon\Mvc\Model\Query\Builder $builder, $where)
    {

        if (isset($where['pid']) && !empty($where['pid'])) {
            $builder->andWhere(' pid =:pid:', [
                'pid' => $where['pid']
            ]);
        }
        return $builder;
    }

    /**
     * 增加文章,管理员的途径
     * @param $data
     */
    public function add($data)
    {

        $article_category = new article_category();
        $article_category->setData($data);
        if ($article_category->save() === false) {
            return $article_category->getMessage();
        }
        return (int)$article_category->id;
    }

    /**
     * 编辑分类信息,管理员的途径
     *
     * @param $id
     * @param $data
     */
    public function edit($id, $data)
    {
        $article_category = article_category::findFirst([
            'id = :id:', 'bind' => [
                'id' => $id
            ]
        ]);
        if ($article_category instanceof article_category) {
            //成功的读取了数据
        } else {
            return "empty-error";
        }
        $article_category->setData($data);
        if ($article_category->save() === false) {
            return $article_category->getMessage();
        }
        return true;
    }

    /**
     * 删除文章分类,管理员的途径
     * @param $id
     */
    public function dele($id)
    {
        $article_category = article_category::findFirst([
            'id = :id:', 'bind' => [
                'id' => $id
            ]
        ]);
        if ($article_category instanceof article_category) {
            //成功的读取了数据
        } else {
            return "empty-error";
        }
        if ($article_category->delete() === false) {
            return $article_category->getMessage();

        }
        return true;

    }

    /**
     * 获取信息
     * @param $id
     * @return \Phalcon\Mvc\Model|string
     */
    public function info($id)
    {
        $article_category = article_category::findFirst([
            'id = :id:', 'bind' => [
                'id' => $id
            ]
        ]);
        if ($article_category instanceof article_category) {
            //成功的读取了数据
            return $article_category;
        } else {
            return "empty-error";
        }
    }

}