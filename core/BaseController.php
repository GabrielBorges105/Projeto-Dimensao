<?php

namespace Core;

abstract class BaseController
{
    protected $params;
    private $view;
    private $layout;
    private $pageTitle = null;

    public function __construct()
    {
        $this->params = new \stdClass;
    }

    protected function renderView($view, $layout = null)
    {
        $this->view = $view;
        $this->layout = $layout;
        if ($layout) {
            return $this->layout();
        } else {
            return $this->content();
        }
    }

    protected function layout()
    {
        if (file_exists(__DIR__ . "/../app/Views/{$this->layout}.phtml")) {
            return require_once __DIR__ . "/../app/Views/{$this->layout}.phtml";
        } else {
            die('Route not found: ' . $this->layout);
        }
    }
    protected function content()
    {
        if (file_exists(__DIR__ . "/../app/Views/{$this->view}.phtml")) {
            return require_once __DIR__ . "/../app/Views/{$this->view}.phtml";
        } else {
            die('Route not found: ' . $this->view);
        }
    }

    protected function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    protected function getPageTitle($separator = null)
    {
        if ($separator) {
            return $this->pageTitle." ".$separator." ";
        } else {
            return $this->pageTitle." ";
        }
    }
    public function forbiden()
    {
        return Redirect::route('/login');
    }
    protected function getSession($key)
    {
        if (Session::get($key)) {
            $this->params->$key = Session::get($key);
            Session::destroy($key);
        }
    }
}
