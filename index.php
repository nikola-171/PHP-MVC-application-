<?php
use app\Core\Config;
use app\Core\Session;
use app\Core\Router;
use app\Core\Request;
use app\Core\Db;
use app\Utils\DependencyInjector;

require_once __DIR__ . '/vendor/autoload.php';

$di = new DependencyInjector();
Session::turn_on_session();
$config = new Config();
$db = null;

try{
    $db = Db::getInstance();
}catch(PDOException $Exception){
    die("Greška prilikom kreiranja PDO objekta: $Exception");
}

$loader = new Twig_Loader_Filesystem(__DIR__ . '/views');
$view = new Twig_Environment($loader);

$di->set('PDO', $db);
$di->set('Utils\Config', $config);
$di->set('Twig_Environment', $view);

$router = new Router($di);
$response = $router->route(new Request());
echo $response;