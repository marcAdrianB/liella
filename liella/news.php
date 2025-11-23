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
    <title>Liella! News</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="news-page">

<!-- TRANSITION FRAME -->
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

<main class="news-main">
    <h1 class="news-page-title reveal">Liella! News</h1>

    <div class="news-list">

        <!-- NEWS ITEM TEMPLATE -->
        <a href="news_kanon.php" class="news-item reveal">
            <div class="news-image-box">
                <img src="assets/kanon_news.jpeg" class="news-img">
                <div class="news-img-frame">
                    <span>VIEW</span>
                    <i class="ri-arrow-right-line animated-arrow"></i>
                </div>
            </div>

            <div class="news-text">
                <h2>Kanon's Shibuya Adventures</h2>
                <p>A short look into Kanon’s day wandering around her favorite places in Shibuya.</p>
            </div>
        </a>

        <a href="news_bts.php" class="news-item reveal">
            <div class="news-image-box">
                <img src="assets/liella4.jpg" class="news-img">
                <div class="news-img-frame">
                    <span>VIEW</span>
                    <i class="ri-arrow-right-line animated-arrow"></i>
                </div>
            </div>

            <div class="news-text">
                <h2>Liella! Behind the Stage</h2>
                <p>A glimpse into rehearsals, preparation, and the energy before the curtains rise.</p>
            </div>
        </a>

        <a href="news_single.php" class="news-item reveal">
            <div class="news-image-box">
                <img src="assets/liella_news.jpg" class="news-img">
                <div class="news-img-frame">
                    <span>VIEW</span>
                    <i class="ri-arrow-right-line animated-arrow"></i>
                </div>
            </div>

            <div class="news-text">
                <h2>Liella! New Single Release Announcement</h2>
                <p>A fresh new single is coming soon—full of sparkle and sound!</p>
            </div>
        </a>

        <a href="news_sumire.php" class="news-item reveal">
            <div class="news-image-box">
                <img src="assets/sumire_news.jpeg" class="news-img">
                <div class="news-img-frame">
                    <span>VIEW</span>
                    <i class="ri-arrow-right-line animated-arrow"></i>
                </div>
            </div>

            <div class="news-text">
                <h2>Sumire's Shrine Maiden Journey</h2>
                <p>Sumire takes on a traditional challenge—with her own sparkly twist.</p>
            </div>
        </a>

    </div>
</main>

<!-- FOOTER -->
<?php include 'footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>
