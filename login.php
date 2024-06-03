<?php 
session_start();
include("connection.php");
include("functions.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_identifier = $_POST['username'];  
    $password = $_POST['password'];

    if (!empty($user_identifier) && !empty($password)) {
        $query = "SELECT * FROM users WHERE username = '$user_identifier' OR email = '$user_identifier' LIMIT 1";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);

            if ($user_data["password"] == $password) {
                $_SESSION['user_id'] = $user_data['user_id'];
                $_SESSION['is_admin'] = $user_data['is_admin'];  // Set admin session variable
                header("Location: index.php");
                die;
            } else {
                echo 'Invalid password';
            }
        } else {
            echo 'User not found';
        }
    } else {
        echo 'Please enter valid information';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login Page</title>
    <link rel="stylesheet" type="text/css" href="styles/login.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        h2 {
            text-align: center;
            margin-top: 30px;
            color: #333;
        }

        form {
            width: 300px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #fff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 15px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        p {
            text-align: center;
        }

        a {
            text-decoration: none;
            color: #4CAF50;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Login</h2>
    <form action="login.php" method="POST"> 
        <label for="username">Username or Email:</label>
        <input type="text" name="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="signup.php">Signup here</a>.</p>
</body>
</html>
