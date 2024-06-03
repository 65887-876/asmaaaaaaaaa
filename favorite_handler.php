<?php
session_start();
require_once("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; 

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $home_id = $_POST['home_id']; 

    $check_query = "SELECT * FROM favorites WHERE user_id = '$user_id' AND home_id = '$home_id'";
    $result = mysqli_query($con, $check_query);

    if ($result && mysqli_num_rows($result) > 0) {
        // If it's already in favorites, remove it
        $delete_query = "DELETE FROM favorites WHERE user_id = '$user_id' AND home_id = '$home_id'";
        mysqli_query($con, $delete_query);
        $_SESSION['favorite_message'] = 'Home removed from favorites.';
    } else {
        // If it's not in favorites, add it
        $insert_query = "INSERT INTO favorites (user_id, home_id) VALUES ('$user_id', '$home_id')";
        mysqli_query($con, $insert_query);
        $_SESSION['favorite_message'] = 'Home added to favorites.';
    }

    header("Location: my_favorites.php");
    exit; 
}
