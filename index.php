<?php

$host = '127.0.0.1';
$dbname = 'korzun';
$user = 'korzun';
$pass = 'neto1653';

/*
$host = '127.0.0.1';
$dbname = 'lesson04-2';
$user = 'root';
$pass = '';
*/
try
{

$db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

function get_param($param_name) {
  if (isset($_REQUEST[$param_name]) and !empty($_REQUEST[$param_name])) {
    return strip_tags(trim($_REQUEST[$param_name]));
  }
  else {
    return "";
  }
}

$action=get_param('action');
$description=get_param('description');
$id=get_param('id');

if ($action=='new_desc' and !empty($description)){ // Добавляем новую запись
  $sql = "INSERT INTO `tasks` (`id`, `description`) VALUES ('', '$description')";
  $result = $db->prepare($sql)->execute();
  header('location: index.php'); // чтобы при нажатии F5 снова не передавать то же значение
}

if ($action=='delete' and $id>0) { // Удаляем запись
  $sql = "DELETE FROM `tasks` WHERE `id`='$id'";
  $result = $db->prepare($sql)->execute();
  header('location: index.php');
}

if ($action=='done' and $id>0) { // Меняем статус
  $sql = "UPDATE `tasks` SET `is_done`= '1' WHERE `id`='$id'";
  $result = $db->prepare($sql)->execute();
  header('location: index.php');
}

if ($action=='edit' and $id>0) { // Выводим текст описания для редактирования
  $result = $db->prepare("SELECT description FROM tasks WHERE `id`='$id' LIMIT 1"); 
  $result->execute();
  $row = $result->fetch();
  $description=$row['description'];
}

if ($action=='update' and $id>0) { // Меняем описание
  $sql = "UPDATE `tasks` SET `description`= '$description' WHERE `id`='$id'";
  $result = $db->prepare($sql)->execute();
  header('location: index.php');
}

$sort="date_added"; // Параметры для сортировки
if (!empty($_POST['sort_by']) and $_POST['sort_by']=='date_created') {
  $sort="date_added";
}
if (!empty($_POST['sort_by']) and $_POST['sort_by']=='is_done') {
  $sort="is_done";
}
if (!empty($_POST['sort_by']) and $_POST['sort_by']=='description') {
  $sort="description";
}

} 
catch (Exception $e) {
  die('Error: ' . $e->getMessage() . '<br>');
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Домашнее задание к лекции 4.2 «Запросы SELECT, INSERT, UPDATE и DELETE»</title>
  <style>
    form {
      margin-bottom: 15px;
    }

    table { 
      border-spacing: 0;
      border-collapse: collapse;
    }

    table td, table th {
      border: 1px solid #ccc;
      padding: 5px;
    }
      
    table th {
      background: #eee;
    }
</style>
</head>
<body>
<h1>Список дел на сегодня</h1>
<div style="float: left">
  <form method="POST">
    <input type="text" name="description" placeholder="Описание задачи" value="<?php echo $description; ?>">
<?php
  if ($action=='edit') { 
    echo "
    <input type='hidden' name='action' value='update'>
    <input type='submit' name='save' value='Сохранить'>
    <input type='hidden' name='id' value='$id'>";
}
  else {
    echo "
    <input type='hidden' name='action' value='new_desc'>
    <input type='submit' name='save' value='Добавить'>";
}
?>
  </form>
</div>

<div style="float: left; margin-left: 20px;">
  <form method="POST">
    <label for="sort">Сортировать по:</label>
    <select name="sort_by">
      <option selected="selected" value="date_created">Дате добавления</option>
      <option value="is_done">Статусу</option>
      <option value="description">Описанию</option>
    </select>
    <input type="submit" name="sort" value="Отсортировать">
    </form>
</div>
<div style="clear: both"></div>

<table>
  <tr>
  <th>Описание задачи</th>
    <th>Дата добавления</th>
    <th>Статус</th>
    <th>Что сделать</th>
  </tr>

<?php
$sql = "SELECT * FROM tasks ORDER BY $sort";
$result = $db->query($sql);
  foreach($result as $row) {
    if ($row['is_done']=="0") {
      $status = "<span style='color: orange;'>В процессе";
    }
    else {
      $status = "<span style='color: green;'>Выполнено";
    }
    echo "
<tr>
  <td>".$row['description']."</td>
  <td>".$row['date_added']."</td>
  <td>".$status."</td>
  <td>
    <a href='?id=".$row['id']."&action=edit'>Изменить</a> |
    <a href='?id=".$row['id']."&action=done'>Выполнить</a> |
    <a href='?id=".$row['id']."&action=delete'>Удалить</a>
  </td>
</tr>\n";
}
?>
</table>
</body>
</html>