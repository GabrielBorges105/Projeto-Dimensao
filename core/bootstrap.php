<?php
if (!session_id()) session_start();

//iniciando as rotas
$routes = require_once __DIR__ . "/../app/route.php";
$route = new \Core\Route($routes);
