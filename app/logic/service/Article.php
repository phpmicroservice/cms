<?php

namespace app\logic\service;

use app\Base;
use app\logic\Cate;

class Article extends Base
{

    public static function ids2list($id_list)
    {
        return \app\model\article::query()->inWhere('id', $id_list)->execute();
    }

    /**
     * 列表
     * @param $uid
     * @param $page
     * @param int $row
     * @return \stdClass
     */
    public static function lists($where, $page, $row = 10)
    {
        $modelsManager = \Phalcon\Di::getDefault()->get('modelsManager');
        $builder = $modelsManager->createBuilder()
            ->from(\app\model\article::class)
            ->orderBy("id");
        $builder = self::call_where($builder, $where);
        $paginator = new \pms\Paginator\Adapter\QueryBuilder(
            [
                "builder" => $builder,
                "limit" => $row,
                "page" => $page,
            ]
        );
        return $paginator->getPaginate();
    }

    private static function call_where(\Phalcon\Mvc\Model\Query\Builder $builder, $where)
    {

        output(func_get_args(), 41);

        if (isset($where['user_id']) && !empty($where['user_id'])) {
            $builder->andwhere(' uid= :uid:', [
                'uid' => $where['user_id']
            ]);
        }

        if (isset($where['cate_id']) && !empty($where['cate_id'])) {
            $builder->andwhere(' category_id= :category_id:', [
                'category_id' => $where['cate_id']
            ]);
        }
        if (isset($where['type_n']) && !empty($where['type_n'])) {
            # 读取分类id
            $cate_id = Cate::name2id($where['type_n']);
            $builder->andwhere(' category_id= :category_id:', [
                'category_id' => $cate_id
            ]);
        }
        if (isset($where['search_key']) && !empty($where['search_key'])) {
            $builder->where("title LIKE :title:", [
                "title" => "%" . $where['search_key'] . "%"]);
        }

        return $builder;
    }

}