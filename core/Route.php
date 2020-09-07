<?php

namespace Core;

class Route
{
    private $routes;
    public function __construct(array $routes)
    {
        $this->setRoutes($routes);
        $this->run();
    }

    private function setRoutes($routes)
    {
        foreach ($routes as $route) {
            $routeArray = explode('@', $route[1]);
            if (isset($route[2])) {
                $r = [$route[0], $routeArray[0], $routeArray[1], $route[2]];
            } else {
                $r = [$route[0], $routeArray[0], $routeArray[1]];
            }
            $newRoutes[] = $r;
        }
        $this->routes = $newRoutes;
    }
    private function getRequest()
    {
        $obj = (object)array();
        $obj->get = (object)array();
        $obj->post = (object)array();
        $obj->files = (object)array();

        foreach ($_GET as $key => $value) {
            $obj->get->$key = $value;
        }
        foreach ($_POST as $key => $value) {
            $obj->post->$key = $value;
        }
        foreach ($_FILES as $key => $value) {
            $obj->files->$key = $value;
        }
        return $obj;
    }
    private function getUrl()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
    private function run()
    {

        $url = $this->getUrl();
        $urlArray = explode('/', $url);
        if($urlArray[1]=="sgr3"){
            $urlArray[1] = $urlArray[2];
            unset($urlArray);
            $url = explode('/sgr3', $url);
            $url = $url[1];
        }
        foreach ($this->routes as $route) {
            $routeArray = explode('/', $route[0]);
            $param = [];

            for ($i = 0; $i < count($routeArray); $i++) {
                if ((strpos($routeArray[$i], "{") !== false) && (count($routeArray) == count($urlArray))) {
                    $routeArray[$i] = $urlArray[$i];
                    $param[] = $urlArray[$i];
                }
                $route[0] = implode('/', $routeArray);
            }
           
            
            if ($url == $route[0]) {
                $found = true;
                $controller = $route[1];
                $action = $route[2];
                $auth = new Auth;
                if (isset($route[3]) && !$auth->check()) {
                    $action = 'forbiden';
                }
                break;
            }

        }
        if (isset($found) && $found == true) {
            $controller = Container::newController($controller);
            if (isset($param)) {
                switch (count($param)) {
                    case 1:
                        $controller->$action($param[0], $this->getRequest());
                        break;
                    case 2:
                        $controller->$action($param[0], $param[1], $this->getRequest());
                        break;
                    case 3:
                        $controller->$action($param[0], $param[1], $param[2], $this->getRequest());
                        break;
                    default:
                        $controller->$action($this->getRequest());
                        break;
                }
            } else {
                $controller->$action($this->getRequest());
            }
        } else {
            Container::pageNotFound();
        }
    }
}
