<?php
require_once 'db.php';

function hasUserLikedPost($postId, $userId)
{
    global $conn;
    $sql = "SELECT liked FROM postlike WHERE post_id=$postId AND user_id=$userId";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['liked'] == 1;
    }
    return false;
}

$sql = "SELECT post.*, user.username AS author, category.name AS category
        FROM post
        LEFT JOIN user ON post.author_id = user.id
        LEFT JOIN category ON post.category_id = category.id
        LEFT JOIN postlike ON post.id = postlike.post_id AND postlike.liked = 1
        GROUP BY post.id
        ORDER BY created_at DESC";
$result = $conn->query($sql);

function formatDate($date)
{
    return date("F j, Y, g:i a", strtotime($date));
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Posts</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">BLOG</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="posts.php">Posts</a>
                </li>
            </ul>
            <?php
            session_start();
            if ($_SESSION['user_id'] < 0) {
                echo '<ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="register.php">Register</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="login.php">Login</a>
        </li>
      </ul>';
            } else {
                echo '<p class="text-light mb-2 sm-2">' . $_SESSION["email"] . " " . '<a href="logout.php">Вийти</a>' . '</p>';
            }
            ?>
        </div>
    </nav>
    <div class="container">
        <h1 class="mt-4 mb-4" style="text-align: center;">Posts</h1>

        <div class="row mb-3">
            <div class="col-md-6">
                <form method="post">
                    <label for="category">Фільтрувати за категорією:</label>
                    <select name="category" id="category" class="form-control">
                        <option value="">Усі категорії</option>
                        <?php
                        $sql = "SELECT * FROM category";
                        $result = $conn->query($sql);

                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row["id"] . '">' . $row["name"] . '</option>';
                        }
                        ?>
                    </select>
            </div>
            <!-- <div class="col-md-6">
                <label for="date">Фільтрувати за датою:</label>
                <input type="date" name="date" id="date" class="form-control">
            </div> -->
            <div class="col-md-6">
                <label for="date_from">Фільтрувати за датою (від):</label>
                <input type="date" name="date_from" id="date_from" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="date_to">Фільтрувати за датою (до):</label>
                <input type="date" name="date_to" id="date_to" class="form-control">
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary mt-2">Фільтрувати</button>
                <?php
                if ($_SESSION['role'] == 'admin' && $_SESSION['user_id'] > 0) {
                    echo '<a class="btn btn-primary mt-2" href="addcategory.php">Додати категорії</a>';
                }
                ?>
                </form>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <form method="post">
                    <div class="form-group">
                        <label for="search">Пошук:</label>
                        <input type="text" name="search" id="search" class="form-control ">
                    </div>
                    <button type="submit" class="btn btn-primary">Пошук</button>
                    <?php
                    if ($_SESSION['user_id'] > 0) {
                        echo '<a class="btn btn-primary" href="addpost.php">Додати пост</a>';
                    }
                    ?>
                </form>
            </div>
        </div>

        <hr>
        <div class="post-list">
            <div class="row">
                <?php
                $sql = "SELECT post.*, user.username AS author, category.name AS category FROM post
            LEFT JOIN user ON post.author_id = user.id
            LEFT JOIN category ON post.category_id = category.id";

                $category_filter = isset($_POST["category"]) && !empty($_POST["category"]) ? $_POST["category"] : null;
                // $date_filter = isset($_POST["date"]) && !empty($_POST["date"]) ? $_POST["date"] : null;
                $date_from = isset($_POST["date_from"]) && !empty($_POST["date_from"]) ? $_POST["date_from"] : null;
                $date_to = isset($_POST["date_to"]) && !empty($_POST["date_to"]) ? $_POST["date_to"] : null;

                $search_query = isset($_POST['search']) ? $_POST['search'] : '';
                if ($search_query) {
                    $sql = "SELECT post.*, user.username AS author, category.name AS category
                FROM post 
                LEFT JOIN user ON post.author_id = user.id 
                LEFT JOIN category ON post.category_id = category.id 
                WHERE post.title LIKE '%$search_query%' 
                OR post.content LIKE '%$search_query%' 
                OR user.username LIKE '%$search_query%' 
                ";
                }
                if ($category_filter) {
                    $sql .= " WHERE category.id = $category_filter";
                }

                // if ($date_filter) {
                //     $sql .= $category_filter ? " AND" : " WHERE";
                //     $sql .= " DATE(created_at) = '$date_filter'";
                // }
                if ($date_from && $date_to) {
                    $sql .= " AND DATE(created_at) BETWEEN '$date_from' AND '$date_to'";
                }

                $sql .= " ORDER BY created_at DESC";

                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    echo "Debug: SQL error: " . mysqli_error($conn) . "<br>";
                }

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $likes_query = "SELECT COUNT(*) as count FROM postlike WHERE post_id = " . $row["id"];
                        $likes_result = mysqli_query($conn, $likes_query);
                        $likes_row = mysqli_fetch_assoc($likes_result);
                        $likes_count = $likes_row["count"];
                        echo "<div class='col-lg-4 col-md-6 mb-4'>";
                        echo "<div class='post'>";
                        echo "<h2>" . $row["title"] . "</h2>";
                        echo "<p>" . $row["content"] . "</p>";
                        if (!empty($row['photo'])) {
                            echo "<img src='" . $row['photo'] . "' alt='Post Photo' style='max-width: 300px;'>";
                        }
                        echo "<div class='post-info'>";
                        echo "<span>Author: " . $row["author"] . "</span>";
                        echo "<br>";
                        echo "<span>Category: " . $row["category"] . "</span>";
                        echo "<br>";
                        echo "<span>Created at: " . $row["created_at"] . "</span>";
                        echo "<br>";
                        echo "<span>Likes: " . $likes_count . "</span>";
                        echo "</div>";
                        echo "<br>";
                        echo "<div class='post-actions'>";
                        echo '<a href="like.php?id=' . $row["id"] . '" class="btn btn-primary btn-sm btn-danger mr-2"/><img src="./icons/like.svg"/></a>';
                        echo "<a href='post_comments.php?id=" . $row["id"] . "' class='btn btn-primary btn-sm'><img src='./icons/comment.svg'/></a>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                    mysqli_free_result($result);
                } else {
                    echo "<p>Пост не знайдено!</p>";
                }
                ?>
            </div>
        </div>

    </div>
</body>

</html>