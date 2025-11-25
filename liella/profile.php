<?php
session_start();
require_once 'db.php';

// Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

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

$isAdmin = ($username === 'admin');

// ----- HANDLE POST ACTIONS (avatar + admin CRUD) -----
$adminMessage = '';
$adminError   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Avatar change (for everyone)
    if (isset($_POST['avatar'])) {
        $selected = $_POST['avatar'];

        if (in_array($selected, $avatarChoices, true)) {
            $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("si", $selected, $userId);
                $stmt->execute();
                $stmt->close();

                $_SESSION['profile_image'] = $selected;
                $currentAvatar = $selected;
            }
        }
    }
    // 2) Admin-only CRUD
    elseif ($isAdmin && isset($_POST['admin_action'])) {
        $action = $_POST['admin_action'];

        if ($action === 'create') {
            $name = trim($_POST['event_name'] ?? '');
            $date = trim($_POST['event_date'] ?? '');
            $venue = trim($_POST['venue'] ?? '');
            $desc = trim($_POST['description'] ?? '');

            if ($name === '' || $date === '') {
                $adminError = "Event name and date are required.";
            } else {
                $stmt = $conn->prepare("
                    INSERT INTO events (event_name, event_date, venue, description)
                    VALUES (?, ?, ?, ?)
                ");
                if ($stmt) {
                    $stmt->bind_param("ssss", $name, $date, $venue, $desc);
                    if ($stmt->execute()) {
                        $adminMessage = "Event created successfully.";
                    } else {
                        $adminError = "Failed to create event.";
                    }
                    $stmt->close();
                }
            }
        }

        if ($action === 'update') {
            $id   = (int)($_POST['event_id'] ?? 0);
            $name = trim($_POST['event_name'] ?? '');
            $date = trim($_POST['event_date'] ?? '');
            $venue = trim($_POST['venue'] ?? '');
            $desc = trim($_POST['description'] ?? '');

            if ($id <= 0 || $name === '' || $date === '') {
                $adminError = "Event ID, name and date are required.";
            } else {
                $stmt = $conn->prepare("
                    UPDATE events
                    SET event_name = ?, event_date = ?, venue = ?, description = ?
                    WHERE id = ?
                ");
                if ($stmt) {
                    $stmt->bind_param("ssssi", $name, $date, $venue, $desc, $id);
                    if ($stmt->execute()) {
                        $adminMessage = "Event updated successfully.";
                    } else {
                        $adminError = "Failed to update event.";
                    }
                    $stmt->close();
                }
            }
        }

        if ($action === 'delete') {
            $id = (int)($_POST['event_id'] ?? 0);
            if ($id <= 0) {
                $adminError = "Invalid event ID.";
            } else {
                // optional: delete registrations tied to this event
                $delReg = $conn->prepare("DELETE FROM event_registrations WHERE event_id = ?");
                if ($delReg) {
                    $delReg->bind_param("i", $id);
                    $delReg->execute();
                    $delReg->close();
                }

                $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $id);
                    if ($stmt->execute()) {
                        $adminMessage = "Event deleted successfully.";
                    } else {
                        $adminError = "Failed to delete event.";
                    }
                    $stmt->close();
                }
            }
        }
    }
}

// ----- LOAD DATA: My Events (normal user) or Events list (admin) -----
$myEvents = [];
$allEvents = [];

if ($isAdmin) {
    $res = $conn->query("
        SELECT id, event_name, event_date, venue, description
        FROM events
        ORDER BY event_date ASC
    ");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $allEvents[] = $row;
        }
        $res->free();
    }
} else {
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

            <!-- Top bar -->
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
                    <p>
                        <?php if ($isAdmin): ?>
                            Managing Liella! Live Events ✨
                        <?php else: ?>
                            Welcome back to Liella! Live Events 💫
                        <?php endif; ?>
                    </p>
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

            <?php if ($isAdmin): ?>
                <!-- =========================
                     ADMIN EVENT MANAGER
                     ========================= -->
                <section class="profile-events-section admin-events-section">
                    <div class="profile-events-header">
                        <h2>Event Manager</h2>
                        <p>Create, update, and manage Liella! events.</p>
                    </div>

                    <?php if ($adminMessage): ?>
                        <p class="profile-admin-message" style="color:#1a8f4a;margin-top:8px;">
                            <?php echo htmlspecialchars($adminMessage); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($adminError): ?>
                        <p class="profile-admin-message" style="color:#d52c5b;margin-top:8px;">
                            <?php echo htmlspecialchars($adminError); ?>
                        </p>
                    <?php endif; ?>

                    <div style="margin-top:14px; margin-bottom:10px;">
                        <button type="button" id="adminAddEventBtn" class="profile-save-avatar-btn">
                            <i class="ri-add-line"></i>
                            <span>Add New Event</span>
                        </button>
                    </div>

                    <?php if (empty($allEvents)): ?>
                        <p class="profile-no-events">No events found in the database.</p>
                    <?php else: ?>
                        <ul class="profile-events-list admin-events-list">
                            <?php foreach ($allEvents as $ev): ?>
                                <?php
                                    $dateObj = new DateTime($ev['event_date']);
                                    $monthShort = strtoupper($dateObj->format('M'));
                                    $day = $dateObj->format('d');
                                ?>
                                <li class="profile-event-item admin-event-item"
                                    data-event-id="<?php echo (int)$ev['id']; ?>"
                                    data-event-name="<?php echo htmlspecialchars($ev['event_name'], ENT_QUOTES); ?>"
                                    data-event-date="<?php echo $dateObj->format('Y-m-d'); ?>"
                                    data-event-venue="<?php echo htmlspecialchars($ev['venue'] ?? '', ENT_QUOTES); ?>"
                                    data-event-description="<?php echo htmlspecialchars($ev['description'] ?? '', ENT_QUOTES); ?>">
                                    <div class="profile-event-date">
                                        <span class="profile-event-month"><?php echo $monthShort; ?></span>
                                        <span class="profile-event-day"><?php echo $day; ?></span>
                                    </div>
                                    <div class="profile-event-details">
                                        <div class="profile-event-mainline">
                                            <span class="profile-event-name">
                                                <?php echo htmlspecialchars($ev['event_name']); ?>
                                            </span>
                                            <div class="admin-event-actions">
                                                <button type="button"
                                                        class="admin-event-btn admin-edit-btn"
                                                        data-event-id="<?php echo (int)$ev['id']; ?>">
                                                    <i class="ri-edit-2-line"></i>
                                                </button>
                                                <button type="button"
                                                        class="admin-event-btn admin-delete-btn"
                                                        data-event-id="<?php echo (int)$ev['id']; ?>">
                                                    <i class="ri-delete-bin-5-line"></i>
                                                </button>
                                                <button type="button"
                                                        class="admin-event-btn admin-viewreg-btn"
                                                        data-event-id="<?php echo (int)$ev['id']; ?>">
                                                    <i class="ri-user-3-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php if (!empty($ev['venue'])): ?>
                                            <span class="profile-event-venue">
                                                <?php echo htmlspecialchars($ev['venue']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($ev['description'])): ?>
                                            <span class="profile-event-email">
                                                <?php echo htmlspecialchars($ev['description']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>

            <?php else: ?>
                <!-- =========================
                     NORMAL USER: MY EVENTS
                     ========================= -->
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
            <?php endif; ?>
        </div>
    </main>

    <!-- Simple delete confirmation dialog (for normal user registrations) -->
    <div class="profile-confirm-overlay" id="deleteConfirmOverlay">
        <div class="profile-confirm-dialog">
            <p id="deleteConfirmText">Are you sure you want to remove this event?</p>
            <div class="profile-confirm-actions">
                <button type="button" class="confirm-cancel-btn" id="cancelDelete">Cancel</button>
                <button type="button" class="confirm-delete-btn" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>

    <!-- ADMIN MODAL: ADD / EDIT EVENT -->
    <?php if ($isAdmin): ?>
    <div class="profile-confirm-overlay" id="adminEventModal">
        <div class="profile-confirm-dialog" style="max-width:420px;">
            <h3 id="adminEventModalTitle" style="margin-bottom:8px;">New Event</h3>
            <form method="post" id="adminEventForm">
                <input type="hidden" name="admin_action" id="adminActionField" value="create">
                <input type="hidden" name="event_id" id="adminEventIdField" value="">

                <div class="form-row">
                    <label for="adminEventName">Event Name</label>
                    <input type="text" id="adminEventName" name="event_name" required>
                </div>
                <div class="form-row">
                    <label for="adminEventDate">Event Date</label>
                    <input type="date" id="adminEventDate" name="event_date" required>
                </div>
                <div class="form-row">
                    <label for="adminEventVenue">Venue</label>
                    <input type="text" id="adminEventVenue" name="venue">
                </div>
                <div class="form-row">
                    <label for="adminEventDesc">Description</label>
                    <textarea id="adminEventDesc" name="description" rows="3"
                              style="width:100%; border-radius:12px; border:1px solid rgba(255,142,195,0.6); padding:6px 8px;"></textarea>
                </div>

                <div class="profile-confirm-actions" style="margin-top:12px;">
                    <button type="button" class="confirm-cancel-btn" id="adminEventCancelBtn">Cancel</button>
                    <button type="submit" class="confirm-delete-btn" id="adminEventSaveBtn">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ADMIN MODAL: VIEW REGISTRATIONS -->
    <div class="profile-confirm-overlay" id="adminRegModal">
        <div class="profile-confirm-dialog" style="max-width:460px;">
            <h3 style="margin-bottom:6px;">Event Registrations</h3>
            <p id="adminRegSubtitle" style="font-size:0.85rem; color:#7b4c74; margin-bottom:8px;"></p>
            <div id="adminRegContent" style="max-height:260px; overflow-y:auto;">
                <!-- filled by JS -->
            </div>
            <div class="profile-confirm-actions" style="margin-top:12px;">
                <button type="button" class="confirm-cancel-btn" id="adminRegCloseBtn">Close</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="js/script.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // ===========================
        // NORMAL USER DELETE REG
        // ===========================
        const overlay = document.getElementById('deleteConfirmOverlay');
        const confirmText = document.getElementById('deleteConfirmText');
        const cancelBtn = document.getElementById('cancelDelete');
        const confirmBtn = document.getElementById('confirmDelete');

        let pendingRegId = null;
        let pendingItem = null;

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

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                overlay.classList.remove('active');
                pendingRegId = null;
                pendingItem = null;
            });
        }

        if (overlay) {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    overlay.classList.remove('active');
                    pendingRegId = null;
                    pendingItem = null;
                }
            });
        }

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

        // ===========================
        // ADMIN EVENT CRUD + VIEW REGS
        // ===========================
        const isAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;
        if (!isAdmin) return;

        const adminEventModal = document.getElementById('adminEventModal');
        const adminEventForm  = document.getElementById('adminEventForm');
        const adminModalTitle = document.getElementById('adminEventModalTitle');
        const adminActionField= document.getElementById('adminActionField');
        const adminEventIdField = document.getElementById('adminEventIdField');
        const adminEventName  = document.getElementById('adminEventName');
        const adminEventDate  = document.getElementById('adminEventDate');
        const adminEventVenue = document.getElementById('adminEventVenue');
        const adminEventDesc  = document.getElementById('adminEventDesc');
        const adminEventCancelBtn = document.getElementById('adminEventCancelBtn');

        const addBtn = document.getElementById('adminAddEventBtn');

        function openAdminEventModal(mode, data = null) {
            if (mode === 'create') {
                adminModalTitle.textContent = 'Add New Event';
                adminActionField.value = 'create';
                adminEventIdField.value = '';
                adminEventName.value = '';
                adminEventDate.value = '';
                adminEventVenue.value = '';
                adminEventDesc.value = '';
            } else if (mode === 'update' && data) {
                adminModalTitle.textContent = 'Edit Event';
                adminActionField.value = 'update';
                adminEventIdField.value = data.id || '';
                adminEventName.value = data.name || '';
                adminEventDate.value = data.date || '';
                adminEventVenue.value = data.venue || '';
                adminEventDesc.value = data.desc || '';
            }
            adminEventModal.classList.add('active');
        }

        function closeAdminEventModal() {
            adminEventModal.classList.remove('active');
        }

        if (addBtn) {
            addBtn.addEventListener('click', () => {
                openAdminEventModal('create');
            });
        }

        if (adminEventCancelBtn) {
            adminEventCancelBtn.addEventListener('click', () => {
                closeAdminEventModal();
            });
        }

        if (adminEventModal) {
            adminEventModal.addEventListener('click', e => {
                if (e.target === adminEventModal) {
                    closeAdminEventModal();
                }
            });
        }

        // Edit buttons
        document.querySelectorAll('.admin-edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const item = btn.closest('.admin-event-item');
                if (!item) return;
                const data = {
                    id: item.dataset.eventId,
                    name: item.dataset.eventName,
                    date: item.dataset.eventDate,
                    venue: item.dataset.eventVenue,
                    desc: item.dataset.eventDescription
                };
                openAdminEventModal('update', data);
            });
        });

        // Delete buttons
        document.querySelectorAll('.admin-delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.eventId;
                if (!id) return;
                if (!confirm('Delete this event (and its registrations)?')) return;

                const form = document.createElement('form');
                form.method = 'post';
                form.action = 'profile.php';

                const act = document.createElement('input');
                act.type = 'hidden';
                act.name = 'admin_action';
                act.value = 'delete';

                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'event_id';
                idInput.value = id;

                form.appendChild(act);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            });
        });

        // View registrations
        const adminRegModal   = document.getElementById('adminRegModal');
        const adminRegContent = document.getElementById('adminRegContent');
        const adminRegSubtitle= document.getElementById('adminRegSubtitle');
        const adminRegClose   = document.getElementById('adminRegCloseBtn');

        function openRegModal() {
            adminRegModal.classList.add('active');
        }
        function closeRegModal() {
            adminRegModal.classList.remove('active');
            adminRegContent.innerHTML = '';
        }

        if (adminRegClose) {
            adminRegClose.addEventListener('click', closeRegModal);
        }
        if (adminRegModal) {
            adminRegModal.addEventListener('click', e => {
                if (e.target === adminRegModal) {
                    closeRegModal();
                }
            });
        }

        document.querySelectorAll('.admin-viewreg-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const item = btn.closest('.admin-event-item');
                if (!item) return;

                const eventId = item.dataset.eventId;
                const eventName = item.dataset.eventName || 'This Event';

                adminRegSubtitle.textContent = eventName;

                adminRegContent.innerHTML = '<p style="font-size:0.9rem;color:#7b4c74;">Loading...</p>';
                openRegModal();

                fetch('get_event_registrations.php?event_id=' + encodeURIComponent(eventId))
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            adminRegContent.innerHTML =
                                '<p style="font-size:0.9rem;color:#d52c5b;">' +
                                (data.message || 'Failed to load registrations.') +
                                '</p>';
                            return;
                        }

                        if (!data.registrations || data.registrations.length === 0) {
                            adminRegContent.innerHTML =
                                '<p style="font-size:0.9rem;color:#7b4c74;">No one has registered for this event yet.</p>';
                            return;
                        }

                        let html = '<table style="width:100%;border-collapse:collapse;font-size:0.85rem;">';
                        html += '<thead><tr>' +
                                '<th style="text-align:left;padding:4px 6px;border-bottom:1px solid rgba(255,142,195,0.6);">User</th>' +
                                '<th style="text-align:left;padding:4px 6px;border-bottom:1px solid rgba(255,142,195,0.6);">Email</th>' +
                                '</tr></thead><tbody>';

                        data.registrations.forEach(row => {
                            html += '<tr>' +
                                '<td style="padding:4px 6px;border-bottom:1px solid rgba(255,142,195,0.2);">' +
                                    (row.user_name || '—') +
                                '</td>' +
                                '<td style="padding:4px 6px;border-bottom:1px solid rgba(255,142,195,0.2);">' +
                                    (row.email || '—') +
                                '</td>' +
                                '</tr>';
                        });

                        html += '</tbody></table>';
                        adminRegContent.innerHTML = html;
                    })
                    .catch(() => {
                        adminRegContent.innerHTML =
                            '<p style="font-size:0.9rem;color:#d52c5b;">Something went wrong. Please try again.</p>';
                    });
            });
        });
    });
    </script>
</body>
</html>
