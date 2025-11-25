
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


// Simple login flag (adjust when you have real login)
$isLoggedIn = isset($_SESSION['user_id']);

// Fetch upcoming events with description
$events = [];
$sql = "SELECT id, event_name, event_date, venue, description
        FROM events
        WHERE event_date >= CURDATE()
        ORDER BY event_date ASC";

if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    $result->free();
}

// Group events by Month + Year
$grouped = [];
foreach ($events as $event) {
    $date = new DateTime($event['event_date']);
    $key  = $date->format('F Y'); // e.g. "November 2025"
    if (!isset($grouped[$key])) {
        $grouped[$key] = [];
    }
    $grouped[$key][] = $event;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Liella! Upcoming Events</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="assets/liella.ico">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="events-page">

<!-- GLOBAL PAGE TRANSITION -->
<div id="transition-frame">
    <div class="panel"></div>
    <img src="assets/Liella!_Official_Logo_White.png" alt="logo">
</div>


<!-- NAVBAR (same as index.php) -->
<header class="navbar navbar-expanded" id="mainNavbar">
    <div class="nav-inner">
        <div class="nav-left">
            <a href="index.php" class="nav-logo">
                <img src="assets/Liella!_Official_Logo.png" alt="Liella Logo" id="navLogo">
            </a>
        </div>

        <div class="nav-right">
            <nav class="nav-links">
                <a href="index.php" class="nav-link"><span>HOME</span><span class="nav-heart"></span></a>
                <a href="news.php" class="nav-link"><span>NEWS</span><span class="nav-heart"></span></a>
                <a href="events.php" class="nav-link active"><span>EVENTS</span><span class="nav-heart"></span></a>
                <a href="members.php" class="nav-link"><span>MEMBERS</span><span class="nav-heart"></span></a>
            </nav>

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

<main class="events-main">
    <section class="events-section">
        <div class="events-header-block reveal">
            <h1>Liella's Upcoming Events</h1>
            <p>See all concerts, fan meetings, and special stages in one place.</p>
        </div>

        <?php if (empty($grouped)): ?>
            <p class="no-events-text">No upcoming events yet. Please check back soon!</p>
        <?php else: ?>
            <?php foreach ($grouped as $monthYear => $evts): ?>
                <div class="events-month-block reveal">
                    <div class="events-month-header">
                        <h2><?php echo htmlspecialchars(strtoupper($monthYear)); ?></h2>
                        <div class="events-divider"></div>
                    </div>

                    <ul class="events-list events-list-full">
                        <?php foreach ($evts as $event): ?>
                            <?php
                                $dateObj = new DateTime($event['event_date']);
                                $monthShort = strtoupper($dateObj->format('M'));
                                $day = $dateObj->format('d');
                            ?>
                            <li class="event-item event-item-full"
                                data-event-id="<?php echo (int)$event['id']; ?>"
                                data-event-name="<?php echo htmlspecialchars($event['event_name'], ENT_QUOTES); ?>"
                                data-event-date="<?php echo $dateObj->format('F j, Y'); ?>"
                                data-event-venue="<?php echo htmlspecialchars($event['venue'] ?? '', ENT_QUOTES); ?>"
                                data-event-description="<?php echo htmlspecialchars($event['description'] ?? '', ENT_QUOTES); ?>">
                                <div class="event-date">
                                    <span class="event-month"><?php echo $monthShort; ?></span>
                                    <span class="event-day"><?php echo $day; ?></span>
                                </div>
                                <div class="event-details">
                                    <span class="event-name event-name-full">
                                        <?php echo htmlspecialchars($event['event_name']); ?>
                                    </span>
                                    <?php if (!empty($event['venue'])): ?>
                                        <span class="event-venue event-venue-full">
                                            <?php echo htmlspecialchars($event['venue']); ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="event-view-details">
                                        Register
                                        <i class="ri-arrow-right-line animated-arrow"></i>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<!-- MODAL OVERLAY FOR REGISTRATION -->
<div class="event-modal-overlay" id="eventModal">
    <div class="event-modal-backdrop"></div>
    <div class="event-modal">
        <button class="event-modal-close" type="button">&times;</button>
        <h2 id="modalEventName"></h2>
        <p class="modal-event-meta" id="modalEventDate"></p>
        <p class="modal-event-meta" id="modalEventVenue"></p>
        <p class="modal-event-description" id="modalEventDescription"></p>

        <?php if ($isLoggedIn): ?>
            <form id="eventRegisterForm" class="event-register-form">
                <input type="hidden" name="event_id" id="modalEventId">
                
                <div class="form-row">
                    <label for="modalEmail">Email Address</label>
                    <input type="email" name="email" id="modalEmail" required>
                </div>
                <button type="submit" class="event-register-btn">Register</button>
            </form>
            <div class="event-modal-message" id="modalMessage"></div>
            <div class="event-modal-success" id="modalSuccess">
                <p>Successfully registered to the event!</p>
                <a href="profile.php" class="view-my-events-btn">View My Events</a>
            </div>
        <?php else: ?>
            <div class="modal-login-required">
                <p>You need to log in first to register for this event.</p>
                <a href="login.php" class="login-redirect-btn">Go to Login</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- FOOTER (same as index) -->
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

<script>
    // Pass login flag to JS
    const IS_LOGGED_IN = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
</script>
<script src="js/script.js"></script>
</body>
</html>

