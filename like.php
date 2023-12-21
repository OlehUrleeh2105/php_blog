<?php
require_once 'db.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$post_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$sql = "SELECT * FROM postlike WHERE post_id=$post_id AND user_id='{$_SESSION['user_id']}'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Ви вже вподобали цей пост!";
} else {
    $sql = "INSERT INTO postlike (post_id, user_id) VALUES ('$post_id', '{$_SESSION['user_id']}')";

    if ($conn->query($sql) === TRUE) {
        echo "Ви успішно вподобали цю публікацію!";
        
    } else {
        echo "Помилка: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
header("Refresh: 0; url=posts.php");
