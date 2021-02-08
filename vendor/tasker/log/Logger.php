<?php 
namespace Task\log;

use Carbon\Carbon;

/**
 * 
 */
class Logger
{
	public $carbon;

	public function __construct(Carbon $carbon = null) {
		if ($carbon !== null) {
			$this->carbon = $carbon;
		} else {
			$this->carbon = new Carbon;
		}
	}

	public function logging($message) {
		$message = $this->stylizedMessage($message);
		$f = fopen('/home/hiro/www/bot.com/logs/log.txt', 'w');
		fwrite($f, $message);
		fclose($f);
	}

	private function stylizedMessage($message) {
		$date = ($this->carbon::now())->format('Y-m-d H:i');
		return "---------------------------------------------------------------------\r\n\tВышла ошибка: {$message} \r\n\tДата: {$date}\r\n---------------------------------------------------------------------\r\n";
	}
}

