<?php
require_once __DIR__ . '/config/front/header.php';
?>
<main class="main">
    <section id="hero" class="hero section dark-background" >
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="zoom-out">
                    <h1>Welcome To <?= SITE_NAME ?> !</h1>
                    <p>We provide affordable and comprehensive car insurance plans tailored to your needs.</p>
                    <div class="d-flex gap-2">
                        <a href="#contact" class="btn btn-ghost-blue text-white border border-blue">Get a Quote</a>
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out" data-aos-delay="200">
                    <img src="<?= SITE_URL ?>/assets/images/logo.png" class="animated bg-white rounded-circle"
                        style="width: 100%; height: 100%;"
                        alt="">
                </div>
            </div>
        </div>
    </section>
    <section id="about" class="about section">
        <div class="container section-title" data-aos="fade-up">
            <h2>About Us</h2>
        </div>
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
                    <h2 data-aos="fade-up" data-aos-delay="100">About Us - KurdCarInsurance</h2>
                    <p>
                        Welcome to KurdCarInsurance, your trusted partner for all your car insurance needs in Kurdistan. We are proud to be a leading provider of reliable, affordable, and comprehensive car insurance solutions, designed with our community in mind. At KurdCarInsurance, we strive to ensure that every driver has access to the protection and support they deserve, allowing them to drive with confidence and peace of mind.
                    </p>
                    <div class="why-us">
                        <div class="faq-container" data-aos="fade-up" data-aos-delay="200">
                            <div class="faq-item faq-active">
                                <h3><span>01</span> Our Mission</h3>
                                <div class="faq-content">
                                    <p>
                                        At KurdCarInsurance, our mission is simple yet powerful: to make car insurance accessible, affordable, and effective for everyone. We aim to provide policies that not only meet but exceed the expectations of our customers. Whether you’re seeking basic liability coverage or comprehensive protection, our goal is to ensure you and your vehicle are safeguarded at all times.
                                        We are deeply committed to promoting road safety and financial security through our insurance services. By prioritizing transparency and trust, we aspire to build long-term relationships with our customers while contributing to a safer and more insured driving community across Kurdistan.
                                    </p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div>
                            <div class="faq-item">
                                <h3><span>02</span> Our Services</h3>
                                <div class="faq-content">
                                    <p>
                                        KurdCarInsurance offers a wide range of insurance options tailored to suit your specific needs. We understand that no two drivers are the same, which is why we provide flexible plans and policies. Our core services include: <br>
                                        - Comprehensive Car Insurance <br>
                                        - Third-Party Liability Insurance <br>
                                        - Fleet Insurance for Businesses <br>
                                        - Efficient Claims Process <br>
                                        - 24/7 Customer Support <br>
                                    </p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                    <h2>
                        Why Choose KurdCarInsurance?
                    </h2>
                    <p>
                        At KurdCarInsurance, we are more than just an insurance provider—we are your partner on the road. Our promise is to provide you with dependable coverage, fast and fair claims processing, and outstanding customer service. We understand the importance of protecting your vehicle and your financial well-being, which is why we go above and beyond to meet your needs.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <section id="services" class="services section light-background">
        <div class="container section-title" data-aos="fade-up">
            <h2>Services</h2>
        </div>
        <div class="container">
            <div class="row gy-4">
                <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-item position-relative">
                        <h4>
                            <a href="#" class="fs-2 stretched-link">
                                Comprehensive Coverage
                            </a>
                        </h4>
                        <p>
                            Our comprehensive car insurance plans provide you with all-round protection, covering damage to your vehicle, third-party liability, and more.
                        </p>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-item position-relative">
                        <h4>
                            <a href="#" class="fs-2 stretched-link">
                                Third-Party Insurance
                            </a>
                        </h4>
                        <p>
                            Our third-party liability insurance is designed to protect you from financial loss in the event of an accident involving your vehicle.
                        </p>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-item position-relative">
                        <h4>
                            <a href="#" class="fs-2 stretched-link">
                                Fleet Insurance
                            </a>
                        </h4>
                        <p>
                            Our fleet insurance plans are ideal for businesses with multiple vehicles, offering cost-effective coverage and streamlined claims processing.
                        </p>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="400">
                    <div class="service-item position-relative">
                        <h4>
                            <a href="#" class="fs-2 stretched-link">
                                24/7 Support
                            </a>
                        </h4>
                        <p>
                            Our dedicated customer support team is available around the clock to assist you with any queries, claims, or emergencies.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- <section class="section dark-background"
        style="padding: 20px 0;"></section> -->
    <section id="team" class="team section dark-background">
        <div class="container section-title" data-aos="fade-up">
            <h2>Team</h2>
        </div>
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="team-member d-flex align-items-start">
                        <div class="pic"><img src="<?= SITE_URL ?>assets/images/team/team-1.jpg" class="img-fluid" alt=""></div>
                        <div class="member-info">
                            <h4>Walter White</h4>
                            <span>Chief Executive Officer</span>
                            <p>Explicabo voluptatem mollitia et repellat qui dolorum quasi</p>
                            <div class="social">
                                <a href="#"><i class="bi bi-twitter-x"></i></a>
                                <a href="#"><i class="bi bi-facebook"></i></a>
                                <a href="#"><i class="bi bi-instagram"></i></a>
                                <a href="#"> <i class="bi bi-linkedin"></i> </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="team-member d-flex align-items-start">
                        <div class="pic"><img src="<?= SITE_URL ?>assets/images/team/team-2.jpg" class="img-fluid" alt=""></div>
                        <div class="member-info">
                            <h4>Sarah Jhonson</h4>
                            <span>Product Manager</span>
                            <p>Aut maiores voluptates amet et quis praesentium qui senda para</p>
                            <div class="social">
                                <a href=""><i class="bi bi-twitter-x"></i></a>
                                <a href=""><i class="bi bi-facebook"></i></a>
                                <a href=""><i class="bi bi-instagram"></i></a>
                                <a href=""> <i class="bi bi-linkedin"></i> </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="team-member d-flex align-items-start">
                        <div class="pic"><img src="<?= SITE_URL ?>assets/images/team/team-3.jpg" class="img-fluid" alt=""></div>
                        <div class="member-info">
                            <h4>William Anderson</h4>
                            <span>CTO</span>
                            <p>Quisquam facilis cum velit laborum corrupti fuga rerum quia</p>
                            <div class="social">
                                <a href=""><i class="bi bi-twitter-x"></i></a>
                                <a href=""><i class="bi bi-facebook"></i></a>
                                <a href=""><i class="bi bi-instagram"></i></a>
                                <a href=""> <i class="bi bi-linkedin"></i> </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="team-member d-flex align-items-start">
                        <div class="pic"><img src="<?= SITE_URL ?>assets/images/team/team-4.jpg" class="img-fluid" alt=""></div>
                        <div class="member-info">
                            <h4>Amanda Jepson</h4>
                            <span>Accountant</span>
                            <p>Dolorum tempora officiis odit laborum officiis et et accusamus</p>
                            <div class="social">
                                <a href=""><i class="bi bi-twitter-x"></i></a>
                                <a href=""><i class="bi bi-facebook"></i></a>
                                <a href=""><i class="bi bi-instagram"></i></a>
                                <a href=""> <i class="bi bi-linkedin"></i> </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="contact" class="contact section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Contact</h2>
        </div>
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row gy-4">
                <div class="col-lg-5">
                    <div class="info-wrap">
                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
                            <i class="bi bi-geo-alt flex-shrink-0"></i>
                            <div>
                                <h3>Address</h3>
                                <p>
                                    <?= SITE_ADDRESS; ?>
                                </p>
                            </div>
                        </div>
                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                            <i class="bi bi-telephone flex-shrink-0"></i>
                            <div>
                                <h3>Call Us</h3>
                                <p>
                                    <?= SITE_PHONE; ?>
                                </p>
                            </div>
                        </div>
                        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                            <i class="bi bi-envelope flex-shrink-0"></i>
                            <div>
                                <h3>Email Us</h3>
                                <p>
                                    <?= SITE_EMAIL; ?>
                                </p>
                            </div>
                        </div>
                        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d48389.78314118045!2d-74.006138!3d40.710059!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a22a3bda30d%3A0xb89d1fe6bc499443!2sDowntown%20Conference%20Center!5e0!3m2!1sen!2sus!4v1676961268712!5m2!1sen!2sus" frameborder="0" style="border:0; width: 100%; height: 270px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
                <div class="col-lg-7">
                    <?php require_once __DIR__ . '/config/front/contact-form.php'; ?>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
require_once  __DIR__ . '/config/front/footer.php';
