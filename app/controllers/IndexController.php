<?php
use Phalcon\Mvc\View;

class IndexController extends ControllerBase
{

    public function indexAction($khid = '')
    {
        $khtofd = json_decode($this->redis->get("khtofd"), true);
        $kftokh = json_decode($this->redis->get("kftokh"), true);
        $kh     = json_decode($this->redis->get("kh"), true);
        if (!empty($kftokh[$this->view->account])) {
            $khidarr = (array) $kftokh[$this->view->account];
            foreach ($khidarr as $key => $value) {
                $khlist[$value] = $kh[$value];
            }
        } else {
            $khlist = [];
        }

        $this->view->khlist = $khlist;
        $curr_kh            = [
            'uid'    => $khid,
            'ua'     => $kh[$khid]['ua'],
            'avatar' => $kh[$khid]['avatar'],
        ];
        $this->view->khid    = $khid;
        $this->view->curr_kh = $curr_kh;
    }

    public function loginAction()
    {
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);
        $db_account_table = [
            '1101' => '123456',
            '1102' => '123456',
            '1103' => '123456',
        ];
        $account  = $this->request->getPost('account', ['trim', 'int']);
        $password = $this->request->getPost('password', ['trim', 'alphanum']);
        if (isset($db_account_table[$account])) {
            if ($db_account_table[$account] == $password) {
                $json = ['code' => 0, 'msg' => null, 'kfid' => $account];
                $auth = serialize(['account' => $account]);
                //$this->cookies->set('auth', $auth, time() + 15 * 86400);
                $this->cookies->set('auth', $auth);
            } else {
                $json = ['code' => 2, 'msg' => '密码不正确'];
            }
        } else {
            $json = ['code' => 1, 'msg' => '工号不存在'];
        }
        $this->response->setJsonContent($json);
        return $this->response;
    }

    public function chatlogAction($id)
    {
        header('Content-Type:application/json; charset=utf-8');
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);
        $log['code'] = 200;
        $log['data'] = null;
        $khtokf      = json_decode($this->redis->get("khtokf"), true);
        if (!empty($khtokf[$id])) {
            $key = $id . ':' . $khtokf[$id];
        } else {
            $key = 'system:' . $id;
        }
        for ($i = 0; $i <= 10; $i++) {
            $item = json_decode($this->redis->lGet($key, $i), true);
            if ($item) {
                $log['data'][] = $item;
            } else {
                break;
            }
        }
        if ($log['data']) {
            $log['data'] = array_reverse($log['data']);
        }
        $callback = $_GET['callback'];
        echo $callback . '(' . json_encode($log) . ')';
        exit;
    }

    public function chatlogkfAction($id)
    {
        header('Content-Type:application/json; charset=utf-8');
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);
        $log['code'] = 200;
        $log['data'] = null;
        $kftokh      = json_decode($this->redis->get("kftokh"), true);
        if (!empty($kftokh[$id])) {
            $key = $id . ':' . $kftokh[$id];
        } else {
            $key = 'system:' . $id;
        }
        $key = '1101:' . $id;
        for ($i = 0; $i <= 10; $i++) {
            $item = json_decode($this->redis->lGet($key, $i), true);
            if ($item) {
                $log['data'][] = $item;
            } else {
                break;
            }
        }
        if ($log['data']) {
            $log['data'] = array_reverse($log['data']);
        }
        $callback = $_GET['callback'];
        echo $callback . '(' . json_encode($log) . ')';
        exit;
    }

}
