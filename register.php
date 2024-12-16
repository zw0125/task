<?php
include 'db_config.php';
include 'header.php';

$registrationSuccess = false; // Variable to check registration status

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if the username or email already exists
    $check_sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        $error = "Username or Email already exists. Please choose a different one.";
    } else {
        // Insert new user if no duplicates
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            $registrationSuccess = true; // Set success flag to true
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<div class="form-container">
    <h1>Register</h1>
    <?php if (!empty($error)) : ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post" id="registrationForm">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" minlength="8" required>
        <p class="hint">* Password must be at least 8 characters long.</p>
        
        <label for="confirmPassword">Confirm Password:</label>
        <div class="password-match-container">
            <input type="password" name="confirmPassword" id="confirmPassword" required>
            <span id="matchIcon" class="password-icon"></span>
        </div>
        
        <button type="submit" id="registerBtn" disabled>Register</button>
    </form>
</div>

<!-- Pop-up Modal -->
<?php if ($registrationSuccess): ?>
<div id="successModal" class="modal">
    <div class="modal-content">
        <h2>Registration Successful!</h2>
        <p>You have successfully registered. You can now log in to your account.</p>
        <button onclick="redirectToLogin()">Login Now</button>
    </div>
</div>
<script>
    document.getElementById('successModal').style.display = 'block';
    function redirectToLogin() {
        window.location.href = 'login.php';
    }
</script>
<?php endif; ?>

<script>
// JavaScript for password validation
document.addEventListener('DOMContentLoaded', () => {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    const matchIcon = document.getElementById('matchIcon');
    const registerBtn = document.getElementById('registerBtn');

    function validatePasswords() {
        if (password.value.length >= 8 && confirmPassword.value.length >= 8) {
            if (password.value === confirmPassword.value) {
                matchIcon.textContent = '✔'; // Show tick icon
                matchIcon.style.color = 'green';
                registerBtn.disabled = false; // Enable submit button
            } else {
                matchIcon.textContent = '✖'; // Show cross icon
                matchIcon.style.color = 'red';
                registerBtn.disabled = true; // Disable submit button
            }
        } else {
            matchIcon.textContent = ''; // Clear icon if requirements not met
            registerBtn.disabled = true;
        }
    }

    password.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);
});
</script>
