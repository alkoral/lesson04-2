<?php

$host = '127.0.0.1';
$dbname = 'korzun';
$user = 'korzun';
$pass = 'neto1653';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $sql1 = "
        DROP TABLE IF EXISTS `tasks`;
    
        CREATE TABLE `tasks` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `description` TEXT NOT NULL,
            `is_done` TINYINT(4) NOT NULL DEFAULT '0',
            `date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
    ";
    echo $db->prepare($sql1)->execute() ? 'Таблица tasks создана' : 'Ошибка при создании таблицы';
    echo '<br>';

    $sql2 = "INSERT INTO `tasks` (`id`, `description`) VALUES ('', 'Проверить запись')";
    echo $db->prepare($sql2)->execute() ? 'В таблицу tasks успешно добавлена запись' : 'Ошибка при добавлении записи';
    echo '<br>';

    $sql3 = "SELECT * FROM tasks";
    echo 'Содержание таблицы tasks:';
    echo '<pre>';
    $statement = $db->prepare($sql3);
    $statement->execute();
    var_dump($statement->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {
    die('Error: ' . $e->getMessage() . '<br>');
}
?>