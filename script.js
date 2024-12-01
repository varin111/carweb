document.addEventListener("DOMContentLoaded", function() {
    const slides = document.querySelectorAll(".slide");
    let currentSlideIndex = 0;

    // Function to change the slide
    function changeSlide() {
        const slideWidth = slides[0].offsetWidth;
        document.querySelector(".slides").style.transform = `translateX(-${currentSlideIndex * slideWidth}px)`;
    }

    // Previous button functionality
    document.getElementById("prev-btn").addEventListener("click", function() {
        currentSlideIndex = (currentSlideIndex === 0) ? slides.length - 1 : currentSlideIndex - 1;
        changeSlide();
    });

    // Next button functionality
    document.getElementById("next-btn").addEventListener("click", function() {
        currentSlideIndex = (currentSlideIndex === slides.length - 1) ? 0 : currentSlideIndex + 1;
        changeSlide();
    });

    // Automatic background change (slideshow)
    setInterval(function() {
        currentSlideIndex = (currentSlideIndex === slides.length - 1) ? 0 : currentSlideIndex + 1;
        changeSlide();
    }, 5000); // Change every 5 seconds
});
