<?php
session_start();
require_once("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$home_id = $_POST['home_id'] ?? null;

if ($home_id) {
    $query = "DELETE FROM favorites WHERE user_id = '$user_id' AND home_id = '$home_id'";
    if (mysqli_query($con, $query)) {
        $_SESSION['favorite_message'] = "Home has been removed from your favorites.";
    } else {
        $_SESSION['favorite_message'] = "Failed to remove home from favorites. Please try again.";
    }
} else {
    $_SESSION['favorite_message'] = "Invalid request.";
}

header("Location: favorites.php");
exit;
