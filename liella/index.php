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

// Fetch upcoming events (you can tweak the query)
$events = [];
$sql = "SELECT id, event_name, event_date, venue 
        FROM events 
        WHERE event_date >= CURDATE()
        ORDER BY event_date ASC
        LIMIT 4";

if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    $result->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Liella! Live Events</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="assets/liella.ico">
    <!-- Google Font example -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <!-- Icon font (for arrows / social icons if you want) -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">


    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- GLOBAL PAGE TRANSITION -->
<div id="transition-frame">
    <div class="panel"></div>
    <img src="assets/Liella!_Official_Logo_White.png" alt="logo">
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
                <a href="index.php" class="nav-link active"><span>HOME</span><span class="nav-heart"></span></a>
                <a href="news.php" class="nav-link"><span>NEWS</span><span class="nav-heart"></span></a>
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


<main>

    <!-- HERO / SLIDESHOW SECTION -->
    <section class="hero-section" id="topSection">
        <div class="hero-slideshow">
            <div class="hero-slide active" style="background-image: url('assets/liella5.jpg');"></div>
            
        </div>

        <div class="hero-overlay">
            <div class="hero-text reveal">
                <h1>Liella! Live Events</h1>
                <p>Feel the music, the lights, and the love of Liella! wherever you are.</p>
            </div>
        </div>
        <a href="#mediaEventsSection" class="scroll-events-btn">
    <span class="scroll-text">Upcoming Events!</span>
    <i class="ri-arrow-down-double-line scroll-arrow"></i>
</a>

    </section>

    <!-- NEWS SNEAK PEEKS -->
    <section class="news-section" id="newsSection">
        <div class="section-header reveal">
            <h2>What's Up Liella?</h2>
            <p>Catch a glimpse of what’s happening with Liella!</p>
        </div>

       <div class="news-grid">

    <!-- Kanon Article -->
    <a href="news_kanon.php" class="news-card reveal">
        <div class="news-image">
            <img src="assets/kanon_news.jpeg" alt="Kanon News">
            <div class="news-frame">
                <div class="news-view">
                    <span>View</span>
                    <i class="ri-arrow-right-line animated-arrow"></i>
                </div>
            </div>
        </div>
        <div class="news-meta">
            <h3>Kanon's Shibuya Adventures</h3>
        </div>
    </a>

    <!-- Behind the Stage -->
    <a href="news_bts.php" class="news-card reveal">
        <div class="news-image">
            <img src="assets/liella4.jpg" alt="Behind the Stage">
            <div class="news-frame">
                <div class="news-view">
                    <span>View</span>
                    <i class="ri-arrow-right-line animated-arrow"></i>
                </div>
            </div>
        </div>
        <div class="news-meta">
            <h3>Liella! Behind the Stage</h3>
        </div>
    </a>

    <!-- New Single Release -->
    <a href="news_single.php" class="news-card reveal">
        <div class="news-image">
            <img src="assets/liella_news.jpg" alt="New Single Teaser">
            <div class="news-frame">
                <div class="news-view">
                    <span>View</span>
                    <i class="ri-arrow-right-line animated-arrow"></i>
                </div>
            </div>
        </div>
        <div class="news-meta">
            <h3>Liella! New Single Release Announcement</h3>
        </div>
    </a>

   
</div>

        <div class="news-view-all-wrapper reveal">
    <a href="news.php" class="news-view-all-btn">
        <span>View All</span>
        <i class="ri-arrow-right-line viewall-arrow"></i>
    </a>
</div>

    </section>


    <!-- VIDEO + UPCOMING EVENTS -->
    <section class="media-events-section" id="mediaEventsSection">
        
        <div class="media-column reveal">
            <h2>Watch Liella!</h2>
            <div class="video-wrapper">
                <!-- Replace with your chosen Liella YouTube URL -->
                <iframe
                    src="https://www.youtube.com/embed/_oZ3EcOO5xU?autoplay=1&mute=1&loop=1&playlist=_oZ3EcOO5xU"
                    title="Liella Video"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>

            </div>
        </div>

        <div class="events-column reveal">
            <h2>Liella's Upcoming Events</h2>
            <ul class="events-list" >
                <?php if (empty($events)): ?>
                    <li class="event-item no-events">
                        <span>No upcoming events yet. Please check back soon!</span>
                    </li>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                        <?php
                            $date = new DateTime($event['event_date']);
                            $month = strtoupper($date->format('M'));
                            $day = $date->format('d');
                        ?>
                        <li class="event-item event-home" data-event-id="<?= $event['id'] ?>">

                            <div class="event-date">
                                <span class="event-month"><?= $month ?></span>
                                <span class="event-day"><?= $day ?></span>
                            </div>
                            <div class="event-details">
                                <span class="event-name"><?= htmlspecialchars($event['event_name']) ?></span>
                                <?php if (!empty($event['venue'])): ?>
                                    <span class="event-venue"><?= htmlspecialchars($event['venue']) ?></span>
                                <?php endif; ?>
                                <span class="event-view-details">
                                    View Details
                                    <i class="ri-arrow-right-line animated-arrow"></i>
                                </span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    <div class="view-all-wrapper">
                        <a href="events.php" class="view-all-btn">
                            <span>View All Events</span>
                            <i class="ri-arrow-right-double-line view-all-arrow"></i>
                        </a>
                    </div>

                <?php endif; ?>
            </ul>
        </div>
    </section>

</main>

<!-- FOOTER -->
<footer class="site-footer">
    <div class="footer-inner reveal">
        <p class="footer-title">Follow Liella!</p>

        <div class="social-buttons">

            <a href="https://x.com/LoveLive_staff" target="_blank" class="social-btn x-btn">
                <div class="social-left">
                    <i class="ri-twitter-x-line"></i>
                    <span class="social-name">X (Twitter)</span>
                </div>
                <span class="social-arrow">
                    <i class="ri-arrow-right-s-line single-arrow"></i>
                    <i class="ri-arrow-right-s-line double-arrow"></i>
                </span>
            </a>

            <a href="https://www.youtube.com/channel/UCTkyJbRhal4voLZxmdRSssQ" target="_blank" class="social-btn yt-btn">
                <div class="social-left">
                    <i class="ri-youtube-fill"></i>
                    <span class="social-name">YouTube</span>
                </div>
                <span class="social-arrow">
                    <i class="ri-arrow-right-s-line single-arrow"></i>
                    <i class="ri-arrow-right-s-line double-arrow"></i>
                </span>
            </a>

            <a href="https://web.facebook.com/LoveLiveStaff/?_rdc=1&_rdr#" target="_blank" class="social-btn fb-btn">
                <div class="social-left">
                    <i class="ri-facebook-circle-fill"></i>
                    <span class="social-name">Facebook</span>
                </div>
                <span class="social-arrow">
                    <i class="ri-arrow-right-s-line single-arrow"></i>
                    <i class="ri-arrow-right-s-line double-arrow"></i>
                </span>
            </a>

            <a href="https://www.instagram.com/lovelive_superstar_staff/" target="_blank" class="social-btn ig-btn">
                <div class="social-left">
                    <i class="ri-instagram-line"></i>
                    <span class="social-name">Instagram</span>
                </div>
                <span class="social-arrow">
                    <i class="ri-arrow-right-s-line single-arrow"></i>
                    <i class="ri-arrow-right-s-line double-arrow"></i>
                </span>
            </a>

        </div>


        <p class="footer-copy">© <?php echo date('Y'); ?> Liella! Live Events. All rights reserved.</p>
    </div>
</footer>

<script src="js/script.js"></script>
</body>
</html>

