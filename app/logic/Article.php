<?php

namespace app\logic;

use app\Base;
use app\filterTool\ArticleES;
use app\validation\ArticleAdd;
use app\validation\ArticleEdit;
use pms\Validation\Validator\ServerAction;

class Article extends Base
{

    /**
     * 物理删除
     * @param $id
     * @return bool
     */
    public function dele($id)
    {
        $model = \app\model\article::findFirstById($id);
        if ($model instanceof \app\model\article) {
            return $model->delete();
        }
        return false;
    }

    /**
     * id转换成列表
     * @param $id_list
     * @return mixed
     */
    public static function ids2list($id_list)
    {
        $list = service\Article::ids2list($id_list);
        return \funch\Arr::array_change_index($list->toArray(), 'id');
    }

    /**
     * 下一篇
     * @return string
     */
    public static function next_info($id)
    {
        $model = \app\model\article::next_info($id);
        if ($model === false) {
            return [];
        }
        return $model;
    }

    /**
     * 修改文章状态
     * @param $user_id 修改人
     * @param $data 数据
     */
    public function edit_status($user_id, $data)
    {
        $fes = new ArticleES();
        $fes->filter($data);
        $va = new \app\validation\ArticleES();
        if (!$va->validate($data)) {
            return $va->getErrorMessages();
        }
        $dataBoj = \app\model\article::findFirst([
            'id = :id:', 'bind' => [
                'id' => $data['id']
            ]
        ]);
        if (!$dataBoj->save(['status' => $data['status']])) {
            return false;
        }
        return true;
    }

    public function va_ex($id)
    {
        $mo = \app\model\article::findFirstById($id);
        if (empty($mo)) {
            return false;
        }
        return true;

    }

    /**
     * 编辑
     * @param $user_id
     * @param $data
     * @return bool|string
     */
    public function edit($user_id, $data)
    {
        # 过滤
        $filter = new \app\filterTool\ArticleEdit();
        $filter->filter($data);
        //验证
        $validation = new ArticleEdit();
        if (!$validation->validate($data)) {
            return $validation->getMessages();
        }
        $dataBoj = \app\model\article::findFirst([
            'id = :id:', 'bind' => [
                'id' => $data['id']
            ]
        ]);
        $data['update_time'] = time();
        if (!$dataBoj) {
            return "不存在的数据!";
        }
        $dataBoj->setData($data);
        $re = $dataBoj->update();
        if ($re === false) {
            return $dataBoj->getMessage();
        } else {
            return true;
        }

    }

    /**
     * 删除
     * @param $id
     * @return bool|string
     */
    public function del($id)
    {
        $model = \app\model\article::findFirstById($id);
        if ($model === false) {
            return '_empty-info';
        }
        $model->status = -1;
        if ($model->save() === false) {
            return "_model-error";
        }
        return true;
    }


    /**
     * 获取文章列表 ,分页的
     * @param type $uid
     * @param type $pageData
     */
    public function lists($where, $now_page, $rows)
    {
        $page = service\Article::lists($where, $now_page, $rows);
        return $page;
    }

    /**
     * 增加文章
     * @param type $data
     */
    public function add($user_id, array $data)
    {
        $data['user_id'] = $user_id;
        # 过滤
        $filter = new \app\filterTool\ArticleAdd();
        $filter->filter($data);
        //验证
        $validation = new ArticleAdd();

        $validation->add_Validator('content', [
            'message' => 'content',
            'name' => ServerAction::class,
            'data' => [
                'id' => $data['content'],
                'type' => 'cms',
                'user_id' => $user_id
            ],
            'server_action' => 'article@/server/validation'
        ]);
        if (!$validation->validate($data)) {
            return $validation->getMessages();
        }
        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['status'] = 1;
        # 涉及多服务同时同时更新采用全局事务
        $task_data = [
            'name' => 'ArticleAddTx',
            'data' => $data
        ];

        $result = $this->swooleServer->taskwait($task_data, 20, -1);
        if ($result['re'] === true) {
            # 成功
            return true;
        }
        if(is_string($result['message'])){
            return $result['message'];
        }
        return false;

    }


    /**
     * 增加文章
     * @param type $data
     */
    public function add_bak($user_id, array $data)
    {
        $data['user_id'] = $user_id;
        # 过滤
        $filter = new \app\filterTool\ArticleAdd();
        $filter->filter($data);
        //验证
        $validation = new ArticleAdd();
        $validation->add_Validator('content', [
            'message' => 'content',
            'name' => ServerAction::class,
            'data' => [
                'id' => $data['content'],
                'type' => 'cms',
                'user_id' => $user_id
            ],
            'server_action' => 'article@/server/validation'
        ]);
        if (!$validation->validate($data)) {
            return $validation->getMessages();
        }
        #
        $tm = $this->transactionManager->get();
        //验证通过 进行插入
        $ArticleModel = new \app\model\article();
        if (!$ArticleModel->save($data)) {
            $tm->rollback();
            return $ArticleModel->getMessages();
        }
        # 进行关联更新
        $re = $this->proxyCS->request_return('article', '/server/correlation', [
            'id' => $data['content'],
            'type' => 'cms',
            'user_id' => $user_id
        ]);
        if (is_array($re) && $re['e']) {
            $tm->rollback();
            return false;
        }
        $tm->commit();
        return true;
    }


    /**
     * 查看量 增加+
     * @param $forum_id
     */
    public function viewedadd1($id)
    {
        $info = self::info($id);
        if (is_string($info)) {
            return $info;
        }
        if ($this->session->get('article_viewadd1' . $id)) {
            return true;
        }

        $info->viewed = $info->viewed + 1;
        if ($info->save() === false) {
            $this->session->get('article_viewadd1' . $id, 1);
            return $info->getMessage();
        }
        return true;
    }

    /**
     * 信息
     * @param $id
     */
    public function info($user_id, $id)
    {
        $model = \app\model\article::findFirstById($id);
        if ($model === false) {
            return '_empty-info';
        }
        return $model;
    }

    /**
     * 信息
     * @param $id
     */
    public function info2($id, $with_content)
    {
        $model = \app\model\article::findFirstById($id);
        if ($model === false) {
            output([$id, $model]);
            return '_empty-info';
        }
        $arr = $model->toArray();
        # 读取内容
        if ($with_content) {

            $info = $this->proxyCS->request_return('article', '/server/info', ['id' => $arr['content']]);
            if (!is_array($info) || $info['e']) {
                $arr['content_info'] = false;
            } else {
                $arr['content_info'] = $info['d'];
            }
        }

        return $arr;
    }
}