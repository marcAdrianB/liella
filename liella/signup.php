<?php
session_start();
require_once 'db.php';

// Fetch current user data if logged in
$currentUsername = 'Guest';
$profileImage = 'assets/default-profile.png';

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username, profile_image FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $stmt->bind_result($uname, $pimg);
        if ($stmt->fetch()) {
            $currentUsername = $uname ?: 'Guest';
            $profileImage = $pimg ?: 'assets/default-profile.png';
        }
        $stmt->close();
    }
}
?>


<?php


$errors = [];
$success = false;

$username = '';
$password = '';
$confirm  = '';

// Handle signup
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm_password'] ?? '');

    // Basic validation (same rules as front-end)
    if ($username === '' || !preg_match('/^[A-Za-z0-9_]+$/', $username)) {
        $errors[] = "Username must contain only letters, numbers, or underscore (no spaces).";
    }

    if ($password === '' || !preg_match('/^[A-Za-z0-9]{8,}$/', $password)) {
        $errors[] = "Password must be at least 8 characters, letters and numbers only.";
    }

    if ($confirm !== $password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $errors[] = "User already exists.";
            }
            $stmt->close();
        } else {
            $errors[] = "Database error. Please try again.";
        }
    }

    if (empty($errors)) {
        // If you want to hash passwords, use:
        // $hashed = password_hash($password, PASSWORD_DEFAULT);
        // and store $hashed instead.
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("ss", $username, $password);
            if ($stmt->execute()) {
                $newId = $stmt->insert_id;
                $success = true;

                // Automatically log in the user
                $_SESSION['user_id'] = $newId;
                $_SESSION['username'] = $username;
            } else {
                $errors[] = "Failed to create user. Please try again.";
            }
            $stmt->close();
        } else {
            $errors[] = "Database error. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Liella! Signup</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts + Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page signup-page">

<!-- GLOBAL PAGE TRANSITION -->
<div id="transition-frame">
    <div class="panel"></div>
    <img src="assets/Liella!_Official_Logo_White.png" alt="logo">
</div>

<main class="auth-main">


    <div class="auth-card">
        <div class="auth-logo">
    <img src="assets/Liella!_Official_Logo.png" alt="Liella Logo">
</div>
        <div class="auth-header">
            <h1>Sign Up</h1>
            <p>Join Liella! and never miss a live event ✨</p>
        </div>

        <?php if (!empty($errors)): ?>
            <ul class="auth-error-list">
                <?php foreach ($errors as $e): ?>
                    <li><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="post" class="auth-form" id="signupForm" autocomplete="off">

    <!-- USERNAME -->
    <div class="auth-field field-with-status" id="userField">
        <label for="su_username">Username</label>

        <div class="auth-input-row">
            <input 
                type="text"
                id="su_username"
                name="username"
                placeholder="Enter your desired username"
                value="<?php echo htmlspecialchars($username); ?>"
                required
            >
            <span class="field-status-icon"><i class="ri-check-line"></i></span>
        </div>

        <small class="auth-hint">Letters, numbers, and underscore only.</small>
    </div>


    <!-- PASSWORD -->
    <div class="auth-field field-with-status" id="passField">
        <label for="su_password">Password</label>

        <div class="auth-input-row auth-password-wrapper">
            <input
                type="password"
                id="su_password"
                name="password"
                placeholder="Enter your password"
                required
            >

            <button type="button" class="auth-eye-btn pass-eye">
                <i class="ri-eye-off-line"></i>
            </button>

            <span class="field-status-icon"><i class="ri-check-line"></i></span>
        </div>

        <small class="auth-hint">At least 8 characters, letters and numbers only.</small>
    </div>


    <!-- CONFIRM PASSWORD -->
    <div class="auth-field field-with-status" id="confirmField">
        <label for="su_confirm">Confirm Password</label>

        <div class="auth-input-row auth-password-wrapper">
            <input
                type="password"
                id="su_confirm"
                name="confirm_password"
                placeholder="Confirm your password"
                required
            >

            <button type="button" class="auth-eye-btn confirm-eye">
                <i class="ri-eye-off-line"></i>
            </button>

            <span class="field-status-icon"><i class="ri-check-line"></i></span>
        </div>
    </div>


    <button type="submit" class="auth-submit-btn" id="signupSubmit" disabled>
        Sign Up
    </button>
</form>

        <p class="auth-switch">
            Already have an account?
            <a href="login.php">Login here</a>
        </p>
        <p class="auth-back-home">
            <a href="index.php">← Return to Home</a>
        </p>

    </div>
    

    <!-- Success dialog overlay -->
    <?php if ($success): ?>
        <div class="auth-success-overlay">
            <div class="auth-success-dialog">
                <h2>Successfully signed up!</h2>
                <p>Welcome to Liella! Live Events. You’re now logged in.</p>
                <button type="button" id="goHomeBtn" class="auth-submit-btn">Go to Home Page</button>
            </div>
        </div>
    <?php endif; ?>
</main>

<script src="js/script.js"></script>

<!-- Inline JS for validation + checkmarks + password reveal + success redirect -->
<script>
document.addEventListener('DOMContentLoaded', () => {

    const userInput = document.getElementById('su_username');
    const passInput = document.getElementById('su_password');
    const confirmInput = document.getElementById('su_confirm');

    const userField = document.getElementById('userField');
    const passField = document.getElementById('passField');
    const confirmField = document.getElementById('confirmField');

    const submitBtn = document.getElementById('signupSubmit');

    const passEye = document.querySelector('.pass-eye');
    const confirmEye = document.querySelector('.confirm-eye');

    function validateUsername(v) {
        return /^[A-Za-z0-9_]+$/.test(v) && v.length >= 3;
    }

    function validatePassword(v) {
        return /^[A-Za-z0-9]{8,}$/.test(v);
    }

    function updateStatus() {
        const uOK = validateUsername(userInput.value.trim());
        const pOK = validatePassword(passInput.value.trim());
        const cOK = (confirmInput.value.trim() === passInput.value.trim() && pOK);

        userField.classList.toggle("field-valid", uOK);
        passField.classList.toggle("field-valid", pOK);
        confirmField.classList.toggle("field-valid", cOK);

        submitBtn.disabled = !(uOK && pOK && cOK);
    }

    userInput.addEventListener("input", updateStatus);
    passInput.addEventListener("input", updateStatus);
    confirmInput.addEventListener("input", updateStatus);


    // ==== Eye Toggle for Password ====
    passEye.addEventListener("click", () => {
        const hidden = passInput.type === "password";
        passInput.type = hidden ? "text" : "password";
        passEye.innerHTML = hidden
            ? '<i class="ri-eye-line"></i>'
            : '<i class="ri-eye-off-line"></i>';
    });

    // ==== Eye Toggle for Confirm Password ====
    confirmEye.addEventListener("click", () => {
        const hidden = confirmInput.type === "password";
        confirmInput.type = hidden ? "text" : "password";
        confirmEye.innerHTML = hidden
            ? '<i class="ri-eye-line"></i>'
            : '<i class="ri-eye-off-line"></i>';
    });


    // ==== Success dialog Go Home ====
    const goHomeBtn = document.getElementById("goHomeBtn");
    if (goHomeBtn) {
        goHomeBtn.addEventListener("click", () => {

            // play transition
            sessionStorage.setItem("liella-transition", "1");
            const frame = document.getElementById("transition-frame");
            frame.classList.add("active-exit");

            setTimeout(() => {
                window.location.href = "index.php";
            }, 900);
        });
    }

    updateStatus();
});
</script>

</body>
</html>
