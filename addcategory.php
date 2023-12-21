<?php
require_once 'db.php';

session_start();
if ($_SESSION['role'] != 'admin') {
  header('Location: login.php');
}
if (isset($_POST['add'])) {
  $name = $_POST['name'];
  $direction = $_POST['direction'];
  $sql = "INSERT INTO Category (name, direction) VALUES ('$name', '$direction')";
  if ($conn->query($sql) === TRUE) {
    echo "Категорія успішно додана.";
    header("Refresh: 1; url=posts.php");
  } else {
    echo "Помилка: " . $sql . "<br>" . $conn->error;
  }
}

$conn->close();
?>
<!DOCTYPE html>
<html>

<head>
  <title>Додати категорію</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
  <div class="container">
    <h1 class="mt-4 mb-4">Додати категорію</h1>
    <form method="post" action="">
      <div class="form-group">
        <label for="name">Назва:</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="direction">Напрямок:</label>
        <input type="text" name="direction" class="form-control" required>
      </div>
      <?php
      session_start();
      if ($_SESSION['role'] == 'admin') {
        echo '<input type="submit" name="add" value="Додати" class="btn btn-outline-success">';
      } else {
        echo '<p>У вас немає прав на додавання категорій.</p>';
      }
      ?>
      <button type="button" onclick="history.go(-1);" name="back" class="btn btn-outline-danger">Назад</button>
    </form>
  </div>
</body>

</html>