<?php
session_start();
require_once("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT homes.*, users.username 
          FROM favorites 
          JOIN homes ON favorites.home_id = homes.id 
          JOIN users ON homes.user_id = users.user_id 
          WHERE favorites.user_id = '$user_id'";

$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Mes Favoris</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            margin: 0;
        }

        .main-content {
            flex: 0;
            padding-bottom: 50px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #c3e6cb;
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
        }

        .home-section {
            padding: 20px;
        }

        .home-title {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .home-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .home-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            width: calc(33.333% - 20px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .home-card img,
        .home-card video {
            height: 280px;
            width: 100%;
            object-fit: cover;
        }

        .home-card-content {
            padding: 15px;
            flex-grow: 1;
            gap:1px;
        }

        .home-card-content h3 {
            margin: 0;
            font-size: 18px;
        }

        .home-card-content .price {
            color: orange;
            font-size: 18px;
            font-weight: bold;
        }

        .home-card-content .type {
            color: black;
            font-size: 24px;
        }

        .home-card-content .posted-by {
            font-size: 14px;
            color: #555;
        }

        .home-card-buttons {
            padding: 15px;
            display: flex;
            justify-content: center;
            gap: 30px;
            align-items: center;
        }

        .home-card-buttons form {
            margin: 0;
        }

        .home-card-buttons .edit-btn,
        .home-card-buttons .delete-btn,
        .home-card-buttons .contact-btn {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .home-card-buttons .favorite-btn {
            background: #fff;
            color: red;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .home-card-buttons .favorite-btn:hover {
            color: grey;
        }

        .home-card-buttons .favorite-btn.clicked {
            color: grey;
        }
        .home-card-buttons i {
            font-size: 24px; 
        }
        .home-card-buttons .edit-btn {
            background: #fff;
            color: green;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .home-card-buttons .delete-btn {
            background: #fff;
            color: red;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .home-card-buttons .contact-btn {
            background: #17a2b8;
        }

        .swiper-container {
            width: 100%;
            height: 260px;
        }

        .swiper-container .swiper-pagination {
            display: none;
        }

        .swiper-slide {
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f9f9f9;
        }

        footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            width: 100%;
            position: fixed;
            bottom: 0;
        }
    </style>

</head>
<body>
    <?php include 'header.php'; ?>

    <div class="home-section">
        <?php if (isset($_SESSION['favorite_message'])): ?>
            <div class="success-message">
                <?php 
                echo htmlspecialchars($_SESSION['favorite_message']);
                unset($_SESSION['favorite_message']);
                ?>
            </div>
        <?php endif; ?>

        <h2 class="home-title">Mes Favoris</h2>

        <div class="home-grid">
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($home = mysqli_fetch_assoc($result)) {
                    $media_files = json_decode($home['media'], true);
                    echo "<div class='home-card'>";
                    if (!empty($media_files)) {
                        echo "<div class='home-card-slider'>";
                        foreach ($media_files as $file) {
                            $file_extension = pathinfo($file, PATHINFO_EXTENSION);
                            if (in_array(strtolower($file_extension), ["jpg", "jpeg", "png", "gif"])) {
                                echo "<div><img src='" . htmlspecialchars($file) . "' alt='" . htmlspecialchars($home['title']) . "' class='home-card-image'></div>";
                            } elseif (in_array(strtolower($file_extension), ["mp4", "mov", "avi"])) {
                                echo "<div><video controls class='home-card-video'><source src='" . htmlspecialchars($file) . "' type='video/$file_extension'></video></div>";
                            }
                        }
                        echo "</div>";
                    }
                    echo "<div class='home-card-content'>";
                    echo "<h2 style='margin:0px;'>" . htmlspecialchars($home['title']) . "</h2>";
                    echo "<p class='price' style='margin:0px;'>" . htmlspecialchars($home['price']) . " DA</p>";
                    echo "<h3 class='type'>" . htmlspecialchars($home['type'] === 'sell' ? 'À vendre' : 'À louer') . "</h3>";
                    echo "<p style='padding-bottom:20px;'>" . htmlspecialchars($home['description']) . "</p>";
                    echo "<h2 class='posted-by'>Posté par @" . htmlspecialchars($home['username']) . "</h2>";
                    echo "</div>";

                    echo "<div class='home-card-buttons'>";
                    echo "<form action='favorite_handler.php' method='POST' style='display:inline-block;'>";
                    echo "<input type='hidden' name='home_id' value='" . htmlspecialchars($home['id']) . "'>";
                    echo "<button type='submit' class='favorite-btn'><i class='fas fa-heart'></i></button>";
                    echo "</form>";

                    if ($user_id && $home['user_id'] === $user_id) {
                        echo "<button class='edit-btn' onclick=\"window.location.href='edit_home.php?home_id=" . htmlspecialchars($home['id']) . "'\"><i class='fas fa-edit'></i></button>";
                        echo "<form action='delete_home.php' method='POST' style='display:inline-block;'>";
                        echo "<input type='hidden' name='home_id' value='" . htmlspecialchars($home['id']) . "'>";
                        echo "<button type='submit' class='delete-btn' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer cette Annonce?');\"><i class='fas fa-trash'></i></button>";
                        echo "</form>";
                    } else {
                        echo "<a href='contact_seller.php?user_id=" . htmlspecialchars($home['user_id']) . "' class='contact-btn'><i class='fas fa-envelope'></i></a>";
                    }

                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>Pas de favoris ici</p>";
            }
            ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.home-card-slider').slick({
                dots: true,
                infinite: true,
                speed: 300,
                slidesToShow: 1,
                adaptiveHeight: true
            });
        });
    </script>
</body>
</html>
