<?php
session_start();
require_once("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    die;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $address = $_POST['address'];
    $price = $_POST['price'];
    $price_period = $_POST['price_period'];
    $type = $_POST['type'];
    $media_paths = [];

    if (isset($_FILES['media']) && count($_FILES['media']['name']) <= 6) {
        $allowed_extensions = ["jpg", "jpeg", "png", "gif", "mp4", "mov", "avi"];
        foreach ($_FILES['media']['name'] as $key => $name) {
            if ($_FILES['media']['error'][$key] === UPLOAD_ERR_OK) {
                $file_extension = pathinfo($name, PATHINFO_EXTENSION);
                if (in_array(strtolower($file_extension), $allowed_extensions)) {
                    $unique_filename = uniqid() . '.' . $file_extension;
                    $file_path = 'uploads/' . $unique_filename;
                    if (move_uploaded_file($_FILES['media']['tmp_name'][$key], $file_path)) {
                        $media_paths[] = $file_path;
                    } else {
                        echo "Erreur lors du déplacement du fichier téléchargé.";
                    }
                } else {
                    echo "Type de fichier non valide. Veuillez télécharger une image JPG, JPEG, PNG";
                }
            }
        }
    } else {
        echo "Vous pouvez télécharger jusqu'à 6 fichiers.";
    }

    if (!empty($title) && !empty($description) && !empty($price) && !empty($price_period) && !empty($type) && !empty($address) && !empty($media_paths)) {
        $media_paths_json = json_encode($media_paths);
        $query = "INSERT INTO homes (user_id, title, description, address, price, price_period, type, media, approved) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";
        if ($stmt = mysqli_prepare($con, $query)) {
            mysqli_stmt_bind_param($stmt, "isssssss", $user_id, $title, $description, $address, $price, $price_period, $type, $media_paths_json);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: index.php");
                die;
            } else {
                echo "Erreur de base de données : " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Échec de la préparation de la requête : " . mysqli_error($con);
        }
    } else {
        echo "Veuillez saisir un titre, une description, un prix, une période de prix, une adresse, un type valides, et au moins un fichier multimédia.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Ajouter une Maison ou une Villa</title>
    <link rel="stylesheet" href="styles/addhome.css">
</head>
<body>
<?php include 'header.php'; ?>

<h1>Ajouter votre annonce</h1>

<div class="container">
    <form action="add_home.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Type d'Annonce</label>
            <input type="text" name="title" required>
        </div>

        <div class="form-group">
            <label for="price">Prix </label>
            <div class="price-container">
                <input type="text" name="price" class="price-input" required>
            </div>
            <select name="price_period" >
                <option value="" disabled selected hidden>Sélectionnez une période</option>
                <option value="total">Total</option>
                <option value="par mois">Par mois</option>
                <option value="par ans">Par an</option>
            </select>
        </div>
        <div class="form-group">
            <label for="address">Adresse </label>
            <input type="text" name="address" required>
        </div>


        <div class="lone">
    <label style="flex:0;" for="price">Type</label>
    <select name="type" required>
        <option value="" disabled selected hidden>Sélectionnez un type</option>
        <option value="sell">À vendre</option>
        <option value="rent">À louer</option>
    </select>
</div>

        <div class="form-group file-upload">
            <label for="media">Images (jusqu'à 6)</label>
            <input type="file" name="media[]" multiple accept="image/*">
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" required></textarea>
        </div>
        
        <div class="line">
            <p><a href="index.php">Retour à l'Accueil</a></p>
            <button type="submit" class="hey">Publier</button>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
</body>
</html>