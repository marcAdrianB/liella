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
    <title>Liella! Members</title>
    <link rel="icon" type="image/x-icon" href="assets/liella.ico">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">

    <style>
   /* FULL SCREEN SECTION */
.members-hero {
   
    width: 100%;

    min-height: 100vh;
    position: relative;
    overflow: hidden;
    padding-top: 120px;   /* keeps portraits below navbar */


    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 7%;
    background: #c7eaff;  /* updated dynamically via JS */
    transition: background 0.6s ease;

    
}

.members-hero {
    min-height: 100vh;
    position: relative;
    overflow: hidden;

    /* The important part */
    padding-top: 100px;   /* adjust until head is fully visible */
    flex-direction: row;

    padding-bottom: 0 !important;
    margin-bottom: -1px; /* removes visible gap */
}

.members-hero::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 180px;
    pointer-events: none;
    background: linear-gradient(to top,
        rgba(255, 182, 216, 0.9),
        rgba(255, 182, 216, 0)
    );
}
.members-hero {
    overflow: hidden; /* KEEP this */
}
.members-hero,
footer {
    margin-top: 0;
    margin-bottom: 0;
}

/* PORTRAIT — NO CONTAINER, NATURAL SCALE */
.member-portrait {
  
    position: absolute;
    bottom: -120px;          /* pushes the portrait downward */
    left: 0;
    transform-origin: bottom center;   /* IMPORTANT: scale from feet */
    transform: scale(1.25);            /* enlarge without cutting head */


    left: 0;
   
    height: 130vh;         /* oversized so half body appears */
    
    opacity: 0;
    transition: opacity .5s ease, transform .5s ease;
    pointer-events: none;
}

.member-portrait.active {
    opacity: 1;
    transform: translateY(10%);

}

/* RIGHT SIDE PANEL */
.members-info-panel {
    position: relative;
    z-index: 5;
    width: 45%;
    display: flex;
    flex-direction: column;
    gap: 18px;
    top: -40px;   /* moves everything up */
    margin-top: -4vh;
}

/* AVATAR BUTTONS */
.members-avatar-row {
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
    justify-content: center;
}

.member-avatar-btn {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid transparent;
    background: #fff;
    cursor: pointer;
    transition: 0.25s ease;
}

.member-avatar-btn.active {
    border-color: #ff4f9b;
    transform: scale(1.1);
}

.member-avatar-btn:hover {
    transform: translateY(-4px) scale(1.06);
}
.member-avatar-btn.active {
    border-color: #ff4f9b;
    transform: scale(1.08);
}

/* NAME */
.member-name {
    font-size: 3rem;
    font-weight: 700;
    color: #3a102f;
    opacity: 0;
    transform: translateX(-20px);
    transition: .5s;
}
.member-name.active {
    opacity: 1;
    transform: translateX(0);
}

/* FROSTED GLASS DETAILS BOX */
.member-details-box {
    background: rgba(255, 255, 255, 0.55);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    border-radius: 26px;
    padding: 22px 28px;
    box-shadow: 0 10px 40px rgba(255, 79, 155, 0.22);
}
.member-avatar-btn img {
    width: 100%;
    height: 100%;
    object-fit: contain;   /* fixes zoom issue */
}


.member-details-box.active {
    opacity: 1;
    transform: translateY(0);
}

/* GRADIENT GLOW AT BOTTOM */
.members-bottom-glow {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 200px;
    pointer-events: none;
    background: linear-gradient(to bottom, transparent, rgba(255, 152, 216, 0.75));
    z-index: 4;
}

    </style>
</head>
<body class="members-page">

<!-- GLOBAL PAGE TRANSITION -->
<div id="transition-frame">
    <div class="panel"></div>
    <img src="assets/Liella!_Official_Logo_White.png" alt="logo">
</div>

<?php include 'navbar.php'; ?>

<main>
    <!-- ============= SECTION 1: MEMBERS ============= -->
   <section class="members-hero" id="membersHero">

    <img class="member-portrait active" data-member="kanon"
         src="assets/members/Kanon_Shibuya.webp" alt="Kanon">
    <img class="member-portrait" data-member="keke"
         src="assets/members/Tang_Keke.webp" alt="Keke">
    <img class="member-portrait" data-member="chisato"
         src="assets/members/Chisato_Arashi.webp" alt="Chisato">
    <img class="member-portrait" data-member="sumire"
         src="assets/members/Sumire_Heanna.webp" alt="Sumire">
    <img class="member-portrait" data-member="ren"
         src="assets/members/Ren_Hazuki.webp" alt="Ren">

    <div class="members-info-panel">
        <div class="members-avatar-row">
            <button class="member-avatar-btn active" data-member="kanon">
                <img src="assets/avatars/liella_kanon.png">
            </button>
            <button class="member-avatar-btn" data-member="keke">
                <img src="assets/avatars/liella_keke.png">
            </button>
            <button class="member-avatar-btn" data-member="chisato">
                <img src="assets/avatars/liella_chisato.png">
            </button>
            <button class="member-avatar-btn" data-member="sumire">
                <img src="assets/avatars/liella_sumire.png">
            </button>
            <button class="member-avatar-btn" data-member="ren">
                <img src="assets/avatars/liella_ren.png">
            </button>
        </div>

        <h1 id="memberName" class="member-name active">Kanon Shibuya</h1>
        <p id="memberSub" class="member-sub">
            A bright melody that started it all for Liella!.
        </p>

        <div id="memberDetails" class="member-details-box active">
            <p><strong>Birthday:</strong> May 1</p>
            <p><strong>Blood Type:</strong> A</p>
            <p><strong>Height:</strong> 159 cm</p>
        </div>
    </div>

    <!-- gradient glow -->
    <div class="members-bottom-glow"></div>

</section>

    <!-- ============= SECTION 2: SNS FOOTER ============= -->
    <?php include 'footer.php'; ?>
</main>

<script src="js/script.js"></script>

<script>
// ===== MEMBER DATA =====
const memberData = {
    kanon: {
        name: "Kanon Shibuya",
        sub: "A bright melody that started it all for Liella!.",
        birthday: "May 1",
        blood: "A",
        height: "159 cm",
        bg: "linear-gradient(135deg, #ffe2c7, #ffb3d9)"
    },
    keke: {
        name: "Keke Tang",
        sub: "The overseas idol fan who turned her dream into Liella! reality.",
        birthday: "Jul 17",
        blood: "O",
        height: "159 cm",
        bg: "linear-gradient(135deg, #ffd1e9, #ffb3ff)"
    },
    chisato: {
        name: "Chisato Arashi",
        sub: "Cheerful childhood friend with steps as light as her smile.",
        birthday: "Feb 25",
        blood: "B",
        height: "155 cm",
        bg: "linear-gradient(135deg, #d7f8ff, #b4ecff)"
    },
    sumire: {
        name: "Sumire Heanna",
        sub: "Aiming for center stage with pure superstar energy.",
        birthday: "Sep 28",
        blood: "AB",
        height: "161 cm",
        bg: "linear-gradient(135deg, #eaffd6, #c8ffb3)"
    },
    ren: {
        name: "Ren Hazuki",
        sub: "Refined and strict, yet quietly moved by Liella!'s song.",
        birthday: "Nov 24",
        blood: "A",
        height: "163 cm",
        bg: "linear-gradient(135deg, #ffe0c2, #ffc48a)"
    }
};

const heroSection   = document.getElementById('membersHero');
const portraits     = document.querySelectorAll('.member-portrait');
const avatarButtons = document.querySelectorAll('.member-avatar-btn');
const nameEl        = document.getElementById('memberName');
const subEl         = document.getElementById('memberSub');
const detailsEl     = document.getElementById('memberDetails');

function switchMember(key) {
    const data = memberData[key];
    if (!data) return;

    // Avatar active state
    avatarButtons.forEach(btn =>
        btn.classList.toggle('active', btn.dataset.member === key)
    );

    // Portrait crossfade
    portraits.forEach(img => {
        img.classList.toggle('active', img.dataset.member === key);
    });

    // Crossfade text
    nameEl.classList.remove('active');
    detailsEl.classList.remove('active');

    setTimeout(() => {
        nameEl.textContent = data.name;
        subEl.textContent  = data.sub;
        detailsEl.innerHTML = `
            <p><strong>Birthday:</strong> ${data.birthday}</p>
            <p><strong>Blood Type:</strong> ${data.blood}</p>
            <p><strong>Height:</strong> ${data.height}</p>
        `;

        nameEl.classList.add('active');
        detailsEl.classList.add('active');

        // Flat background + glow is applied to the hero section
        heroSection.style.background = data.bg;
    }, 120);
}

// Hook up avatar clicks
avatarButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        const key = btn.dataset.member;
        switchMember(key);
    });
});
</script>

</body>
</html>

