<?php 

namespace Task;

/**
 * 
 */
class Connection
{
	public static $singeltone;

	public static function instance() {
		if (self::$singeltone === null) {
			self::$singeltone = new static;
			return new static;
		}
		return self::$singeltone;
	}
	

	public function connect()
	{
		try {
			$config = require dirname(dirname(__DIR__)) . '/config/db.php';

			return new \PDO("mysql:host={$config['host']};dbname={$config['dbname']}", $config['login'], $config['password'], [
			       \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
			       \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
			       \PDO::ATTR_EMULATE_PREPARES   => false,
			]);
		} catch (PDOException $e) {
		    print "Неудалось подключиться к бд!: " . $e->getMessage();
		    die();
		}
	}


}