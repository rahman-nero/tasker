<?php

ini_set('date.timezone', 'Europe/Moscow');
use Task\Tasker;

require 'vendor/autoload.php';

$tasker = new Tasker();
/*
* Запускаем бесконечный цикл для того чтобы скрипт невырубался, и каждую минуту, проверяем есть ли таск который должен выполниться
*/
while (true) {
	$tasker->run();
	sleep(60);
}
