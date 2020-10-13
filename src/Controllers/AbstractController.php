<?php

namespace app\Controllers;

use app\Core\Request;

abstract class AbstractController {
    protected $request;
    protected $di;
    protected $view;
    protected $db;

    public function __construct($di, Request $request) {
        $this->request = $request;
        $this->di = $di;
        $this->view = $this->di->get('Twig_Environment');
        $this->db = $this->di->get('PDO');

    }

    protected function render(string $template, array $params){
        return $this->view->loadTemplate($template)->render($params);
    }
}