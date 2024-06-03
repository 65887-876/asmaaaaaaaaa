<?php
session_start();
require_once("connection.php");

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    die;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve'])) {
        $home_id = $_POST['home_id'];
        $query = "UPDATE homes SET approved = 1 WHERE id = '$home_id'";
        mysqli_query($con, $query);
    } elseif (isset($_POST['delete'])) {
        $home_id = $_POST['home_id'];
        $query = "DELETE FROM homes WHERE id = '$home_id'";
        mysqli_query($con, $query);
    }
}

$query = "SELECT homes.*, users.username FROM homes JOIN users ON homes.user_id = users.user_id WHERE homes.approved = 0";
$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Panel Administrateur</title>
    <link rel="stylesheet" href="styles/admin.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
</head>
<body>
<?php include 'header.php'; ?>

    <div class="container">
        <h2>Panel Administrateur : Approuver ou Supprimer les Biens Immobiliers</h2>

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
                    echo "<h3>" . htmlspecialchars($home['title']) . "</h3>";
                    echo "<p>" . htmlspecialchars($home['description']) . "</p>";
                    echo "<p>Prix : " . htmlspecialchars($home['price']) . " DZN</p>";
                    echo "<p>Posté par @" . htmlspecialchars($home['username']) . "</p>";

                    echo "<form method='POST' action='admin.php'>";
                    echo "<input type='hidden' name='home_id' value='" . htmlspecialchars($home['id']) . "'>";
                    echo "<button type='submit' name='approve' class='approve-btn'>Approuver</button>";
                    echo "<button type='submit' name='delete' class='delete-btn' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer cette maison?');\">Supprimer</button>";
                    echo "</form>";

                    echo "</div>";
                }
            } else {
                echo "<p>Aucun bien immobilier en attente d'approbation.</p>";
            }
            ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>

    <script type="text/javascript">
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
