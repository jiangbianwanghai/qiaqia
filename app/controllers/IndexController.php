<?php
use Phalcon\Mvc\View;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        $this->view->history = '';
        $history             = [];
        $key                 = '1101:uid_1536279503856';
        for ($i = -1; $i >= -10; $i--) {
            $item = json_decode($this->redis->lGet($key, $i), true);
            if ($item) {
                $history[] = $item;
            } else {
                break;
            }
        }
        $this->view->history = $history;
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
                $json = ['code' => 0, 'msg' => null];
                $auth = serialize(['account' => $account]);
                $this->cookies->set('auth', $auth, time() + 15 * 86400);
            } else {
                $json = ['code' => 2, 'msg' => '密码不正确'];
            }
        } else {
            $json = ['code' => 1, 'msg' => '工号不存在'];
        }
        $this->response->setJsonContent($json);
        return $this->response;
    }

}
