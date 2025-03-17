<?php
require_once __DIR__ . '/../connection.php';
$env = parse_ini_file(__DIR__.'../../../.env', true);
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

    <style>
        #chat-icon {
            position: fixed;
            bottom: 80px;
            right: 30px;
            background: linear-gradient(135deg, #00b4db, #0083b0);
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        #chat-icon:hover {
            transform: scale(1.1);
        }

        #chat-icon span {
            font-size: 40px;
            color: white;
        }

        #chat-window {
            position: fixed;
            bottom: 150px;
            right: 30px;
            width: 400px;
            height: 600px;
            background-color: #fff;
            border-radius: 15px;
            display: none;
            flex-direction: column;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
            z-index: 999;
            overflow: hidden;
        }

        #chat-header {
            background: linear-gradient(135deg, #00b4db, #0083b0);
            color: white;
            padding: 20px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        #close-chat {
            cursor: pointer;
            font-size: 20px;
            color: white;
        }

        #messages {
            flex-grow: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background-color: #f5f7f9;
        }

        .message {
            max-width: 85%;
            padding: 12px 16px;
            border-radius: 15px;
            font-size: 14px;
            line-height: 1.4;
            position: relative;
            white-space: pre-wrap;
        }

        .user-message {
            background-color: #0083b0;
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 5px;
        }

        .bot-message {
            background-color: white;
            color: #1a1a1a;
            align-self: flex-start;
            border-bottom-left-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        #chat-input {
            padding: 20px;
            background-color: white;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }

        #input-box {
            flex-grow: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 25px;
            outline: none;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        #input-box:focus {
            border-color: #0083b0;
        }

        #send-btn {
            background: linear-gradient(135deg, #00b4db, #0083b0);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s ease;
        }

        #send-btn:hover {
            transform: scale(1.05);
        }

        .typing-indicator {
            display: flex;
            gap: 5px;
            padding: 12px 16px;
            background-color: white;
            border-radius: 15px;
            align-self: flex-start;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background-color: #90a4ae;
            border-radius: 50%;
            animation: typing 1s infinite ease-in-out;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .error-message {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 10px;
            margin: 10px 0;
            font-size: 14px;
            align-self: center;
        }
    </style>
</head>

<body class="main-body <?= isActivePages('index.php') ? 'index-page' : ''; ?>">
    <?php if (
        $_SERVER['PHP_SELF'] !== '/login.php' && $_SERVER['PHP_SELF'] !== '/signup.php'
        && str_contains($_SERVER['PHP_SELF'], 'bill/print.php') === false
    ) : ?>
        <?php require_once __DIR__ . '/navbar.php'; ?>
    <?php endif; ?>