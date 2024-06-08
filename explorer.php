<?php
session_start();
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
    <title>Explorer les Maisons</title>
    <link rel="stylesheet" type="text/css" href="styles/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
/* Add the existing styles */
.add-house-card {
    display: flex; /* Flex layout */
    flex-direction: column; /* Vertical alignment */
    justify-content: center; /* Center align */
    align-items: center; /* Center align */
    border-radius: 10px; /* Rounded corners */
    background: #f9f9f9; /* Light background */
    cursor: pointer; /* Indicate clickability */
    transition: background 0.3s; /* Smooth transition on hover */
}

.add-house-card:hover {
    background: #e0e0e0; /* Darker background on hover */
}
.home-card-content .title{
    margin:0px;
}
.plus-sign {
    font-size: 3rem; /* Large font for the plus sign */
    color: #555; /* Dark gray color */
}

.add-house-card a {
    text-decoration: none; /* No underline */
    color: #333; /* Text color */
    text-align: center; /* Center align */
}
.home-card-content{
    margin-top:-18px;
}
.home-card-content .price {
    margin:0px;
}
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="home-section">
        <h2 class="home-title">Explorer nos immobilier</h2>
        <div class="home-grid">
            <!-- "Ajouter un Bien Immobilier" card -->
            <div class="home-card add-house-card">
                <a href="add_home.php">
                    <div class="plus-sign">+</div>
                    <p>Ajouter un Bien Immobilier</p>
                </a>
            </div>
            <?php
if ($result && mysqli_num_rows($result) > 0) {
    while ($home = mysqli_fetch_assoc($result)) {
        // Define $is_favorited here
        $is_favorited = $home['is_favorited']; // Assuming it's returned in the query result
        
        $media_files = json_decode($home['media'], true);
        echo "<div class='home-card'>";
        
        // Display media files
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

        // Display home card content
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

        // Display home card buttons
        echo "<div class='home-card-buttons'>";
        echo "<form action='favorite_handler.php' method='POST'>";
        echo "<input type='hidden' name='home_id' value='" . htmlspecialchars($home['id']) . "'>";
        echo "<button type='submit' class='favorite-btn" . ($is_favorited ? " favorited" : "") . "'><i class='fas fa-heart" . ($is_favorited ? " clicked" : "") . "'></i></button>";
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

        echo "</div>"; // End of home-card-buttons div

        echo "</div>"; // End of home-card div
    }
} else {
    echo "<p>Pas de maisons disponibles actuellement.</p>";
}
?>
        </div>
    </div>

    <?php include 'footer.php'; ?> 
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