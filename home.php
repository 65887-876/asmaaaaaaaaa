<?php
require_once("connection.php");

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
    <link rel="stylesheet" type="text/css" href="styles/home.css">
    <!-- Include Slick CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>
    <div class="home-section">
        <h2 class="home-title">Explorer nos immobilier</h2>

        <div class="home-grid">
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($home = mysqli_fetch_assoc($result)) {
                    $is_favorited = false; // Default to false
                    if ($user_id) {
                        // Check if the current user has favorited this home
                        $favorite_check = mysqli_query($con, "SELECT COUNT(*) AS count FROM favorites WHERE user_id = '$user_id' AND home_id = '{$home['id']}'");
                        $is_favorited = (mysqli_fetch_assoc($favorite_check)['count'] > 0);
                    }

                    $media_files = json_decode($home['media'], true);
                    echo "<div class='home-card'>";
                    if (!empty($media_files)) {
                        echo "<div class='home-card-slider'>";
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
                    echo "<p class='price'>" . htmlspecialchars($home['price']) . " DZN";

                    if ($home['type'] === 'rent') {
                        echo " / " . htmlspecialchars($home['price_period']);
                    }

                    echo "</p>";
                    echo "<h3>" . htmlspecialchars($home['address']) . "</h3>";
                    echo "<h3 class='type'>" . htmlspecialchars($home['type'] === 'sell' ? 'À vendre' : 'À louer') . "</h3>";
                    echo "<p>" . htmlspecialchars($home['description']) . "</p>";
                    echo "<p class='posted-by'>Posté par @" . htmlspecialchars($home['username']) . "</p>";
                    echo "</div>";

                    echo "<div class='home-card-buttons'>";
                    echo "<form action='favorite_handler.php' method='POST'>";
                    echo "<input type='hidden' name='home_id' value='" . htmlspecialchars($home['id']) . "'>";
                    echo "<button type='submit' class='favorite-btn" . ($is_favorited ? " favorited" : "") . "'><i class='fas fa-heart" . ($is_favorited ? " clicked" : "") . "'></i></button>";
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
                echo "<p>Pas de maisons disponibles actuellement.</p>";
            }
            ?>
        </div>
    </div>


    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Slick JS -->
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
