<?php
namespace app\Core;
use app\Core\Config;
use PDO;

class Db {
	private static $instance;
	
	private static function connect(){
	$config = new Config();
	$dbConfig = $config->get('db');
	return new PDO(
		'mysql:host=127.0.0.1;dbname=drustvena_odgovornost',
		$dbConfig['user'],
		$dbConfig['password']
		);
	}
	
	public static function getInstance(){
		if (self::$instance == null) {
			self::$instance = self::connect();
		}
		return self::$instance;
	}
	
}