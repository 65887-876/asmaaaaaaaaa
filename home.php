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
    <meta charset="UTF-8">
    <title>Explorateur d'immobilier</title>
    <link rel="stylesheet" type="text/css" href="styles/home.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
.home-card-content {
    margin:-5px;
}
    </style>
</head>
<body>
    <div class="home-section">
        <h2 class="home-title" style="font-size:40px;">Explorer nos immobilier</h2>
        <div style="display:flex;">
    <label style="padding-top:27px;padding-right:18px;" ><h3 style='color:black;'>filtrer par:</h3></label>
    <div style="display:flex;flex-direction:column;">
    <h4 style="display:flex;justify-content:center;">Categorie</h4>        
    <select id="title-filter">
        <option value="">Tous</option>
        <?php
        $titles_query = mysqli_query($con, "SELECT DISTINCT title FROM homes");
        while ($title_row = mysqli_fetch_assoc($titles_query)) {
            echo "<option value='" . htmlspecialchars($title_row['title']) . "'>" . htmlspecialchars($title_row['title']) . "</option>";
        }
        ?>
    </select>
    </div>
    <div style="display:flex;flex-direction:column; margin-left:30px;">


    <h4 style="display:flex;justify-content:center;" >V/L</h4>
    <select id="type-filter" class="type-filtrer">
        <option value="">Tous</option>
        <option value="sell">À vendre</option>
        <option value="rent">À louer</option>
    </select>
    </div>
</div>
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
        echo "<h2 class='title'>" . htmlspecialchars($home['title']) . "</h2>";
        echo "<p class='price'>" . htmlspecialchars($home['price']) . " DA";

        if ($home['type'] === 'rent') {
            echo " / " . htmlspecialchars($home['price_period']);
        }

        echo "</p>";
        echo "<h3>" . htmlspecialchars($home['address']) . "</h3>";
        echo "<h3 class='type'>" . htmlspecialchars($home['type'] === 'sell' ? 'À vendre' : 'À louer') . "</h3>";
        echo "<p style='padding-bottom:20px;'>" . htmlspecialchars($home['description']) . "</p>";
        echo "<h2 class='posted-by'>Posté par @" . htmlspecialchars($home['username']) . "</h2>";
        echo "</div>";

        echo "<div class='home-card-buttons'>";
        echo "<form action='favorite_handler.php' method='POST'>";
        echo "<input type='hidden' name='home_id' value='" . htmlspecialchars($home['id']) . "'>";
        echo "<button type='submit' class='favorite-btn" . ($is_favorited ? " favorited" : "") . "'><i class='fas fa-heart" . ($is_favorited ? " clicked" : "") . "'></i></button>";
        echo "</form>";

        if ($user_id && $home['user_id'] === $user_id) {
            echo "<button class='edit-btn' onclick=\"window.location.href='edit_home.php?home_id=" . htmlspecialchars($home['id']) . "'\"><i class='fas fa-edit'></i></button>";
            echo "<form action='delete_home.php' method='POST' style='display:inline-block;'>";
            echo "<input type='hidden' name='home_id' value='" . htmlspecialchars($home['id']) . "'>";
            echo "<button type='submit' class='delete-btn' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer cette maison?');\"><i class='fas fa-trash'></i></button>";
            echo "</form>";
        } else {
            echo "<a href='contact_seller.php?user_id=" . htmlspecialchars($home['user_id']) . "' class='contact-btn'><i class='fas fa-envelope'></i></a>";
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

    <!-- Include Slick JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script>
    $(document).ready(function(){
        // Initialize Slick slider
        $('.home-card-slider').slick({
            dots: true,
            infinite: true,
            speed: 300,
            slidesToShow: 1,
            adaptiveHeight: true
        });

        // Handle select change event
        $('#title-filter, #type-filter').change(function() {
            var selectedTitle = $('#title-filter').val();
            var selectedType = $('#type-filter').val();

            // Send an AJAX request to fetch filtered homes based on selected title and type
            $.ajax({
                url: 'fetch_filtered_homes.php',
                type: 'POST',
                data: { title: selectedTitle, type: selectedType },
                success: function(response) {
                    $('.home-grid').html(response); // Replace existing homes with filtered homes
                    $('.home-card-slider').slick('unslick'); // Unslick the slider
                    $('.home-card-slider').slick({ // Reinitialize Slick slider
                        dots: true,
                        infinite: true,
                        speed: 300,
                        slidesToShow: 1,
                        adaptiveHeight: true
                    });
                }
            });
        });
    });
    </script>
</body>
</html>
