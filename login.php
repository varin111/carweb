<?php
ob_start();
require_once __DIR__ . '/config/front/header.php';
if (!empty(getSession('user_id') || !empty($_COOKIE['user_login']))) {
    if (auth()['is_admin'] === '1') {
        header("Location: " . SITE_URL . "/admin/index.php");
    } else {
        header("Location: " . SITE_URL . "/index.php");
    }
}
$errors = [
    'email' => '',
    'password' => '',
];
$values = [
    'email' => '',
    'password' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $values['email'] = clear($_POST['email']);
    $values['password'] = clear($_POST['password']);

    if (empty($values['email'])) {
        $errors['email'] = 'Email is required';
    } elseif (validate_email($values['email']) === false) {
        $errors['email'] = 'Email must be a valid email address';
    } else {
        $errors['email'] = '';
    }

    if (empty($values['password'])) {
        $errors['password'] = 'Password is required';
    } elseif (validate_string($values['password'], 8) === false) {
        $errors['password'] = 'Password must be 8 characters long';
    } else {
        $errors['password'] = '';
    }

    if (empty(array_filter($errors))) {
        $user = query_select('users', '*', "email = '{$values['email']}'");
        if ($user) {
            if (password_verify($values['password'], $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                if ($user['is_admin'] === '1') {
                    header("Location: " . SITE_URL . "/admin/index.php");
                } else {
                    header("Location: " . SITE_URL . "/index.php");
                }
            } else {
                $errors['password'] = 'Password is incorrect';
            }
        } else {
            $errors['email'] = 'Email is not registered';
        }
    }
}

?>
<div class="page page-center h-full d-flex align-items-center bg-white">
    <div class="container container-tight">
        <div class="card">
            <div class="text-center mt-4">
                <a href="<?= SITE_URL ?>" class="navbar-brand">
                    <img src="<?= SITE_URL ?>/assets/images/logo.png" alt="Tabler"
                        width="60" height="60">
                    <h2 class="fs-1">
                        <?= SITE_NAME ?>
                    </h2>
                </a>
            </div>
            <div class="card-body">
                <h2 class="fs-1 text-center mb-4">Login to your account</h2>
                <?= showSessionMessage('account-created-successfully') ?>
                <form
                    action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                    method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" class="form-control" placeholder="your@email.com"
                            name="email" value="<?= $values['email'] ?>">
                        <?= showErrors($errors['email']); ?>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">
                            Password
                        </label>
                        <div class="input-group input-group-flat border rounded"
                            x-data="{ show: false }">
                            <input type="password" class="form-control border-0" placeholder="Your password" autocomplete="off"
                                name="password" value="<?= $values['password'] ?>"
                                x-bind:type="show ? 'text' : 'password'">
                            <span class="input-group-text border-0">
                                <a href="#"
                                    x-on:click="show = !show"
                                    class="link-secondary border-0" data-bs-toggle="tooltip" aria-label="Show password" data-bs-original-title="Show password">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                                    </svg>
                                </a>
                            </span>
                        </div>
                        <?= showErrors($errors['password']); ?>
                    </div>
                    <div class="form-footer">
                        <button type="submit" name="login" class="btn btn-primary w-100">Sign in</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="text-center text-secondary mt-3">
            Don't have account yet? <a href="<?= SITE_URL . 'signup.php' ?>" tabindex="-1">Sign up</a>
        </div>
    </div>
</div>
<?php
require_once  __DIR__ . '/config/front/footer.php';
ob_end_flush();
