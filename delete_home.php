<?php
session_start();
require_once("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    die;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $home_id = $_POST['home_id'];
    $user_id = $_SESSION['user_id'];

    // Verify that the home belongs to the user
    $query = "SELECT * FROM homes WHERE id = '$home_id' AND user_id = '$user_id'";
    $result = mysqli_query($con, $query);
    $home = mysqli_fetch_assoc($result);

    if ($home) {
        // Delete the home
        $delete_query = "DELETE FROM homes WHERE id = '$home_id' AND user_id = '$user_id'";
        if (mysqli_query($con, $delete_query)) {
            // Optionally, delete the associated picture file
            if (!empty($home['picture'])) {
                unlink($home['picture']);
            }
            header("Location: index.php");
            die;
        } else {
            echo "Annonce error" . mysqli_error($con);
        }
    } else {
        echo "Annonce non trouvée ou vous n'avez pas la permission de supprimer cette Annonce.";

    }
}
?>
