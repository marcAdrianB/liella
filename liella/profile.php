<?php
session_start();
require_once 'db.php';

// Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Back link (previous page) fallback
$backLink = $_SERVER['HTTP_REFERER'] ?? 'index.php';

// Available avatar choices (make sure these files exist)
$avatarChoices = [
    'assets/avatars/liella_kanon.png',
    'assets/avatars/liella_keke.png',
    'assets/avatars/liella_chisato.png',
    'assets/avatars/liella_sumire.png',
    'assets/avatars/liella_ren.png',
    'assets/default-profile.png'
];

// Get current user data
$username = 'Guest';
$currentAvatar = 'assets/default-profile.png';

$stmt = $conn->prepare("SELECT username, profile_image FROM users WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($u, $img);
    if ($stmt->fetch()) {
        $username = $u ?: 'Guest';
        $currentAvatar = $img ?: 'assets/default-profile.png';
    }
    $stmt->close();
}

// Handle avatar change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['avatar'])) {
    $selected = $_POST['avatar'];

    if (in_array($selected, $avatarChoices, true)) {
        $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $selected, $userId);
            $stmt->execute();
            $stmt->close();

            // Update session so navbar reflects immediately
            $_SESSION['profile_image'] = $selected;
            $currentAvatar = $selected;
        }
    }
}

// Load My Events (based on username)
$myEvents = [];
$regStmt = $conn->prepare("
    SELECT r.id, r.event_id, r.user_name, r.email,
           e.event_name, e.event_date, e.venue
    FROM event_registrations r
    JOIN events e ON r.event_id = e.id
    WHERE r.user_name = ?
    ORDER BY e.event_date ASC
");
if ($regStmt) {
    $regStmt->bind_param("s", $username);
    $regStmt->execute();
    $result = $regStmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $myEvents[] = $row;
    }
    $regStmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($username); ?> | Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts + Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">
</head>
<body class="profile-page">
    <!-- GLOBAL PAGE TRANSITION -->
<div id="transition-frame">
    <div class="panel"></div>
    <img src="assets/Liella!_Official_Logo_White.png" alt="logo">
</div>


    <main class="profile-main">
        <div class="profile-card">

            <!-- Back button -->
            <div class="profile-top-bar">
                <a href="index.php" class="profile-back-btn">
                    <i class="ri-arrow-left-line"></i>
                    <span>Back to Home</span>
                </a>

                <form action="logout.php" method="post">
                    <button type="submit" class="profile-logout-btn">
                        <i class="ri-logout-box-r-line"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>

            <!-- User header -->
            <section class="profile-header">
                <div class="profile-avatar-wrapper">
                    <img src="<?php echo htmlspecialchars($currentAvatar); ?>" alt="Profile picture" class="profile-avatar">
                </div>
                <div class="profile-user-info">
                    <h1><?php echo htmlspecialchars($username); ?></h1>
                    <p>Welcome back to Liella! Live Events 💫</p>
                    <form action="profile.php" method="post" class="profile-avatar-form">
                        <p class="profile-avatar-label">Edit profile picture</p>
                        <div class="profile-avatar-grid">
                            <?php foreach ($avatarChoices as $choice): ?>
                                <label class="avatar-option">
                                    <input
                                        type="radio"
                                        name="avatar"
                                        value="<?php echo htmlspecialchars($choice); ?>"
                                        <?php if ($choice === $currentAvatar) echo 'checked'; ?>
                                    >
                                    <span class="avatar-circle">
                                        <img src="<?php echo htmlspecialchars($choice); ?>" alt="Avatar">
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" class="profile-save-avatar-btn">
                            <i class="ri-save-3-line"></i>
                            <span>Save Changes</span>
                        </button>
                    </form>
                </div>
            </section>

            <!-- My Events -->
            <section class="profile-events-section">
                <div class="profile-events-header">
                    <h2>My Events</h2>
                    <p>Your registered Liella! lives and activities.</p>
                </div>

                <?php if (empty($myEvents)): ?>
                    <p class="profile-no-events">
                        You haven’t registered to any events yet.
                    </p>
                <?php else: ?>
                    <ul class="profile-events-list">
                        <?php foreach ($myEvents as $reg): ?>
                            <?php
                                $dateObj = new DateTime($reg['event_date']);
                                $monthShort = strtoupper($dateObj->format('M'));
                                $day = $dateObj->format('d');
                            ?>
                            <li class="profile-event-item" data-reg-id="<?php echo (int)$reg['id']; ?>">
                                <div class="profile-event-date">
                                    <span class="profile-event-month"><?php echo $monthShort; ?></span>
                                    <span class="profile-event-day"><?php echo $day; ?></span>
                                </div>
                                <div class="profile-event-details">
                                    <div class="profile-event-mainline">
                                        <span class="profile-event-name">
                                            <?php echo htmlspecialchars($reg['event_name']); ?>
                                        </span>
                                        <button
                                            type="button"
                                            class="profile-event-delete-btn"
                                            data-reg-id="<?php echo (int)$reg['id']; ?>"
                                            data-event-name="<?php echo htmlspecialchars($reg['event_name'], ENT_QUOTES); ?>"
                                        >
                                            <i class="ri-delete-bin-6-line"></i>
                                        </button>
                                    </div>
                                    <?php if (!empty($reg['venue'])): ?>
                                        <span class="profile-event-venue">
                                            <?php echo htmlspecialchars($reg['venue']); ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="profile-event-email">
                                        Registered email: <?php echo htmlspecialchars($reg['email']); ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <!-- Simple delete confirmation dialog -->
    <div class="profile-confirm-overlay" id="deleteConfirmOverlay">
        <div class="profile-confirm-dialog">
            <p id="deleteConfirmText">Are you sure you want to remove this event?</p>
            <div class="profile-confirm-actions">
                <button type="button" class="confirm-cancel-btn" id="cancelDelete">Cancel</button>
                <button type="button" class="confirm-delete-btn" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const overlay = document.getElementById('deleteConfirmOverlay');
        const confirmText = document.getElementById('deleteConfirmText');
        const cancelBtn = document.getElementById('cancelDelete');
        const confirmBtn = document.getElementById('confirmDelete');

        let pendingRegId = null;
        let pendingItem = null;

        // When clicking the trash bin
        document.querySelectorAll('.profile-event-delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const regId = btn.dataset.regId;
                const eventName = btn.dataset.eventName || 'this event';

                pendingRegId = regId;
                pendingItem = btn.closest('.profile-event-item');
                confirmText.textContent = `Remove "${eventName}" from your registered events?`;

                overlay.classList.add('active');
            });
        });

        // Cancel button
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                overlay.classList.remove('active');
                pendingRegId = null;
                pendingItem = null;
            });
        }

        // Clicking backdrop closes
        if (overlay) {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    overlay.classList.remove('active');
                    pendingRegId = null;
                    pendingItem = null;
                }
            });
        }

        // Confirm delete
        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => {
                if (!pendingRegId) return;

                const formData = new FormData();
                formData.append('registration_id', pendingRegId);

                fetch('delete_registration.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (pendingItem) {
                            pendingItem.remove();
                        }
                    } else {
                        alert(data.message || 'Failed to delete registration.');
                    }
                })
                .catch(() => {
                    alert('Something went wrong. Please try again.');
                })
                .finally(() => {
                    overlay.classList.remove('active');
                    pendingRegId = null;
                    pendingItem = null;
                });
            });
        }
    });
    </script>
</body>
</html>
