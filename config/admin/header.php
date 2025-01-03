<?php
require_once __DIR__ . '/../connection.php';
if (empty(getSession('user_id')) && empty($_COOKIE['user_login'])) {
    header("Location: " . SITE_URL . "login.php");
} else {
    if (!empty($_COOKIE['user_login'])) {
        setSession('user_id', $_COOKIE['user_login']);
    } else {
        // set to cookie for 30 days
        // 86400 = 1 day
        setcookie('user_login', getSession('user_id'), time() + (86400 * 30), "/");
    }
    $auth = auth();
    if (auth()['is_admin'] !== '1') {
        header("Location: " . SITE_URL . "index.php");
    }
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>
        <?= SITE_NAME; ?>
    </title>
    <!-- CSS files -->
    <?= getCssLinks() ?>
    <!-- js -->
    <?= getJsLinks() ?>
</head>

<body class="page bg-body-emphasis">
    <!-- Sidebar -->
    <?php require "sidebar.php"; ?>
    <!-- End of Sidebar -->
    <div class="page-wrapper">
        <div class="page-body d-print-none">
            <div class="container-xl">