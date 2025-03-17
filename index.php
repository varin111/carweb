<?php
require_once __DIR__ . '/config/front/header.php';
?>
<main class="main">
    <section id="hero" class="hero section dark-background">
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
                        <div class="pic"><img src="<?= SITE_URL ?>assets/images/team/hama.jpg" class="img-fluid" alt=""></div>
                        <div class="member-info">
                            <h4>Muhammed Ahmed</h4>
                            <span>Founder & CEO</span>
                            <p>With over 10 years in the insurance industry, Muhammed leads our company with a vision for affordable and reliable car insurance solutions.</p>
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
                        <div class="pic"><img src="<?= SITE_URL ?>assets/images/team/nur.jpg" class="img-fluid" alt=""></div>
                        <div class="member-info">
                            <h4>Nuralhuda Nabil</h4>
                            <span>Head of Customer Relations</span>
                            <p>Dedicated to exceptional customer service, Nuralhuda ensures every client gets the support and guidance they need.</p>
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
                        <div class="pic"><img src="<?= SITE_URL ?>assets/images/team/varinn.jpg" class="img-fluid" alt=""></div>
                        <div class="member-info">
                            <h4>Varin Kamil</h4>
                            <span>Underwriting Manager</span>
                            <p>Varin assesses risk and creates tailored policies that fit our clients’ needs and budgets.</p>
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
                        <div class="pic"><img src="<?= SITE_URL ?>assets/images/team/lana.jpg" class="img-fluid" alt=""></div>
                        <div class="member-info">
                            <h4>Lana Hameed</h4>
                            <span> Senior Claims Specialist</span>
                            <p>Lana makes the claims process smooth and stress-free, helping clients get back on the road quickly.</p>
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
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3219.553010919305!2d43.96920137547042!3d36.20175127242381!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x400723862d9c2b5f%3A0xa9a44163b9df376c!2sEmpire%20World!5e0!3m2!1sen!2siq!4v1739049167121!5m2!1sen!2siq" frameborder="0" style="border:0; width: 100%; height: 270px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
                <div class="col-lg-7">
                    <?php require_once __DIR__ . '/config/front/contact-form.php'; ?>
                </div>
            </div>
        </div>
    </section>
    <!-- chat -->
    <div>
        <div id="chat-icon">
            <span>💬</span>
        </div>
        
        <div id="chat-window">
            <div id="chat-header">
                Kurd Car Insurance Assistant
                <span id="close-chat">×</span>
            </div>
            <div id="messages"></div>
            <div id="chat-input">
                <input type="text" id="input-box" placeholder="Type your message..." />
                <button id="send-btn">Send</button>
            </div>
        </div>
    </div>
</main>
<?php
require_once  __DIR__ . '/config/front/footer.php';
