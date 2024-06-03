<?php
session_start();

require_once("connection.php");
require_once("functions.php");

$loggedIn = isset($_SESSION['user_id']);
$user_data = $loggedIn ? check_login($con) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Booking Clone</title>
    <link rel="stylesheet" href="styles/index.css">
</head>
<body>
    <?php require_once 'header.php'; ?>

    <main>
        <?php include 'hero.php'; ?>

        <?php include 'location.php'; ?> 

        <?php include 'home.php'; ?>


    </main>
    
    <?php include 'footer.php'; ?> 

</body>
</html>
