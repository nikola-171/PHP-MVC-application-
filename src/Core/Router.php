<?php

namespace app\Core;
// ovde bilo include za sessije, valjda to nije potrebno

use app\Controllers\welcomeController;
use app\Controllers\notFoundController;
use app\Controllers\loginController;

class Router {
	private $routeMap;
	private static $regexPatters = [
        'number' => '\d+',
        'string' => '\w'
    ];
    
    private $di;
	

    public function __construct($di) {
        $json = file_get_contents(__DIR__ . '/../../config/routes.json');
		$this->routeMap = json_decode($json, true);
		$this->di = $di;
    }

    public function route(Request $request){
		$path = $request->getPath();
		//novo
		foreach ($this->routeMap as $route => $info) {
            $regexRoute = $this->getRegexRoute($route, $info);
            if (preg_match("@^/$regexRoute$@", $path)) {
                return $this->executeController($route, $path, $info, $request);
            }
		}
		
        $errorController = new notFoundController($this->di,$request);
        return $errorController->show_error_message();
	}

	private function executeController(
        string $route,
        string $path,
        array $info,
        Request $request
    ){
        $controllerName = '\app\Controllers\\' . $info['controller'] . 'Controller';
        $controller = new $controllerName($this->di,$request);
      
        if (isset($info['login']) && $info['login']) {
            if (!$request->getSession()->has('user')) {
                $not_logged_in_error = new loginController($this->di,$request);
                return $not_logged_in_error->showAll();
            } 
        }
        $params = $this->extractParams($route, $path);
        return call_user_func_array([$controller, $info['method']], $params);
    }
	
	private function getRegexRoute(string $route, array $info){
        if (isset($info['params'])) {
            foreach ($info['params'] as $name => $type) {
                $route = str_replace(':' . $name, self::$regexPatters[$type], $route);
            }
        }

        return $route;
	}
	
	private function extractParams(string $route, string $path){
        $params = [];

        $pathParts = explode('/', $path);
        $routeParts = explode('/', $route);

        foreach ($routeParts as $key => $routePart) {
            if (strpos($routePart, ':') === 0) {
                $name = substr($routePart, 1);
                $params[$name] = $pathParts[$key+1];
            }
        }

        return $params;
    }

    
}
	
	
