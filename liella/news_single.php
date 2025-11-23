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
    <title>Liella! New Single Release Announcement</title>
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

                        <form class="search-bar" method="get">
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

    <h1 class="news-article-title">Liella! New Single Release Announcement</h1>

    <article class="news-article-content">
        <p>
            Liella! has officially announced their newest single — a song that blends soft emotional warmth with a radiant, uplifting sound that only they can deliver. The track was crafted with the intention of reaching listeners who need a gentle hand on the shoulder, a small spark of confidence, or a reminder that tomorrow still holds something bright.

In the production studio, the members worked closely with composers, suggesting lyrical adjustments and vocal textures that reflect their growth. Kanon’s voice carries the emotional core, layered with harmonies from Keke and Ren that bring both strength and softness. Sumire and Chisato add a lively edge, making the chorus feel expansive and full of color.

During recording, Kanon commented that the song feels like “a letter to your future self — the version of you who finally made it through.” Ren described it as “a warm light in the quiet moments,” while Keke excitedly claimed that fans would “absolutely cry, but in a good way!”

The single will be released with a beautifully styled cover, teaser behind-the-scenes clips, and a performance video set to drop shortly after. As anticipation builds, Liella! hopes the song will become something listeners return to during both their brightest and their loneliest days.
        </p>
    </article>

    <div class="news-article-image">
        <img src="assets/liella_news.jpg">
    </div>

</main>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>
