<?php
use Phalcon\Mvc\View;

class IndexController extends ControllerBase
{

    public function indexAction()
    {

    }

    public function loginAction()
    {
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);
        $accountArr = [
            '1101' => '123456',
            '1102' => '123456',
            '1103' => '123456',
        ];
        $account  = $this->request->getPost('account', ['trim', 'int']);
        $password = $this->request->getPost('password', ['trim', 'alphanum']);
        if (isset($accountArr[$account])) {
            if ($accountArr[$account] == $password) {
                $json = ['code' => 0, 'msg' => null];
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
