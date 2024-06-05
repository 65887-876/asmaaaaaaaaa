<?php
// Start the session
session_start();

// Include the database connection file
require_once("connection.php");

// Get the user_id from the session variable
$user_id = $_SESSION['user_id'] ?? null;

// Check if the 'title' parameter is set in the POST request
if(isset($_POST['title'])) {
    // Sanitize the input to prevent SQL injection
    $selectedTitle = mysqli_real_escape_string($con, $_POST['title']);

    // Construct the SQL query to fetch homes based on the selected title
    if(empty($selectedTitle)) {
        // If "Tous les titres" is selected, fetch all homes
        $query = "SELECT homes.*, users.username, 
        (SELECT COUNT(*) FROM favorites WHERE user_id = '$user_id' AND home_id = homes.id) AS is_favorited 
        FROM homes 
        JOIN users ON homes.user_id = users.user_id
        WHERE homes.approved = 1";
    } else {
        // If a specific title is selected, fetch homes matching that title
        $query = "SELECT homes.*, users.username, 
        (SELECT COUNT(*) FROM favorites WHERE user_id = '$user_id' AND home_id = homes.id) AS is_favorited 
        FROM homes 
        JOIN users ON homes.user_id = users.user_id
        WHERE homes.approved = 1 AND homes.title = '$selectedTitle'";
    }

    // Execute the query
    $result = mysqli_query($con, $query);

    // Check if there are any matching homes
    if(mysqli_num_rows($result) > 0) {
        // Loop through the results and generate HTML content for each home
        while($home = mysqli_fetch_assoc($result)) {
            // Output HTML content for the home
            echo "<div class='home-card'>";
            // Output home card slider
            echo "<div class='home-card-slider'>";
            // Output media files for the home
            $media_files = json_decode($home['media'], true);
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
            echo "</div>"; // End home card slider

            // Output home card content
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
            echo "</div>"; // End home card content

            echo "<div class='home-card-buttons'>";
            // Output form for adding/removing from favorites
            // Output buttons for editing/deleting home
            // Output button for contacting seller
            echo "</div>"; // End home card buttons
            
            echo "</div>"; // End home card
        }
    } else {
        if(empty($selectedTitle)) {
            echo "<p>Tous les titres</p>";
        } else {
            echo "<p>Aucune maison trouvée pour ce titre.</p>";
        }
    }
} else {
    echo "<p>Erreur: Aucun titre sélectionné.</p>";
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Explorateur d'immobilier</title>
    <link rel="stylesheet" type="text/css" href="styles/home.css">
    <!-- Include Slick CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
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
    });
</script>
</body>
</html>
