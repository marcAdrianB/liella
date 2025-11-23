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
    <title>Liella! Behind the Stage</title>
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

    <h1 class="news-article-title">Liella! Behind the Stage</h1>

    <article class="news-article-content">
        <p>
            Long before the spotlight hits and the cheers rise, the members of Liella! are already hard at work. The rehearsal hall is filled with the sound of footsteps, claps, and the soft repetition of harmonies as they polish every part of their performance. Behind the scenes, their energy is different—more focused, more intimate, but still filled with their signature sparkle.

Keke is usually the first one to arrive, stretching and practicing lines from songs she memorized the night before. Sumire follows soon after, adjusting her hair while humming confidently despite pretending she’s “not even warmed up yet.” Ren sets up her practice area with meticulous care, while Chisato checks everyone’s posture and offers pointers with a bright smile.

Meanwhile, Kanon reviews lyrics under her breath, sometimes stopping to help a member work through a difficult harmony. Their individual routines blend naturally, forming the soft rhythm of teamwork that lies beneath their polished performances.

By the time the final rehearsal run begins, the room transforms. Their steps synchronize, their voices merge, and the energy shifts from casual chatting to something powerful and unified. Even without an audience, Liella! performs as if they’re filling an arena — because every practice, no matter how exhausting, is another step toward delivering the best version of themselves onstage.

For fans who only see the glittering final result, these backstage moments remain unseen. But they are the heart of Liella!: laughter, effort, encouragement, and quiet determination woven together long before the curtains rise.
        </p>
    </article>

    <div class="news-article-image">
        <img src="assets/liella4.jpg">
    </div>

</main>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>
