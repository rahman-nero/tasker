<?php
ini_set('date.timezone', 'Europe/Moscow');

use Task\Tasker;
require 'vendor/autoload.php';

$tasker = new Tasker('dbname', 'login', 'password');

while (true) {
	$tasker->run();
	sleep(60);
}
