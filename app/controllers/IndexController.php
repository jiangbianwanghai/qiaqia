<?php
use Phalcon\Mvc\View;

class IndexController extends ControllerBase
{
    public $tbl = APP_PATH . '/app/db/user.tbl';

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

    /**
     * 登录&注册面板
     */
    public function authAction()
    {
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    /**
     * 注册处理
     *
     * 如果用户表不存在，则初始化一个管理员帐号；如果存在则比对库中的帐号和注册的帐号是否冲突，不冲突则存入库中
     */
    public function signupAction()
    {
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);
        if ($this->request->isPost()) {
            $username = $this->request->getPost('username', ['trim', 'string']);
            $email    = $this->request->getPost('email', ['trim', 'string']);
            $password = $this->request->getPost('password', ['trim', 'alphanum']);
            if (!file_exists($this->tbl)) {
                $initUser['admin@weqia.live'] = [
                    'username'    => 'admin',
                    'email'       => 'admin@weqia.live',
                    'password'    => '123456',
                    'active'      => 1, //是否为激活用户
                    'create_time' => time(),
                ];
                file_put_contents($this->tbl, json_encode($initUser));
                $json = ['code' => 0, 'msg' => '初始化管理员帐号成功'];
            } else {
                $userTable = json_decode(file_get_contents($this->tbl), true);
                $flag      = false; //冲突标识，默认不冲突
                foreach ($userTable as $key => $value) {
                    if ($key == $email) {
                        $flag = true;
                        break;
                    }
                }
                if ($flag) {
                    $json = ['code' => 1, 'msg' => '此邮箱已经存在，请更换'];
                } else {
                    $userTable[$email] = [
                        'username'    => $username,
                        'password'    => $password,
                        'email'       => $email,
                        'active'      => 0, //是否为激活用户，默认是未经邮箱验证的用户
                        'create_time' => time(),
                    ];
                    file_put_contents($this->tbl, json_encode($userTable));
                    $json = ['code' => 0, 'msg' => '注册成功，请登录', 'url' => '/'];
                }
            }
        } else {
            $json = ['code' => 1, 'msg' => '只接收post提交'];
        }
        $this->response->setJsonContent($json);
        return $this->response;
    }

    /**
     * 登录处理
     */
    public function signinAction()
    {
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);
        $userTable = json_decode(file_get_contents($this->tbl), true);
        $email     = $this->request->getPost('email', ['trim', 'string']);
        $password  = $this->request->getPost('password', ['trim', 'alphanum']);
        if (isset($userTable[$email])) {
            if ($userTable[$email]['password'] == $password) {
                $json = ['code' => 0, 'msg' => null, 'url' => '/'];
                $auth = serialize(['account' => $email, 'acl_group' => 'Admin']);
                //$this->cookies->set('auth', $auth, time() + 15 * 86400);
                $this->cookies->set('auth', $auth);
            } else {
                $json = ['code' => 2, 'msg' => '密码不正确'];
            }
        } else {
            $json = ['code' => 1, 'msg' => '帐号不存在'];
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

    public function logoutAction()
    {
        $this->cookies->get('auth')->delete();
        $this->response->redirect('/auth', true);
    }

    public function error404Action()
    {
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->response->setHeader('HTTP/1.0 404', 'Not Found');
    }

    public function livekhAction()
    {
        header('Content-Type:application/json; charset=utf-8');
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);
        $output['code'] = 200;
        $output['data'] = '';
        $khtofd         = json_decode($this->redis->get("khtofd"), true);
        $kftokh         = json_decode($this->redis->get("kftokh"), true);
        $kh             = json_decode($this->redis->get("kh"), true);
        if (!empty($kftokh[$this->view->account])) {
            $khidarr = (array) $kftokh[$this->view->account];
            foreach ($khidarr as $key => $value) {
                $v           = $kh[$value];
                $v['online'] = 0;
                if (isset($khtofd[$value])) {
                    $v['online'] = 1;
                }
                $output['data'][] = $v;
            }
        }
        $callback = $_GET['callback'];
        echo $callback . '(' . json_encode($output) . ')';
        exit;
    }

}
