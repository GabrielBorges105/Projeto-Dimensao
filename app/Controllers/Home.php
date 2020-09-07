<?php

namespace App\Controllers;

use Core\BaseController;
use Core\Session;

class Home extends BaseController
{
    public function index($request)
    {
        $this->getSession('success');
        $this->getSession('err');
        $this->getSession('info');
        $this->params->user = Session::get('user');
        $this->setPageTitle("Home");
        $this->renderView("home/index", "layout");
    }
}
