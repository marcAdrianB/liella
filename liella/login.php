
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

$error = '';

// Handle login submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = "Please enter both username and password.";
    } else {
        // Adjust table/column names if your users table is different
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user) {
                // If you are using password_hash in DB, replace comparison with password_verify:
                // if (password_verify($password, $user['password'])) { ... }
                if ($password === $user['password']) {
                    // Successful login
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];

                    // Redirect to homepage
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Incorrect username or password.";
                }
            } else {
                $error = "Incorrect username or password.";
            }
        } else {
            $error = "Database error. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Liella! Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts + Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page login-page">

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
            <h1>Login</h1>
            <p>Welcome back to Liella! Live Events ✨</p>
        </div>

        <?php if ($error): ?>
            <p class="auth-error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="post" class="auth-form" autocomplete="off">
            <div class="auth-field">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter your username"
                    required
                >
            </div>

            <div class="auth-field auth-password-field">
                <label for="password">Password</label>
                <div class="auth-password-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >
                    <button type="button" class="auth-eye-btn" aria-label="Show password">
                        <i class="ri-eye-off-line"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="auth-submit-btn">Login</button>
        </form>

        <p class="auth-switch">
            Don’t have an account?
            <a href="signup.php">Sign up here</a>
        </p>
        <p class="auth-back-home">
    <a href="index.php">← Return to Home</a>
        </p>
         
    </div>
   

</main>


<!-- No footer on auth pages -->

<script src="js/script.js"></script>

<!-- Small inline JS for password reveal -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const eyeBtn = document.querySelector('.auth-eye-btn');
    const pwdInput = document.getElementById('password');

    if (eyeBtn && pwdInput) {
        eyeBtn.addEventListener('click', () => {
            const isHidden = pwdInput.type === 'password';
            pwdInput.type = isHidden ? 'text' : 'password';
            eyeBtn.innerHTML = isHidden
                ? '<i class="ri-eye-line"></i>'
                : '<i class="ri-eye-off-line"></i>';
        });
    }
});
</script>

</body>
</html>
