<aside class="navbar navbar-vertical navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="<?= SITE_URL ?>/index.php"
                class="d-flex align-items-center justify-content-center gap-2">
                <img src="<?= SITE_URL ?>/assets/images/logo.png" width="110" height="60" alt="Tabler" class="navbar-brand-image">
                <span>
                    <?= SITE_NAME; ?>
                </span>
            </a>
        </h1>
        <div class="navbar-nav flex-row d-lg-none">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm" style="background-image: url('<?= getImagePath($auth['image_path']) ?>')"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="<?= SITE_URL ?>" class="dropdown-item">Home</a>
                    <a href="<?= SITE_URL . '/admin/profile.php' ?>" class="dropdown-item">Profile</a>
                    <a href="<?= SITE_URL . '/logout.php' ?>" class=" dropdown-item text-danger">Logout</a>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <div class="p-2 d-none d-lg-block">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                        <span class="avatar avatar-sm" style="background-image: url('<?= getImagePath($auth['image_path']) ?>')"></span>
                        <div class="d-block ps-2">
                            <?= $auth['name']; ?>
                        </div>
                    </a>
                    <div class="dropdown-menu mt-1 pt-2">
                        <a href="<?= SITE_URL ?>" class="dropdown-item">Home</a>
                        <a href="<?= SITE_URL . '/admin/profile.php' ?>" class="dropdown-item">Profile</a>
                        <a href="<?= SITE_URL . '/logout.php' ?>" class=" dropdown-item text-danger">Logout</a>
                    </div>
                </div>
            </div>
            <ul class="navbar-nav pt-lg-3 gap-2 pb-3">
                <li class="nav-item <?= isActivePages('/admin/index.php', 'admin') ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?= SITE_URL . '/admin/index.php' ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="6.25" cy="6.25" r="4.25" stroke="currentColor" stroke-width="1.5" />
                                <path d="M18 9.35714V10.5M18 9.35714C16.9878 9.35714 16.0961 8.85207 15.573 8.08517M18 9.35714C19.0122 9.35714 19.9039 8.85207 20.427 8.08517M18 3.64286C19.0123 3.64286 19.9041 4.148 20.4271 4.915M18 3.64286C16.9877 3.64286 16.0959 4.148 15.5729 4.915M18 3.64286V2.5M21.5 4.21429L20.4271 4.915M14.5004 8.78571L15.573 8.08517M14.5 4.21429L15.5729 4.915M21.4996 8.78571L20.427 8.08517M20.4271 4.915C20.7364 5.36854 20.9167 5.91364 20.9167 6.5C20.9167 7.08643 20.7363 7.63159 20.427 8.08517M15.5729 4.915C15.2636 5.36854 15.0833 5.91364 15.0833 6.5C15.0833 7.08643 15.2637 7.63159 15.573 8.08517" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <circle cx="17.75" cy="17.75" r="4.25" stroke="currentColor" stroke-width="1.5" />
                                <circle cx="6.25" cy="17.75" r="4.25" stroke="currentColor" stroke-width="1.5" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Dashboard
                        </span>
                    </a>
                </li>
                <li class="nav-item  <?= isActivePages('/admin/users/index.php', '/admin/users/user-action.php?action=add') ? 'active' : ''; ?> dropdown">
                    <a class="nav-link dropdown-toggle show" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="<?= isActivePages(['/admin/users/index.php', '/admin/users/user-action.php']) ? 'true' : ''; ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M13 7C13 9.20914 11.2091 11 9 11C6.79086 11 5 9.20914 5 7C5 4.79086 6.79086 3 9 3C11.2091 3 13 4.79086 13 7Z" stroke="currentColor" stroke-width="1.5" />
                                <path d="M15 11C17.2091 11 19 9.20914 19 7C19 4.79086 17.2091 3 15 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11 14H7C4.23858 14 2 16.2386 2 19C2 20.1046 2.89543 21 4 21H14C15.1046 21 16 20.1046 16 19C16 16.2386 13.7614 14 11 14Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                <path d="M17 14C19.7614 14 22 16.2386 22 19C22 20.1046 21.1046 21 20 21H18.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            User List
                        </span>
                    </a>
                    <div class="dropdown-menu <?= isActivePages('/admin/users/index.php', '/admin/users/user-action.php?action=add') ? 'show' : ''; ?>">
                        <div class="dropdown-menu-column">
                            <a class="dropdown-item <?= isActivePages('/admin/users/index.php') ? 'active' : ''; ?>" href="<?= SITE_URL . '/admin/users/index.php' ?>">
                                User List
                            </a>
                            <a class="dropdown-item <?= isActivePages('/admin/users/user-action.php?action=add') ? 'active' : ''; ?>" href="<?= SITE_URL . '/admin/users/user-action.php?action=add' ?>">
                                Add User
                            </a>
                        </div>
                    </div>
                </li>
                <li class="nav-item  <?= isActivePages('/admin/customers/index.php', '/admin/customers/user-action.php') ? 'active' : ''; ?> dropdown">
                    <a class="nav-link dropdown-toggle show" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="<?= isActivePages(['/admin/customers/index.php', '/admin/customers/user-action.php']) ? 'true' : ''; ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M13 7C13 9.20914 11.2091 11 9 11C6.79086 11 5 9.20914 5 7C5 4.79086 6.79086 3 9 3C11.2091 3 13 4.79086 13 7Z" stroke="currentColor" stroke-width="1.5" />
                                <path d="M15 11C17.2091 11 19 9.20914 19 7C19 4.79086 17.2091 3 15 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11 14H7C4.23858 14 2 16.2386 2 19C2 20.1046 2.89543 21 4 21H14C15.1046 21 16 20.1046 16 19C16 16.2386 13.7614 14 11 14Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                <path d="M17 14C19.7614 14 22 16.2386 22 19C22 20.1046 21.1046 21 20 21H18.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Customer List
                        </span>
                    </a>
                    <div class="dropdown-menu <?= isActivePages('/admin/customers/index.php', '/admin/customers/user-action.php') ? 'show' : ''; ?>">
                        <div class="dropdown-menu-column">
                            <a class="dropdown-item <?= isActivePages('/admin/customers/index.php') ? 'active' : ''; ?>" href="<?= SITE_URL . '/admin/customers/index.php' ?>">
                                Customer List
                            </a>
                        </div>
                    </div>
                </li>
                <li class="nav-item  <?= isActivePages('/admin/policies/index.php', '/admin/policies/policy-action.php?action=add') ? 'active' : ''; ?> dropdown">
                    <a class="nav-link dropdown-toggle show" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="<?= isActivePages(['/admin/policies/index.php', '/admin/policies/policy-action.php']) ? 'true' : ''; ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4.26759 4.32782C5.95399 3.02741 8.57337 2 12 2C15.4266 2 18.046 3.02741 19.7324 4.32782C19.9693 4.51048 20.0877 4.60181 20.1849 4.76366C20.2665 4.89952 20.3252 5.10558 20.3275 5.26404C20.3302 5.4528 20.2672 5.62069 20.1413 5.95648C19.8305 6.78539 19.6751 7.19984 19.6122 7.61031C19.533 8.12803 19.5322 8.25474 19.6053 8.77338C19.6632 9.18457 19.9795 10.0598 20.6121 11.8103C20.844 12.452 21 13.1792 21 14C21 17 18.5 19.375 16 20C13.8082 20.548 12.6667 21.3333 12 22C11.3333 21.3333 10.1918 20.548 8 20C5.5 19.375 3 17 3 14C3 13.1792 3.15595 12.452 3.38785 11.8103C4.0205 10.0598 4.33682 9.18457 4.39473 8.77338C4.46777 8.25474 4.46702 8.12803 4.38777 7.61031C4.32494 7.19984 4.16952 6.78539 3.85868 5.95648C3.73276 5.62069 3.6698 5.4528 3.67252 5.26404C3.6748 5.10558 3.73351 4.89952 3.81509 4.76366C3.91227 4.60181 4.03071 4.51048 4.26759 4.32782Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M12.6911 7.57767L13.395 8.99715C13.491 9.19475 13.7469 9.38428 13.9629 9.42057L15.2388 9.6343C16.0547 9.77141 16.2467 10.3682 15.6587 10.957L14.6668 11.9571C14.4989 12.1265 14.4069 12.4531 14.4589 12.687L14.7428 13.925C14.9668 14.9049 14.4509 15.284 13.591 14.7718L12.3951 14.0581C12.1791 13.929 11.8232 13.929 11.6032 14.0581L10.4073 14.7718C9.5514 15.284 9.03146 14.9009 9.25543 13.925L9.5394 12.687C9.5914 12.4531 9.49941 12.1265 9.33143 11.9571L8.33954 10.957C7.7556 10.3682 7.94358 9.77141 8.75949 9.6343L10.0353 9.42057C10.2473 9.38428 10.5033 9.19475 10.5993 8.99715L11.3032 7.57767C11.6872 6.80744 12.3111 6.80744 12.6911 7.57767Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Policy List
                        </span>
                    </a>
                    <div class="dropdown-menu <?= isActivePages('/admin/policies/index.php', '/admin/policies/policy-action.php?action=add') ? 'show' : ''; ?>">
                        <div class="dropdown-menu-column">
                            <a class="dropdown-item <?= isActivePages('/admin/policies/index.php') ? 'active' : ''; ?>" href="<?= SITE_URL . '/admin/policies/index.php' ?>">
                                Policy List
                            </a>
                            <a class="dropdown-item <?= isActivePages('/admin/policies/policy-action.php?action=add') ? 'active' : ''; ?>" href="<?= SITE_URL . '/admin/policies/policy-action.php?action=add' ?>">
                                Add Policy
                            </a>
                        </div>
                    </div>
                </li>
                <li class="nav-item <?= isActivePages('/admin/claims/index.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?= SITE_URL . '/admin/claims/index.php' ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7.5 4.94531H16C16.8284 4.94531 17.5 5.61688 17.5 6.44531V7.94531" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M15 12.9453H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M12 16.9453H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M18.497 2L6.30767 2.00002C5.81071 2.00002 5.30241 2.07294 4.9007 2.36782C3.62698 3.30279 2.64539 5.38801 4.62764 7.2706C5.18421 7.7992 5.96217 7.99082 6.72692 7.99082H18.2835C19.077 7.99082 20.5 8.10439 20.5 10.5273V17.9812C20.5 20.2007 18.7103 22 16.5026 22H7.47246C5.26886 22 3.66619 20.4426 3.53959 18.0713L3.5061 5.16638" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Claim List
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>