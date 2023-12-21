<?php

session_start();

require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$post_id = isset($_GET['id']) ? intval($_GET['id']) : null;

$sql = "SELECT post.*, user.username AS author, category.name AS category FROM post
        LEFT JOIN user ON post.author_id = user.id
        LEFT JOIN category ON post.category_id = category.id
        WHERE post.id = $post_id";
$result = mysqli_query($conn, $sql);
if (!$result) {
    echo "Debug: SQL error: " . mysqli_error($conn) . "<br>";
}
$post_data = mysqli_fetch_assoc($result);
mysqli_free_result($result);

$sql = "SELECT comment.*, user.username AS author FROM comment
        LEFT JOIN user ON comment.user_id = user.id
        WHERE comment.post_id = $post_id
        ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    echo "Debug: SQL error: " . mysqli_error($conn) . "<br>";
}
$num_comments = mysqli_num_rows($result);

$comments_per_page = 2;

$total_pages = ceil($num_comments / $comments_per_page);

$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$start_index = ($current_page - 1) * $comments_per_page;

$sql .= " LIMIT $start_index, $comments_per_page";
$result = mysqli_query($conn, $sql);
if (!$result) {
    echo "Debug: SQL error: " . mysqli_error($conn) . "<br>";
}
if (isset($_POST['delete_comment']) && $_SESSION['user_id'] > 0) {
    $comment_id = $_POST['comment_id'];
    $sql = "DELETE FROM comment WHERE id=$comment_id";
    mysqli_query($conn, $sql);
    header("refresh: 0;");
}

if (isset($_POST['addcomment'])) {

    $content = isset($_POST['content']) ? trim($_POST['content']) : '';

    if (empty($content)) {
        echo "<p>Please enter a comment.</p>";
    } else {

        $user_id = $_SESSION['user_id'];
        $sql = "INSERT INTO comment (user_id, post_id, content) VALUES ($user_id, $post_id, '$content')";
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            echo "Debug: SQL error: " . mysqli_error($conn) . "<br>";
        } else {
            header("Location: $_SERVER[PHP_SELF]?id=$post_id&page=$current_page");
            exit();
        }
    }
}
if (isset($_POST['edit_comment'])) {
    $comment_id = $_POST['comment_id'];
    $content = $_POST['content'];
    $sql = "UPDATE comment SET content='$content' WHERE id=$comment_id";
    mysqli_query($conn, $sql);
    header("Location: $_SERVER[PHP_SELF]?id=$post_id&page=$current_page");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $post_data["title"]; ?> - Коментарі
    </title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</head>

<body>
    <div class="container">
        <h1>
            <?php echo $post_data["title"]; ?>
        </h1>
        <p>By
            <?php echo $post_data["author"]; ?> on
            <?php echo $post_data["
created_at"]; ?>
        </p>
        <p>
            <?php echo $post_data["content"]; ?>
        </p>
        <?php if ($_SESSION['role'] == 'admin' || $_SESSION['user_id'] == $post_data['author_id']) : ?>
            <a href="edit_post.php?id=<?php echo $post_data['id']; ?>" class="btn btn-primary">Редагувати</a>
        <?php endif; ?>
        <hr>

        <h2>Коментарі</h2>

        <?php if ($num_comments == 0) : ?>
            <p>Ще немає коментарів.</p>
        <?php else : ?>

            <?php while ($row = mysqli_fetch_assoc($result)) : ?>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <p>By
                            <?php echo $row["author"]; ?> on
                            <?php echo $row["created_at"]; ?>
                        </p>
                    </div>
                    <div class="panel-body">
                        <p>
                            <?php echo $row["content"]; ?>
                        </p>
                        <?php if ($_SESSION['user_id'] > 0) :
                            if ($_SESSION['role'] == 'admin' || $row['user_id'] == $_SESSION['user_id']) : ?>
                                <form method="post">
                                    <input type="hidden" name="comment_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_comment" class="btn btn-danger">Видалити</button>
                                    <?php if ($_SESSION['role'] == 'admin' || $row['user_id'] == $_SESSION['user_id']) : ?>
                                        <button type="button" class="btn btn-primary" onclick="showEditForm(<?php echo $row['id']; ?>)">Редагувати</button>
                                        <div id="edit-form-<?php echo $row['id']; ?>" style="display: none;">
                                            <form method="post">
                                                <input type="hidden" name="comment_id" value="<?php echo $row['id']; ?>">
                                                <div class="form-group">
                                                    <textarea name="content" class="form-control"><?php echo $row['content']; ?></textarea>
                                                </div>
                                                <button type="submit" name="edit_comment" class="btn btn-success">Зберегти зміни</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>

                                </form>
                        <?php endif;
                        endif; ?>
                    </div>
                </div>

            <?php endwhile; ?>

            <?php if ($total_pages > 1) : ?>

                <div class="text-center">
                    <ul class="pagination">
                        <?php if ($current_page > 1) : ?>
                            <li><a href="?id=<?php echo $post_id; ?>&page=<?php echo $current_page - 1; ?>">Prev</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                            <li class="<?php echo $i === $current_page ? 'active' : ''; ?>"><a href="?id=<?php echo $post_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages) : ?>
                            <li><a href="?id=<?php echo $post_id; ?>&page=<?php echo $current_page + 1; ?>">Далі</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

            <?php endif; ?>

        <?php endif; ?>

        <?php if (($_SESSION['user_id']) > 0) : ?>


            <hr>

            <h2>Додати коментар</h2>
            <div class="form">
                <form action="" method="post">
                    <div>
                        <label for="message">Повідомлення:</label><br>
                        <textarea name="content" id="content" rows="5" cols="50"></textarea>
                    </div>
                    <div>
                        <input type="submit" name="addcomment" value="Додати коментар" class="btn btn-success">

                    </div>
                </form>
            </div>
            <br>
        <?php endif; ?>
        <a type="button" href="posts.php" name="back" class="btn btn-danger">Назад</a>
    </div>
    <?php mysqli_free_result($result); ?>
    <?php mysqli_close($conn); ?>
    <script>
        function showEditForm(commentId) {
            var formId = "edit-form-" + commentId;
            var form = document.getElementById(formId);
            form.style.display = "block";
        }
    </script>

</body>

</html>