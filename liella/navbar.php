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
                <a href="news.php" class="nav-link"><span>NEWS</span><span class="nav-heart"></span></a>
                <a href="events.php" class="nav-link"><span>EVENTS</span><span class="nav-heart"></span></a>
                <a href="members.php" class="nav-link active"><span>MEMBERS</span><span class="nav-heart"></span></a>
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