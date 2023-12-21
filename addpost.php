<html>

<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
  <div class="container mt-5">
    <h1 class="mt-4 mb-4">Додати пост</h1>
    <form enctype="multipart/form-data" method="post" action="">
      <div class="form-group">
        <label for="title">Заголовок:</label>
        <input type="text" class="form-control" name="title" required>
      </div>
      <div class="form-group">
        <label for="content">Контент:</label>
        <textarea class="form-control" name="content" required></textarea>
      </div>
      <div class="form-group">
        <label for="category">Категорія:</label>
        <select class="form-control" name="category" required>
          <?php
          require_once 'db.php';
          $sql = "SELECT * FROM Category";
          $result = $conn->query($sql);

          while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
          }
          ?>
        </select>
      </div>
      <div class="form-group">
        <label for="photo">Фото:</label>
        <input type="file" class="form-control-file" name="photo">
      </div>

      <div class="form-group">
        <label for="hashtags">Хештеги (через кому):</label>
        <input type="text" class="form-control" name="hashtags">
      </div>
      <button type="submit" class="btn btn-outline-success" name="add_post">Додати пост</button>
      <button type="button" onclick="history.go(-1);" name="back" class="btn btn-outline-danger">Назад</button>
  </div>
</body>
</form>

</html>
<?php
require_once 'db.php';
session_start();
if (isset($_POST['add_post'])) {
  $title = $_POST['title'];
  $content = $_POST['content'];
  $author_id = $_SESSION['user_id'];
  $category_id = $_POST['category'];
  $created_at = date('Y-m-d H:i:s');
  $hashtags = $_POST['hashtags'];

  $photo = $_FILES['photo']['name'];
  $photo_tmp = $_FILES['photo']['tmp_name'];
  $photo_path = "uploads/" . $photo;

  if (move_uploaded_file($photo_tmp, $photo_path)) {

    $sql = "INSERT INTO Post (title, content, author_id, category_id, created_at, photo) VALUES ('$title', '$content', '$author_id', '$category_id', '$created_at', '$photo_path')";
    if ($conn->query($sql) === TRUE) {
      $post_id = $conn->insert_id;
      if (!empty($hashtags)) {
        $hashtags_array = explode(',', $hashtags);
        foreach ($hashtags_array as $hashtag) {
          $hashtag = trim($hashtag);
          $hashtag_sql = "INSERT INTO PostHashtag (post_id, hashtag) VALUES ('$post_id', '$hashtag')";
          $conn->query($hashtag_sql);
        }
      }
      echo "Пост успішно доданий!";
      header("Refresh: 3; url=posts.php");
    } else {
      echo "Помилка: " . $sql . "<br>" . $conn->error;
    }
  }
}
$category_sql = "SELECT * FROM Category";
$category_result = $conn->query($category_sql);

$conn->close();
?>