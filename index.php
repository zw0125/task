<?php
session_start();
include 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'];
$sql = "SELECT username FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// BMI Calculation
$bmi = '';
$bmiCategory = '';
$color = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $height = (float) $_POST['height'];
    $weight = (float) $_POST['weight'];

    if ($height > 0 && $weight > 0) {
        $bmi = $weight / (($height / 100) * ($height / 100));
        $bmi = round($bmi, 2);

        if ($bmi < 18.5) {
            $bmiCategory = 'Underweight';
            $color = 'blue lighten-4';
        } elseif ($bmi < 24.9) {
            $bmiCategory = 'Normal weight';
            $color = 'green lighten-4';
        } elseif ($bmi < 29.9) {
            $bmiCategory = 'Overweight';
            $color = 'amber lighten-4';
        } else {
            $bmiCategory = 'Obesity';
            $color = 'red lighten-4';
        }

        // Store BMI record in database
        if ($bmi) {
            $stmt = $conn->prepare("INSERT INTO bmi_records (user_id, height, weight, bmi, category) VALUES (?, ?, ?, ?, ?)");
            if ($stmt === false) {
                die('prepare() failed: ' . htmlspecialchars($conn->error));
            }
            $stmt->bind_param("iddds", $user_id, $height, $weight, $bmi, $bmiCategory);
            if (!$stmt->execute()) {
                die('execute() failed: ' . htmlspecialchars($stmt->error));
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BMI Calculator</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        
        <div class="container">
            <h3 class="center-align">BMI Calculator</h3>
            <div class="row z-depth-1" style="padding: 20px; border-radius: 10px;">
                <form method="POST">
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input id="height" type="number" name="height" step="0.01" required>
                            <label for="height">Height (in cm)</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="weight" type="number" name="weight" step="0.01" required>
                            <label for="weight">Weight (in kg)</label>
                        </div>
                    </div>
                    <div class="row">
                        <button class="btn waves-effect waves-light col s12" type="submit">
                            Calculate BMI
                            <i class="material-icons right">calculate</i>
                        </button>
                    </div>
                </form>
            </div>

            <?php if ($bmi): ?>
                <div class="card-panel <?php echo $color; ?>">
                    <h5>Your BMI Results</h5>
                    <p><strong>Height:</strong> <?php echo htmlspecialchars($_POST['height']); ?> cm</p>
                    <p><strong>Weight:</strong> <?php echo htmlspecialchars($_POST['weight']); ?> kg</p>
                    <p><strong>BMI:</strong> <?php echo $bmi; ?></p>
                    <p><strong>Category:</strong> <?php echo $bmiCategory; ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="dashboard-links">
            <a href="profile.php" class="btn">Profile</a>
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
