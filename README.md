# Таскер

#### Что за билиотека?

Таскер - это демон который при запуске в терминале, и который выполняет команды которые, ты передал - аналог cron и at

#### Как работает?

1. Сперва необходимо экспортировать базу данных из config/dbname.sql
2. Потом настроить соединение с бд в папке, config/db.php
3. Чтобы библиотека начала работать, запускаем главный файл вот таким образом, php -f ./start.php 

### Структура папок и файлов

1. папка logs - находятся логи выполненных тасков
2. папка tasks/ - хранятся классы-тасков*

### Команды для управления библиотекой
	
* tasker -a (time) ClassName - команда добавления таска, который будет вызываться по наступлению time(в timestamp), и будет вызывать класс ClassName

* tasker (-remove|-r) id - выполненяет удаление таска по id

* tasker (-dall|-delete_all) - выполняет удаление все тасков   

* tasker (list|-list) - вывод всех тасков которые еще не выполнились


##Примеры

1. tasker -a 1612788400 Example - Когда наступить это время (1612788400), то вызовится метод hadler, у класса Example
2. tasker -list - выводить все еще невыполнившиеся таски
3. tasker -r 5 - удаляет такс с id - 5
4. tasker -d_all - удаление всех тасков 
