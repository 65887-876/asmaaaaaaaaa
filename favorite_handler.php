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

    $check_query = "SELECT * FROM favoris WHERE user_id = '$user_id' AND home_id = '$home_id'";
    $result = mysqli_query($con, $check_query);

    if ($result && mysqli_num_rows($result) > 0) {
        // Si c'est déjà dans les favoris, le retirer
        $delete_query = "DELETE FROM favoris WHERE user_id = '$user_id' AND home_id = '$home_id'";
        mysqli_query($con, $delete_query);
        $_SESSION['favorite_message'] = 'Maison retirée des favoris.';
    } else {
        // Si ce n'est pas dans les favoris, l'ajouter
        $insert_query = "INSERT INTO favoris (user_id, home_id) VALUES ('$user_id', '$home_id')";
        mysqli_query($con, $insert_query);
        $_SESSION['favorite_message'] = 'Maison ajoutée aux favoris.';
    }

    header("Location: mes_favoris.php");
    exit; 
}
