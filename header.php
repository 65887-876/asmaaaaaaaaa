<?php
$loggedIn = isset($_SESSION['user_id']); // Check if user is logged in
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1; // Check if user is admin
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/header.css">
    <style>
        header {
            background: white;
            padding: 20px 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-bottom: solid 1px #e0e0e0;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: white;
        }

        .logo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
        }

        .nav-links {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .nav-links li {
            margin-right: 25px;
        }

        .nav-links a {
            text-decoration: none;
            font-size: 18px;
            color: black;
            transition: color 0.3s;
        }

        .auth-buttons {
            display: flex;
            align-items: center;
        }

        .auth-buttons a {
            text-decoration: none;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            transition: background 0.3s, color 0.3s;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-left: 10px;
        }

        .login-btn {
            background: grey;
            color: white;
            border: 2px solid #333;
        }

        .login-btn:hover {
            background: green;
            color: white;
        }

        .signup-btn {
            background: grey;
            color: white;
        }

        .signup-btn:hover {
            background: green;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <h3>Skikda Immobilier</h3>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="services.php">Les Services</a></li>
                    <li><a href="about.php">A Propos</a></li>
                    <?php if ($loggedIn): ?>
                        <li><a href="my_favorites.php">Favorites</a></li>
                        <?php if ($isAdmin): ?>
                            <li><a href="admin.php">Admin</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if ($loggedIn): ?>
                    <a href="logout.php" class="login-btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="login-btn">Log-in</a>
                    <a href="signup.php" class="signup-btn">Sign-Up</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
</body>
</html>
