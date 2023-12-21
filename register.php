<html>

<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
  <div class="container">
    <h1 class="mt-4 mb-4">Реєстрація</h1>
    <form class="form-horizontal" method="post" action="">
      <div class="form-group">
        <label for="username" class="col-sm-2 control-label">Ім'я користувача:</label>
        <div class="col-sm-10">
          <input type="text" name="username" class="form-control" required>
        </div>
      </div>
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
        <label for="role" class="col-sm-2 control-label">Роль:</label>
        <div class="col-sm-10">
          <select name="role" class="form-control" required>
            <option value="">Виберіть роль</option>
            <option value="user">Користувач</option>
            <option value="admin">Адміністратор</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <button type="submit" name="register" class="btn btn-outline-success">Зареєструватися</button>
          <button type="button" onclick="history.go(-1);" name="back" class="btn btn-outline-danger">Назад</button>
        </div>
      </div>
    </form>
    <p class="text-dark mb-4 sm-2">Вже маєте акаунт? <a href="login.php">Увійти тут</a>.</p>
  </div>
</body>

</html>
<?php
require_once 'db.php';
if (isset($_POST['register'])) {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $role = $_POST['role'];

  $sql = "INSERT INTO User (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";

  if ($conn->query($sql) === TRUE) {
    echo "Користувач зареєстрований успішно.";
    header("refresh: 3;");
  } else {
    echo "Помилка: " . $sql . "<br>" . $conn->error;
  }
}

$conn->close();
?>