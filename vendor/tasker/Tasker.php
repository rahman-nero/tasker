<?php 

namespace Task;

use Carbon\Carbon;
use Task\log\Logger;

/**
 * 
 */
class Tasker 
{	
	/*
	* Здесь хранятся все таски
	* Вид :
	* [
	*	'regular' = [], - таски которые нужно, перезаписать с новым временем - (они не удаляются, а обновляются)
	*	'old' = [], - старые таски, у которых время выполнения было час назад, крч они должны были выполниться час назад и раньше, но не выполнились и удаляем их
	*  ]
	*/
	protected $tasks = [];

	/*
	 * PDO - Соединения с бд 
	*/
	protected $connection;

	/*
	 * Carbon - для работы с времен
	*/
	protected $carbon;
	/*
	 * Logger - логирование, логи находятся в logs/log.txt
	*/
	protected $logger;

	/*
	* Происходить попытка соединения с бд, а затем получаем объекты классов
	* и в конце получаем наши данные и сортируем - getTasksFromTable()
	* 
	* @param $db - имя базы данных 
	* @param $login - и так понятно
	* @param $password - тоже самое
	*/
	public function __construct()
	{
		$this->connection = Connection::instance();
		$this->connection = $this->connection->connect();
		$this->carbon = new Carbon();
		$this->logger = new Logger($this->carbon);
	}

	/*
	* Получаем все таски из бд
	* и вызвав метод sortTasks() - сортируем их
	*/
	private function getTasksFromTable() 
	{
		$tasks = $this->connection->query("SELECT * FROM `tasks`");
		$count = $tasks->rowCount();
		if ($count > 0) {
			$this->tasks = $tasks->fetchAll();
			$this->sortTasks();
		} else {
			$this->tasks = [];
		}
	}

	/**
	* Запускаем весь функционал
	*/
	public function run() {
		$this->getTasksFromTable();
		if (!empty($this->tasks)) {
			$this->deleteOldTasks(); // Производить удаления старых тасков, у которых время наступило уже больше часа назад
			$this->runTasks(); // А это таски которые вызываются один раз - а потом удаляются
		}
	}

	/*
	* Здесь таски выполнятся один раз, а затем происходить удаления их из бд
	*/
	private function runTasks(){
		$now = ($this->carbon::now())->timestamp; // текущее время в timestamp
		foreach ($this->tasks['tasks'] as $key => $task) {
	 		if ($now > $task['time']) {
			 	$id = $task['id'];
			 	$time = $task['time'];
			 	$class = $task['call_class'];
		 		$this->call_class($class, compact('id', 'time'));
		 		$this->connection->query("DELETE FROM `tasks` WHERE `id` = {$id}");
	 		}
		}
	}

	/*
	* В этот метод передаются все таски, которые должны были выполниться еще час назад
	* такие таски считаются неактивными и поэтому удаляются из бд
	*/
	private function deleteOldTasks() {
		foreach ($this->tasks['old'] as $k => $task) {
			try{
				$id = $task['id'];
				$class = $task['call_class'];
				$time = $task['time'];
				$this->connection->query("DELETE FROM `tasks` WHERE `id` = {$id}");
				$this->logger->logging("Удален старый таск, который должен был вызваться в {$time}, вызывая класс {$class}");
			} catch (Exception $e) {
				$this->logger->logging($e->getMessage());
			}
		}
	}

	/*
	* Здесь происходит сортировка пришедших данных из бд, т.е таски
	* Конечный вид
	*  [
	*	'regular' = [], - таски которые нужно, перезаписать с новым временем - (они не удаляются, а обновляются)
	*	'old' = [], - старые таски, у которых время выполнения было час назад, крч они должны были выполниться час назад и раньше, но не выполнились и удаляем их
	*  ]
	*/
	private function sortTasks() {
		$now = ($this->carbon::now())->timestamp;

		// Таски которые должны выполнится
		$tasks = [];

		// Таски у которых прошла время выполнения, сдесь будем хранить id
		$latter_task = [];

		// Сортируем
		foreach ($this->tasks as $key => &$task) {
			// Если уже время прошло с выполнения, храним тут, и удаляем из массива
			if (($now - $task['time']) > 3600) {
				$latter_task[] = $task;
			} else {
				$tasks[] = $task;
			}
			unset($this->tasks[$key]);
		}
		
		$this->tasks['tasks'] = $tasks;
		$this->tasks['old'] = $latter_task;
	}

	/**
	* Вызов класса который был в поле call_class у таска
	*/
	private function call_class($class, $params = []) {
		$class = "Tasks\\". $class;
		if (class_exists($class)) {
			try{
				$id = $params['id'];
				$time = $params['time'];
				$obj = new $class;
				$obj->hadler($id, $time);
			} catch(Exception $e) {
				$this->logger->logging($e->getMessage());
			}
		} else { // если его нету, записываем в логи
			$this->logger->logging("Невозможно вызвать такой класс: " . $class);
		}
	}

	/*
	* Геттер
	*/
	public function getTasks() {
		return $this->tasks;
	}
}
