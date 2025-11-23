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
    <title>Sumire’s Shrine Maiden Journey</title>
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

    <h1 class="news-article-title">Sumire’s Shrine Maiden Journey</h1>

    <article class="news-article-content">
        <p>
            To everyone’s surprise, Sumire accepted an invitation to spend a day helping at a local shrine known for its seasonal festivals. The staff wanted help promoting an upcoming event, and Sumire—being Sumire—saw it as a glamorous opportunity. But the experience turned out to be far more meaningful than she expected.

Dressed in traditional shrine maiden attire, she began her tasks with a carefully practiced smile. At first, she imagined it would simply be greeting visitors and posing for promotional photos. Instead, Sumire found herself helping sweep fallen leaves, carry festival decorations, and even assist elderly visitors up the stone steps. The other shrine workers praised her enthusiasm, which only made her straighten her posture and try even harder.

Children who visited the shrine quickly gathered around her. They tugged at her sleeves, asked about Liella!, and excitedly requested she perform a short dance. Sumire—unable to resist the attention—gave them a small, adorable routine that left them cheering.

As the sun set, she lit lanterns along the shrine path. The warm glow reflected in her eyes as she paused to admire the quiet beauty of the moment. Though the day was physically tiring, something about the experience left her feeling refreshed. “Maybe… maybe this kind of stage isn’t so bad after all,” she murmured to herself.

Her shrine maiden journey, filled with humility, charm, and unexpected heart, became one of her most cherished personal memories.
        </p>
    </article>

    <div class="news-article-image">
        <img src="assets/sumire_news.jpeg">
    </div>

</main>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>
