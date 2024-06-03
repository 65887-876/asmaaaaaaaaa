<?php 
session_start();

include("connection.php");
include("functions.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $age = $_POST['age'];

    if (!empty($username) && !empty($password) && !empty($email) && !empty($phone) && !empty($age)) {
        $user_id = random_num(20);

        $query = "INSERT INTO users (user_id, username, password, email, phone, age) 
                  VALUES ('$user_id', '$username', '$password', '$email', '$phone', '$age')";

        mysqli_query($con, $query);

        header("Location: login.php");
        die;
    } else {
        echo 'Veuillez entrer des informations valides';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Page d'inscription</title>
    <link rel="stylesheet" type="text/css" href="styles/signup.css">
</head>
<body>
    <h2>Inscription</h2>
    <form action="signup.php" method="POST"> 
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" name="username" required><br>

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" required><br>

        <label for="email">Email :</label>
        <input type="email" name="email" required><br>

        <label for="phone">Numéro de téléphone :</label>
        <input type="text" name="phone" required><br>

        <label for="age">Âge :</label>
        <input type="number" name="age" required min="1"><br>

        <button type="submit">S'inscrire</button>
    </form>
    <p>Vous avez déjà un compte ? <a href="login.php">Connectez-vous ici</a>.</p>
</body>
</html>
