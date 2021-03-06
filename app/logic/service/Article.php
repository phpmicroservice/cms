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
                $sub_id_list=[$where['cate_id']];
                $sub_id_list2= Cate::sub_id_list($where['cate_id']);
                $builder->andwhere(' category_id in ({category_id:array})', [
                    'category_id' => array_merge($sub_id_list,$sub_id_list2)
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
        
        if (isset($where['search_title']) && !empty($where['search_title'])) {
            $builder->andwhere("title LIKE :title:", [
                "title" => "%" . $where['search_title'] . "%"]);
        }
        if (isset($where['level_gt']) && $where['level_gt']) {
            $builder->andwhere("level > :level:", [
                "level" => $where['level_gt'] ]);
        }

        if (isset($where['level_eq']) && $where['level_eq']) {
            $builder->andwhere("level = :level:", [
                "level" => $where['level_eq'] ]);
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



}