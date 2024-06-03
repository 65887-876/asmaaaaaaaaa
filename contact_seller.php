<?php
session_start();

require_once("connection.php");

$user_id = $_GET['user_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Contact Seller</title>
    <link rel="stylesheet" type="text/css" href="styles/contact_seller.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="contact-container">
        <?php
        if ($user_id) {
            $query = "SELECT username, email, phone FROM users WHERE user_id = '$user_id'";
            $result = mysqli_query($con, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);

                echo "<div class='contact-card'>";
                echo "<h2>Contact Information</h2>";
                echo "<p><strong>Username:</strong> " . htmlspecialchars($user['username']) . "</p>";
                echo "<p><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</p>";
                echo "<p><strong>Phone:</strong> " . htmlspecialchars($user['phone']) . "</p>";
                echo "</div>";
            } else {
                echo "<div class='contact-card'><p>User not found.</p></div>";
            }
        } else {
            echo "<div class='contact-card'><p>Invalid user ID.</p></div>";
        }
        ?>
    </div>

    <?php require_once("footer.php"); ?>
</body>
</html>
