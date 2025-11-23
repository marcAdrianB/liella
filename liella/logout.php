<?php
session_start();
session_unset();
session_destroy();

// You can also clear any cookies here if you set them

header("Location: login.php");
exit;
