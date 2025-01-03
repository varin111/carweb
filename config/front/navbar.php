<!-- fixed-top -->
<header id="header" class="header d-flex align-items-center">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">
        <a href="<?= SITE_URL ?>" class="logo d-flex align-items-center me-auto">
            <img
                class="rounded-circle border border-2 border-light bg-white"
                style="width: 50px; height: 50px;"
                src="<?= SITE_URL ?>assets/images/logo.png" alt="<?= SITE_NAME ?>" />
            <h1 class="fs-2 text-light d-none d-md-block">
                <?= SITE_NAME ?>
            </h1>
        </a>
        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="<?= SITE_URL ?>">Home</a></li>
                <li><a href="<?= SITE_URL ?>#about">About</a></li>
                <li><a href="<?= SITE_URL ?>#services">Services</a></li>
                <li><a href="<?= SITE_URL ?>#team">Team</a></li>
                <li><a href="<?= SITE_URL ?>#pricing">Pricing</a></li>
                <li><a href="<?= SITE_URL ?>#contact">Contact</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>
        <?php if (isset($_SESSION['user_id'])) : ?>
            <div class="dropdown btn-getstarted bg-transparent px-0 mx-2 ms-4">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar" style="background-image: url('<?= getImagePath($auth['image_path']) ?>')"></span>
                </a>
                <div class="dropdown-menu pt-2">
                    <div class="ps-2 mb-1">
                        <strong><?= $auth['name'] ?></strong>
                        <span class="d-block text-muted">
                            <?= $auth['email'] ?>
                        </span>
                    </div>
                    <?php if ($auth['is_admin'] === '1') : ?>
                        <a href="<?= SITE_URL ?>admin/" class="dropdown-item">
                            Dashboard
                        </a>
                    <?php endif; ?>
                    <?php if ($auth != null) : ?>
                        <a href="<?= SITE_URL ?>home/vehicles/index.php" class="dropdown-item">
                            Vehicles
                        </a>
                    <?php endif; ?>
                    <a href="<?= SITE_URL ?>home/profile.php" class="dropdown-item">Profile</a>
                    <a href="<?= SITE_URL ?>logout.php" class="dropdown-item text-danger">Logout</a>
                </div>
            </div>
        <?php else : ?>
            <a class="btn-getstarted" href="<?= SITE_URL ?>login.php">Login</a>
            <a class="btn-getstarted"
                style="margin-left: 5px;"
            href="<?= SITE_URL ?>signup.php">Signup</a>
        <?php endif; ?>
    </div>
</header>