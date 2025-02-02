<?php if ($_SERVER['PHP_SELF'] !== '/login.php' && $_SERVER['PHP_SELF'] !== '/signup.php') : ?>
    <footer id="footer" class="footer w-full">
        <div class="container footer-top">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6 footer-about">
                    <a href="index.html" class="d-flex align-items-center gap-2">
                        <img src="<?= SITE_URL ?>/assets/images/logo.png" class="border bg-white rounded-circle p-1"
                            style="width: 80px; height: 80px;"
                            alt="">
                        <span style="font-size: 1.5rem; font-weight: 600;text-wrap: wrap;">
                            <?= SITE_NAME; ?>
                        </span>
                    </a>
                    <div class="footer-contact pt-3">
                        <p>
                            <?= SITE_ADDRESS; ?>
                        </p>
                        <p>
                            <strong>Phone:</strong>
                            <span>
                                <?= SITE_PHONE; ?>
                            </span>
                        </p>
                        <p>
                            <strong>Email:</strong>
                            <span>
                                <?= SITE_EMAIL; ?>
                            </span>
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-3 footer-links">
                    <h4>Useful Links</h4>
                    <ul>
                        <li><i class="bi bi-chevron-right"></i> <a href="<?= SITE_URL; ?>">Home</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#about">About us</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#services">Services</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#team">Team</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-12">
                    <h4>Follow Us</h4>
                    <p>
                        Let us be social and follow us on social media to keep in touch with us.
                    </p>
                    <div class="social-links d-flex">
                        <a href=""><i class="bi bi-twitter-x"></i></a>
                        <a href=""><i class="bi bi-facebook"></i></a>
                        <a href=""><i class="bi bi-instagram"></i></a>
                        <a href=""><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container copyright text-center mt-4">
            <p>
                Copyright ©
                <?= date('Y'); ?>
                <a href="." class="link-secondary">
                    <?= SITE_NAME; ?>
                </a>.
                All rights reserved.
            </p>
        </div>
    </footer>
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <div id="preloader"></div>
<?php endif; ?>
</body>

</html>
<?php
ob_end_flush();
?>