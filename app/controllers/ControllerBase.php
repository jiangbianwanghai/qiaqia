<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function initialize()
    {
        $this->view->account = null;
        if ($this->cookies->has('auth')) {
            $auth                 = unserialize($this->cookies->get('auth'));
            $this->view->account  = $auth['account'];
            $this->view->username = $auth['username'];
        }
    }
}
