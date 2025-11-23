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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kanon's Shibuya Adventures</title>
    <link rel="stylesheet" href="css/style.css">
     <!-- Google Font example -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="news-article-page">

<div id="transition-frame">
    <div class="panel"></div>
    <img src="assets/Liella!_Official_Logo_White.png" />
</div>

<!-- NAVBAR -->
<header class="navbar navbar-expanded" id="mainNavbar">
    <div class="nav-inner">

        <!-- LEFT SIDE: LOGO -->
        <div class="nav-left">
            <a href="index.php" class="nav-logo">
                <img src="assets/Liella!_Official_Logo.png" alt="Liella Logo" id="navLogo">
            </a>
        </div>

        <!-- RIGHT SIDE: BUTTONS + SEARCH + PROFILE -->
        <div class="nav-right">

            <nav class="nav-links">
                <a href="index.php" class="nav-link"><span>HOME</span><span class="nav-heart"></span></a>
                <a href="news.php" class="nav-link active"><span>NEWS</span><span class="nav-heart"></span></a>
                <a href="events.php" class="nav-link"><span>EVENTS</span><span class="nav-heart"></span></a>
                <a href="members.php" class="nav-link"><span>MEMBERS</span><span class="nav-heart"></span></a>
            </nav>

                    <!-- SEARCH WRAPPER -->
                    <div class="nav-search-inline">
                        <button class="search-trigger">
                            <i class="ri-search-line"></i>
                        </button>

                        <form class="search-bar"  method="get">
                            <input type="text" name="q" placeholder="Search..." autocomplete="off" />
                            <button type="submit" class="search-submit">
                                <i class="ri-search-line"></i>
                            </button>
                        </form>
                    </div>

                    




           <a href="<?php echo isset($_SESSION['user_id']) ? 'profile.php' : 'login.php'; ?>"
            class="nav-profile"
            data-username="<?php echo htmlspecialchars($currentUsername); ?>">
                <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile">
                <span class="nav-profile-tooltip">
                    <?php echo htmlspecialchars($currentUsername); ?>
                </span>
            </a>


        </div>

    </div>
</header>
<main class="news-article-main">

    <a href="news.php" class="news-back-btn">
        <i class="ri-arrow-left-line"></i> News
    </a>

    <h1 class="news-article-title">Kanon's Shibuya Adventures</h1>

    <article class="news-article-content">
        <p>
            Kanon spent a rare free afternoon wandering through Shibuya, a place that still feels like the starting point of her musical dreams. The city streets were as vibrant as ever—alive with chatter, dancing billboards, and the scent of freshly baked pastries drifting from café doors. She quietly blended into the flow of people, soaking in the atmosphere that once pushed her forward when she felt lost.

Her first stop was the little café she used to visit after practice. Even now, the warm lighting and familiar aroma reminded her of the evenings she’d sit alone, humming melodies into her phone. As she sipped her drink, she took a moment to breathe, reflect, and remember the version of herself who wished for nothing more than to sing confidently.

Afterward, she visited a small music shop tucked between larger stores. Kanon spent nearly an hour flipping through CDs, listening to sample tracks, and gently tapping her foot to rhythms that inspired her. The clerk recognized her and asked for a quick photo, which she shyly accepted with her usual warm smile.

As the sun dipped lower, Kanon walked toward her favorite overlook—a quiet spot where the train rails cut across the skyline. She stood there for a moment, watching the evening lights shimmer across the district. It wasn’t a grand event or a special occasion, yet this simple adventure reminded her why she sings: because everyday moments can shine just as brightly as a stage.
        </p>
    </article>

    <div class="news-article-image">
        <img src="assets/kanon_news.jpeg">
    </div>

</main>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>
