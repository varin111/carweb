
<?php
require_once('./config/vars.php');
// remove cookie
setcookie('user_login', '', time() - 3600, "/");
session_start();
session_destroy();
header("Location: " . SITE_URL . "login.php");
