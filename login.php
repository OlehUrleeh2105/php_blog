<html>

<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
  <div class="container mt-5">
    <h1 class="mt-4 mb-4">Логін</h1>
    <form class="form-horizontal" method="post" action="">
      <div class="form-group">
        <label for="email" class="col-sm-2 control-label">Електронна пошта:</label>
        <div class="col-sm-10">
          <input type="email" name="email" class="form-control" required>
        </div>
      </div>
      <div class="form-group">
        <label for="password" class="col-sm-2 control-label">Пароль:</label>
        <div class="col-sm-10">
          <input type="password" name="password" class="form-control" required>
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <button type="submit" name="login" class="btn btn-outline-success">Увійти</button>
          <button type="button" onclick="history.go(-1);" name="back" class="btn btn-outline-danger">Назад</button>
        </div>
      </div>
    </form>
    <p class="text-dark mb-4 sm-2">Ще не зареєстровані? <a href="register.php">Зареєструватися тут</a>.</p>
  </div>
</body>

</html>
<?php
require_once 'db.php';
if (isset($_POST['login'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $sql = "SELECT * FROM User WHERE email='$email'";

  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($password == $row['password']) {
      echo "Авторизація успішна.";
      session_abort();
      session_start();
      if ($row['role'] == 'admin') {
        $_SESSION['role'] = 'admin';
      }
      if ($row['role'] == 'user') {
        $_SESSION['role'] = 'user';
      }
      $_SESSION['user_id'] = $row['id'];
      $_SESSION['email'] = $row['email'];
      echo "Твоя роль " . $_SESSION["role"] . ".<br>";
      header("Refresh: 1; url=index.php");
    } else {
      echo "Невірний пароль.";
    }
  } else {
    echo "Користувач не знайдений.";
  }
}
$conn->close();
?>