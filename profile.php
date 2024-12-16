<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email, created_at, profile_picture FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
$profile_picture = $user['profile_picture'] ?? 'assets/default.png';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $target_dir = "assets/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    $uploadOk = 1;

    // Check file type
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if (in_array($imageFileType, ['jpg', 'png', 'jpeg'])) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $sql = "UPDATE users SET profile_picture = '$target_file' WHERE id = $user_id";
            if ($conn->query($sql) === TRUE) {
                $profile_picture = $target_file;
                $success_message = "Profile picture updated successfully!";
            } else {
                $error_message = "Error updating profile picture: " . $conn->error;
            }
        }
    } else {
        $error_message = "Only JPG, PNG, and JPEG files are allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="profile-container">
        <h1>Your Profile</h1>
        
        <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" class="profile-pic">

        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="profile-form">
            <label for="profile_picture">Change Profile Picture:</label>
            <input type="file" name="profile_picture" id="profile_picture" required>
            <button type="submit">Upload</button>
        </form>

        <div class="profile-details">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Member since:</strong> <?php echo $user['created_at']; ?></p>
        </div>

        <!-- BMI History Section -->
        <div class="bmi-history">
            <h3>BMI History</h3>
            <?php
            $history_sql = "SELECT * FROM bmi_records WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
            $stmt = $conn->prepare($history_sql);
            if ($stmt === false) {
                die('prepare() failed: ' . htmlspecialchars($conn->error));
            }
            $stmt->bind_param("i", $user_id);
            if (!$stmt->execute()) {
                die('execute() failed: ' . htmlspecialchars($stmt->error));
            }
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo '<table class="striped">';
                echo '<thead><tr>';
                echo '<th>Date</th>';
                echo '<th>Height (cm)</th>';
                echo '<th>Weight (kg)</th>';
                echo '<th>BMI</th>';
                echo '<th>Category</th>';
                echo '</tr></thead><tbody>';
                
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . date('Y-m-d H:i', strtotime($row['created_at'])) . '</td>';
                    echo '<td>' . htmlspecialchars($row['height']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['weight']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['bmi']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['category']) . '</td>';
                    echo '</tr>';
                }
                
                echo '</tbody></table>';
            } else {
                echo '<p>No BMI records found.</p>';
            }
            $stmt->close();
            ?>
        </div>

        <a href="index.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>
