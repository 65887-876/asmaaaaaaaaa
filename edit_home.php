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
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
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
    if (!empty($_FILES['media']['name'])) {
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
    if (!empty($title) && !empty($description) && !empty($price)) {
        // Check if there are any photos
        if (empty($media_paths)) {
            echo "Veuillez télécharger au moins une photo.";
        } else {
            $media_paths_json = json_encode($media_paths);
            $update_query = "UPDATE homes SET title = ?, description = ?, price = ?, media = ? WHERE id = ? AND user_id = ?";
            if ($stmt = mysqli_prepare($con, $update_query)) {
                mysqli_stmt_bind_param($stmt, "ssssii", $title, $description, $price, $media_paths_json, $home_id, $user_id);
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
        echo "Veuillez saisir un titre, une description, et un prix valides.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Home or Villa</title>
    <link rel="stylesheet" href="styles/edit_home.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        h2 {
            text-align: center;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        input[type="file"] {
            margin-top: 5px;
        }
        button[type="submit"],
        button[name="delete"] {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button[type="submit"] {
            background-color: #007bff;
            color: white;
        }
        button[name="delete"] {
            background-color: #dc3545;
            color: white;
        }
        button[type="submit"]:hover,
        button[name="delete"]:hover {
            filter: brightness(0.8);
        }
        .button-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        p {
            text-align: center;
            margin-top: 10px;
        }
        img {
            max-width: 100px;
            margin-bottom: 5px;
        }
        .risk{
            margin: 2;
            display:flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <?php include 'header.php'?>
    <h2>Edit Home or Villa</h2>
    
    <form action="edit_home.php?home_id=<?php echo htmlspecialchars($home_id); ?>" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" name="title" value="<?php echo isset($home['title']) ? htmlspecialchars($home['title']) : ''; ?>" required><br> 
        
        <label for="description">Description:</label>
        <textarea name="description" required><?php echo isset($home['description']) ? htmlspecialchars($home['description']) : ''; ?></textarea><br>
        
        <label for="price">Price:</label>
        <input type="text" name="price" value="<?php echo isset($home['price']) ? htmlspecialchars($home['price']) : ''; ?>" required><br>

        <label for="media">Photos (up to 6):</label>
        <input type="file" name="media[]" multiple accept="image/*"><br>

        <?php if (!empty($home['media'])): ?>
            <label>Current Photos:</label><br>
            <?php $media_paths = json_decode($home['media'], true); ?>
            <?php foreach ($media_paths as $key => $path): ?>
                <input type="hidden" name="existing_media[]" value="<?php echo $path; ?>">
                <div class="image-container">
                    <img src="<?php echo htmlspecialchars($path); ?>" alt="Current Image" style="max-width: 100px;"><br>
                    <input type="checkbox" name="delete_image[]" value="<?php echo $key; ?>"> Delete
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="risk">
            <button type="submit">Update Home</button>
            <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this home?')">Delete Home</button>
        </div>
        <p><a href="index.php">Back to Home</a></p> 
    </form>
    
    <?php include 'footer.php'?>
</body>
</html>