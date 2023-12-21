<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$post_id = isset($_GET['id']) ? intval($_GET['id']) : null;

$sql = "SELECT * FROM post WHERE id = $post_id";
$result = mysqli_query($conn, $sql);
if (!$result) {
    echo "Debug: SQL error: " . mysqli_error($conn) . "<br>";
}
$post_data = mysqli_fetch_assoc($result);

if (isset($_POST['updatepost'])) {

    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';

    $sql = "UPDATE post SET title='$title', content='$content' WHERE id=$post_id";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        echo "Debug: SQL error: " . mysqli_error($conn) . "<br>";
    } else {
        header("Location: post_comments.php?id=$post_id");
        exit();
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редагувати</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container">
        <h1>Редагувати</h1>
        <form method="post">
            <div class="form-group">
                <label for="title">Назва</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo $post_data['title']; ?>">
            </div>
            <div class="form-group">
                <label for="content">Вміст</label>
                <textarea class="form-control" id="content" name="content" rows="5"><?php echo $post_data['content']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="updatepost">Оновити</button>
        </form>
    </div>
</body>

</html>