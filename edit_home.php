<?php
session_start();
require_once("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    die;
}

$home_id = $_GET['home_id'] ?? null;
$user_id = $_SESSION['user_id']; 

$query = "SELECT * FROM homes WHERE id = '$home_id' AND user_id = '$user_id'";
$result = mysqli_query($con, $query);
$home = mysqli_fetch_assoc($result);

if (!$home) {
    header("Location: index.php");
    die;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $price_period = $_POST['price_period'] ?? '';
    $type = $_POST['type'] ?? '';

    $price = $_POST['price'] ?? '';
    $address = $_POST['address'] ?? '';
    $media_paths = $home['media'] ? json_decode($home['media'], true) : [];

    // Handle image deletion
    if (isset($_POST['delete_image'])) {
        $delete_indices = $_POST['delete_image']; // Get the indices of the images to delete
        foreach ($delete_indices as $index) {
            if (isset($media_paths[$index])) {
                if (file_exists($media_paths[$index])) { // Check if the file exists before unlinking
                    unlink($media_paths[$index]); // Remove the image file from the server
                }
                unset($media_paths[$index]); // Remove the image path from the array
            }
        }
    }

    // Process uploaded images
    if (!empty($_FILES['media']['name'][0])) {
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
        foreach ($_FILES['media']['name'] as $key => $name) {
            if ($_FILES['media']['error'][$key] === UPLOAD_ERR_OK) {
                $file_extension = pathinfo($name, PATHINFO_EXTENSION);
                if (in_array(strtolower($file_extension), $allowed_extensions)) {
                    $unique_filename = uniqid() . '.' . $file_extension;
                    $file_path = 'uploads/' . $unique_filename;
                    if (move_uploaded_file($_FILES['media']['tmp_name'][$key], $file_path)) {
                        $media_paths[] = $file_path;
                    } else {
                        echo "Error moving the uploaded file.";
                    }
                } else {
                    echo "Invalid file type. Please upload a JPG, JPEG, PNG, or GIF image.";
                }
            }
        }
    }

    // Update home details in database
    if (!empty($title) && !empty($description) && !empty($price) && !empty($address)) {
        if (empty($media_paths)) {
            echo "Veuillez télécharger au moins une photo.";
        } else {
            $media_paths_json = json_encode($media_paths);
            $update_query = "UPDATE homes SET title = ?, description = ?, price = ?, type = ?, price_period = ?, media = ?, address = ? WHERE id = ? AND user_id = ?";
            if ($stmt = mysqli_prepare($con, $update_query)) {
                mysqli_stmt_bind_param($stmt, "ssssssssi", $title, $description, $price, $type, $price_period, $media_paths_json, $address, $home_id, $user_id);
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: index.php");
                    die;
                } else {
                    echo "Database error: " . mysqli_stmt_error($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Database error: " . mysqli_error($con);
            }
        }
    } else {
        echo "Veuillez saisir un titre, une description, un prix et une adresse valides.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Modifier votre Bien Immobilier</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles/edit_home.css">
    <style>
    .image-container {
        display: inline-block;
        vertical-align: top; /* Align images to the top */
        margin-right: 10px; /* Add some spacing between images */
    }
    .image-container img {
        max-width: 100px; /* Limit the maximum width of images */
        height: 80px; /* Set a fixed height for all images */
        object-fit: cover; /* Maintain aspect ratio while covering the container */
    }
    </style>
</head>
<body>
    <?php include 'header.php'?>
    <h2>Modifier votre annonce</h2>
    
    <form action="edit_home.php?home_id=<?php echo htmlspecialchars($home_id); ?>" method="POST" class="container" enctype="multipart/form-data">
    <div class="form-group" style='height:70px;'>
        <label style='padding-left:55px;' for="title">Type d'Annonce</label>
        <div class="input-container">
            <input type="text" name="title" value="<?php echo isset($home['title']) ? htmlspecialchars($home['title']) : ''; ?>" required>
        </div>
    </div>

    <div class="form-group" style='height:70px;'>
        <label style='padding-left:55px;' for="price">Prix</label>
        <div class="input-container" style="width:75px; margin-right:160px;">
            <input type="text" name="price" value="<?php echo isset($home['price']) ? htmlspecialchars($home['price']) : ''; ?>" required>
        </div>
        <select name="price_period" >
                <option value="" disabled selected hidden>Sélectionnez une période</option>
                <option value="total">Total</option>
                <option value="par mois">Par mois</option>
                <option value="par ans">Par an</option>
            </select>
    </div>
    <div class="form-group" style='height:70px;'>
        <label style='padding-left:55px;' for="address">Adresse</label>
        <div class="input-container">
            <input type="text" name="address" value="<?php echo isset($home['address']) ? htmlspecialchars($home['address']) : ''; ?>" required>
        </div>
    </div>
    <div style = "display: flex;padding-bottom:10px;height:20px;">
            <label  style='padding-left:55px;' for="type">Type</label>
            <select name="type" required>
                <option value="" disabled selected hidden>Sélectionnez un type</option>
                <option value="sell">À vendre</option>
                <option value="rent">À louer</option>
            </select>
        </div>
    <div class="form-group" style='height:70px;padding-left:55px;'>
        <label for="media">Images</label>
        <input style='padding-left:15px;' type="file" name="media[]" multiple accept="image/*">
    </div>


    <?php if (!empty($home['media'])): ?>
    <div class="form-group">
        <label style='padding-left:55px;''>cocher pour suprimmer </label>
        <?php $media_paths = json_decode($home['media'], true); ?>
        <?php foreach ($media_paths as $key => $path): ?>
            <input type="hidden" name="existing_media[]" value="<?php echo $path; ?>">
            <div class="image-container">
                <img src="<?php echo htmlspecialchars($path); ?>" alt="Image Actuelle" style="max-width: 100px;"><br>
                <input type="checkbox" name="delete_image[]" value="<?php echo $key; ?>"> 
                <label for="delete_image[]"></label> <!-- Trash bin icon -->
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>



    <div  style='padding-left:55px;' class="form-group">
        <label for="description">Description</label>
        <div class="input-container">
            <textarea name="description" required><?php echo isset($home['description']) ? htmlspecialchars($home['description']) : ''; ?></textarea>
        </div>
    </div>
    <div class="risk">
        <p><a href="index.php">Retour à l'Accueil</a></p> 
        <button type="submit">Mettre à Jour la Maison</button>
    </div>

</form>

<?php include 'footer.php'?>
</body>
</html>
