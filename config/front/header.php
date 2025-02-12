<?php
require_once __DIR__ . '/../connection.php';
if (!empty(getSession('user_id')) || !empty($_COOKIE['user_login'])) {
    if (!empty($_COOKIE['user_login'])) {
        setSession('user_id', $_COOKIE['user_login']);
    } else {
        // set to cookie for 30 days
        // 86400 = 1 day
        setcookie('user_login', getSession('user_id'), time() + (86400 * 30), "/");
    }
    $auth = auth();
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

<body class="main-body <?= isActivePages('index.php') ? 'index-page' : ''; ?>">
    <?php if (
        $_SERVER['PHP_SELF'] !== '/login.php' && $_SERVER['PHP_SELF'] !== '/signup.php'
        && str_contains($_SERVER['PHP_SELF'], 'bill/print.php') === false
    ) : ?>
        <?php require_once __DIR__ . '/navbar.php'; ?>
    <?php endif; ?>