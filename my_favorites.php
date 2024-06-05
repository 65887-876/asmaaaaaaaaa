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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Définir la hauteur minimale du corps à 100 % de la hauteur de la fenêtre */
            margin: 0;
        }

        /* Conteneur principal */
        .main-content {
            flex: 1; /* S'étend pour remplir l'espace disponible */
            padding-bottom: 50px; /* Ajuster au besoin pour s'adapter au pied de page */
        }

        /* Styles des messages de succès */
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

        /* Styles de la section des maisons */
        .home-section {
            padding: 20px;
        }

        .home-title {
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Styles de la grille */
        .home-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .home-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            width: calc(33.333% - 20px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .home-card img,
        .home-card video {
            height: 280px; /* Définir la hauteur à 280px */
            width: 100%;
            object-fit: cover;
        }

        /* Mettre à jour les styles pour le contenu de la carte */
        .home-card-content {
            padding: 15px;
            position: relative; /* Assurer que le z-index fonctionne */
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

        /* Styles des boutons */
        .home-card-buttons {
            padding: 15px;
            display: flex;
            justify-content: center;
            gap: 30px;
            align-items: center;
        }

        .home-card-buttons form {
            margin: 2;
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
        /* Styles des boutons */
        .home-card-buttons .favorite-btn {
            background: #fff; /* Couleur blanche pour le bouton */
            color: grey; /* Couleur noire pour l'icône du cœur */
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s; /* Transition douce au survol */
        }

        /* Effet de survol pour le bouton favori */
        .home-card-buttons .favorite-btn:hover {
            color: red; /* Changer la couleur de l'icône du cœur en rouge au survol */
        }
        /* Couleur rouge pour le cœur lorsqu'il est cliqué */
        .home-card-buttons .favorite-btn.clicked {
            color: red; /* Couleur rouge pour l'icône du cœur */
        }

        .home-card-buttons .edit-btn {
            background: #28a745;
        }

        .home-card-buttons .delete-btn {
            background: #dc3545;
        }

        .home-card-buttons .contact-btn {
            background: #17a2b8;
        }

        /* Styles du carrousel */
        .swiper-container {
            width: 100%;
            height: 260px;
        }

        /* Mettre à jour les styles du carrousel pour masquer uniquement les points de pagination */
        .swiper-container .swiper-pagination {
            display: none;
        }

        /* Mettre à jour les styles pour centrer les images horizontalement */
        .swiper-slide {
            display: flex;
            justify-content: center; /* Centrer horizontalement */
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
                    echo "<h2>" . htmlspecialchars($home['title']) . "</h2>";
                    echo "<p class='price'>" . htmlspecialchars($home['price']) . " DZN</p>";
                    echo "<h3 class='type'>" . htmlspecialchars($home['type'] === 'sell' ? 'À vendre' : 'À louer') . "</h3>";
                    echo "<p>" . htmlspecialchars($home['description']) . "</p>";
                    echo "<p class='posted-by'>Posté par @" . htmlspecialchars($home['username']) . "</p>";
                    echo "</div>";

                    echo "<div class='home-card-buttons'>";
                    echo "<form action='favorite_handler.php' method='POST'>";
                    echo "<input type='hidden' name='home_id' value='" . htmlspecialchars($home['id']) . "'>";
                    echo "<button type='submit' class='favorite-btn'><i class='fas fa-heart'></i></button>";
                    echo "</form>";

                    if ($user_id && $home['user_id'] === $user_id) {
                        echo "<button class='edit-btn' onclick=\"window.location.href='edit_home.php?home_id=" . htmlspecialchars($home['id']) . "'\">✎ Modifier</button>";
                        echo "<form action='delete_home.php' method='POST' style='display:inline-block;'>";
                        echo "<input type='hidden' name='home_id' value='" . htmlspecialchars($home['id']) . "'>";
                        echo "<button type='submit' class='delete-btn' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer cette maison?');\">🗑 Supprimer</button>";
                        echo "</form>";
                    } else {
                        echo "<a href='contact_seller.php?user_id=" . htmlspecialchars($home['user_id']) . "' class='contact-btn'>Contacter le Vendeur</a>";
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
