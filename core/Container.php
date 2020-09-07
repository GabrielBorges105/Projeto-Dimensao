<?php

namespace Core;

class Container
{
    public static function newController($controller)
    {
        $contr = ucfirst($controller);
        $objController = "\\App\\Controllers\\" . $contr;
        return new $objController;
    }

    public static function getModel($model)
    {
        $mode = ucfirst($model);
        $objModel = "\\App\\Models\\" . $mode;
        return new $objModel(DataBase::getDataBase());
    }

    public static function pageNotFound()
    {
        if (file_exists(__DIR__ . "/../app/Views/404.phtml")) {
            return require_once __DIR__ . "/../app/Views/404.phtml";
        } else {
            echo "Error 404: Page not found!";
        }
    }
}
