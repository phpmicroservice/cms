<?php

namespace app\logic\service;

use app\Base;
use app\logic\Cate;
use app\model\article_category;

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
            ->from(\app\model\article::class);
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

    private function call_where(\Phalcon\Mvc\Model\Query\Builder $builder, $where)
    {
        if (isset($where['user_id']) && !empty($where['user_id'])) {
            $builder->andwhere(' uid= :uid:', [
                'uid' => $where['user_id']
            ]);
        }
        if (isset($where['cate_id']) && !empty($where['cate_id'])) {
            if (isset($where['with_sub']) && $where['with_sub']) {
                # 读取所有子分类的文章
                $sub_id_list = self::sub_id_list($where['cate_id']);
                $builder->andwhere(' category_id = ({category_id:array})', [
                    'category_id' => $sub_id_list
                ]);
            } else {
                $builder->andwhere(' category_id= :category_id:', [
                    'category_id' => $where['cate_id']
                ]);
            }

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
        if (isset($where['o']) && !empty($where['o'])) {
            # 排序
            if (isset($where['desc'])) {
                if ($where['desc']) {
                    $order2 = ' desc';
                } else {
                    $order2 = ' asc';
                }
            } else {
                $order2 = ' asc';
            }
            $builder->orderBy($where['o'] . $order2);
        } else {
            $builder->orderBy('id desc');
        }
        return $builder;
    }

    /**
     * 获取这个分类的所有子类
     * @param $cate_id
     */
    private static function sub_id_list($cate_id)
    {

        $key = md5(__FILE__ . 'sub_id_list' . $cate_id);
        $gCache=\Phalcon\Di::getDefault()->get('gCache');
        if ($gCache->exists($key)) {
        } else {
            # 不存在则读取
            $id_list = self::sub_id_list2($cate_id);
            $gCache->save($key, $id_list);
        }
        return $gCache->get($key);
    }

    /**
     * 获取这个分类的子类
     * @param $cate_id
     */
    public static function sub_id_list2($cate_id, $infinite = true)
    {
        $idlist = article_category::find([
            'pid = :pid:',
            'bind' => [
                'pid' => $cate_id
            ],
            'columns' => 'id'
        ]);
        $idlist = array_column($idlist->toArray(), 'id');
        if ($infinite) {
            foreach ($idlist as $id) {
                $idlist2 = self::sub_id_list2($id);
                $idlist = array_merge($idlist, $idlist2);
            }
        }
        return $idlist;

    }


}