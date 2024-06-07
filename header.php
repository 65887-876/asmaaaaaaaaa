<?php

// Define $currentPage variable based on the current page's name or URL
$currentPage = basename($_SERVER['SCRIPT_NAME'], ".php");

// Initialize $isAdmin and $loggedIn variables
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
$loggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skikda Immobilier</title>
    <link rel="stylesheet" href="styles/header.css">
    <style>
        header {
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-bottom: solid 1px #e0e0e0;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: white;
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

        .nav-links a:hover {
            color: green;
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

        .current-page {
            text-decoration: underline;
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
        .logo img {
    width: 100px; /* Increased width */
    height: auto; /* Maintain aspect ratio */
}

    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo" style='display:flex; align-items:center;'>
                <a href="index.php"><img src="logo.jpg" alt="Skikda Immobilier Logo"></a>
                <h3 style='padding-bottom:3px;'>imo-skikda</h3>
            </div>
            <nav>
                <ul class="nav-links">
                    <li <?php if ($currentPage === 'index'): ?>class="current-page"<?php endif; ?>><a href="index.php">Accueil</a></li>
                    <li <?php if ($currentPage === 'services'): ?>class="current-page"<?php endif; ?>><a href="services.php">Les Services</a></li>
                    <li <?php if ($currentPage === 'about'): ?>class="current-page"<?php endif; ?>><a href="about.php">A Propos</a></li>
                    <?php if ($loggedIn): ?>
                        <li <?php if ($currentPage === 'my_favorites'): ?>class="current-page"<?php endif; ?>><a href="my_favorites.php">Favorites</a></li>
                        <?php if ($isAdmin): ?>
                            <li <?php if ($currentPage === 'admin'): ?>class="current-page"<?php endif; ?>><a href="admin.php">Admin</a></li>
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