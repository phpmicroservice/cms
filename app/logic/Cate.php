<?php

namespace app\logic;

use app\Base;
use app\filterTool\CateFilter;
use app\model\article_category;
use app\validation\CateAdd;
use app\validation\CateEdit;

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
        if (isset($where['with_sub']) && !empty($where['with_sub'])) {
            $idlist=self::sub_id_list($where['with_sub']);
            $builder->andWhere(' id in ({id:array})', [
                'id' => $idlist
            ]);
        }
        return $builder;
    }

    /**
     * 增加文章分类,管理员的途径
     * @param $data
     */
    public function add($data)
    {
        # 进行数据过滤
        $ft = new CateFilter();
        $ft->filter($data);
        $va = new CateAdd();
        if (!$va->validate($data)) {
            return $va->getMessages();
        }
        $article_category = new article_category();
        $article_category->setData($data);
        try {
            if (!$article_category->save()) {
                return $article_category->getMessage();
            }
        } catch (\PDOException $exception) {
            return $exception->getMessage();
        }

        return (int)$article_category->id;
    }

    /**
     * 编辑分类信息,管理员的途径
     *
     * @param $id
     * @param $data
     */
    public function edit($data)
    {
        # 进行数据过滤 和验证
        $ft = new \app\filterTool\CateEdit();
        $ft->filter($data);
        $id = $data['id'] ?? 0;
        $va = new CateEdit();
        if (!$va->validate($data)) {
            return $va->getMessages();
        }

        # 验证完成
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
        if ($article_category->save($data) === false) {
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
            return $article_category->toArray();
        } else {
            return "empty-error";
        }
    }


    /**
     * 获取这个分类的所有子类
     * @param $cate_id
     */
    public static function sub_id_list($cate_id)
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
    private static function sub_id_list2($cate_id, $infinite = true)
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