<?php
session_start();
require_once("connection.php");

// Vérifie si l'utilisateur est authentifié
$user_id = $_SESSION['user_id'] ?? null;

$query = "SELECT homes.*, users.username, 
          (SELECT COUNT(*) FROM favorites WHERE user_id = '$user_id' AND home_id = homes.id) AS is_favorited 
          FROM homes 
          JOIN users ON homes.user_id = users.user_id
          WHERE homes.approved = 1";

$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Explorer les Maisons</title>
    <link rel="stylesheet" type="text/css" href="styles/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .add-house-card {
            background-color: #f9f9f9;
            border: 2px dashed #ccc;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
            width: 300px;
            margin: 10px;
        }
        .add-house-card:hover {
            transform: scale(1.05);
        }

        .plus-sign {
            font-size: 48px;
            line-height: 1;
            margin-bottom: 10px;
        }

        .add-house-card p {
            font-size: 18px;
            margin: 0;
            color: #333;
        }

        .home-card-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            align-items: center;
            padding: 15px;
            border-top: 1px solid #ddd;
        }

        .home-card-buttons form,
        .home-card-buttons a {
            margin: 0;
            padding: 0;
        }

        .contact-btn,
        .delete-btn,
        .edit-btn {
            background: none;
            border: none;
            padding: 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 18px;
            transition: color 0.3s;
        }

        .contact-btn {
            color: #007bff;
        }

        .contact-btn:hover {
            color: #0056b3;
        }

        .delete-btn {
            color: #ff4c4c;
        }

        .delete-btn:hover {
            color: #ff1a1a;
        }

        .edit-btn {
            color: #007bff;
        }

        .edit-btn:hover {
            color: #0056b3;
        }

        .slick-slide img {
            width: 100%;
            height: 260px;
        }

        .slick-slider {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="home-section">
        <h2 class="home-title">Explorer les Maisons</h2>
        <div class="home-grid">
            <div class="add-house-card"> 
                <a href="add_home.php">
                    <div class="plus-sign">+</div>
                    <p>Ajouter un Bien Immobilier</p>
                </a>
            </div> <!-- End of the "Ajouter un Bien Immobilier" card -->
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($home = mysqli_fetch_assoc($result)) {
                    $media_files = json_decode($home['media'], true);
                    echo "<div class='home-card'>";
                    if (!empty($media_files)) {
                        echo "<div class='slick-slider'>";
                        foreach ($media_files as $file) {
                            $file_extension = pathinfo($file, PATHINFO_EXTENSION);
                            echo "<div>";
                            if (in_array(strtolower($file_extension), ["jpg", "jpeg", "png", "gif"])) {
                                echo "<img src='" . htmlspecialchars($file) . "' alt='" . htmlspecialchars($home['title']) . "'>";
                            } elseif (in_array(strtolower($file_extension), ["mp4", "mov", "avi"])) {
                                echo "<video controls><source src='" . htmlspecialchars($file) . "' type='video/$file_extension'></video>";
                            }
                            echo "</div>";
                        }
                        echo "</div>";
                    }

                    echo "<div class='home-card-content'>";
                    echo "<h2>" . htmlspecialchars($home['title']) . "</h2>";
                    echo "<p class='price'>" . htmlspecialchars($home['price']) . " DA</p>";
                    echo "<h3 class='type'>" . htmlspecialchars($home['type'] === 'sell' ? 'À vendre' : 'À louer') . "</h3>";
                    echo "<p>" . htmlspecialchars($home['description']) . "</p>";
                    echo "<p class='posted-by'>Posté par @" . htmlspecialchars($home['username']) . "</p>";
                    echo "</div>";

                    // Section bouton
                    echo "<div class='home-card-buttons'>"; 
                    
                    // Lien pour contacter le vendeur
                    echo "<a href='contact_seller.php?user_id=" . htmlspecialchars($home['user_id']) . "' class='contact-btn'><i class='fas fa-envelope'></i></a>"; 

                    // Bouton Supprimer uniquement pour le propriétaire
                    if ($user_id && $home['user_id'] === $user_id) {
                        echo "<form action='delete_home.php' method='POST' style='display:inline-block;'>";
                        echo "<input type='hidden' name='home_id' value='" . htmlspecialchars($home['id']) . "'>";
                        echo "<button type='submit' class='delete-btn' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer cette maison?');\"><i class='fas fa-trash'></i></button>";
                        echo "</form>";
                    }
                    
                    echo "</div>"; 

                    echo "</div>"; // Fin de la carte
                }
            } else {
                echo "<p>Aucune annonce disponible actuellement.</p>"; 
            }
            ?>
        </div> 
    </div>
    <?php include 'footer.php'; ?> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.slick-slider').slick({
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