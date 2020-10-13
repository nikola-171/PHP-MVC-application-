<?php

use app\Exceptions\NotFoundException;
namespace app\Core;

	class Config {
    private $data;
    private $web_app_data;

    public function __construct() {
        $json = file_get_contents(__DIR__ . '/../../config/app.json');
        $json_app_data = file_get_contents(__DIR__.'/../../config/web_app_data.json');
        $this->data = json_decode($json, true);
        $this->web_app_data = json_decode($json_app_data, true);
    }
    //  Ovde citamo podatke koji se tiču web aplikacije, na ovaj način je lakše rukovoditi izmenama 
    //  ukoliko je to potrebno
    public function get_app_data($key){
        if (!isset($this->web_app_data[$key])) {
            echo "Key $key not in config.";
            throw new NotFoundException("Key $key not in config.");
        }
        return $this->web_app_data[$key];
    }
    
    public function get($key) {
        if (!isset($this->data[$key])) {
            throw new NotFoundException("Key $key not in config.");
        }
        return $this->data[$key];
    }
}