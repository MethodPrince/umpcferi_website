document.addEventListener('DOMContentLoaded', function () {
    const slideshow = document.getElementById('hero-slideshow');
    if (!slideshow) {
        return;
    }

    const slides = slideshow.querySelectorAll('.hero-slide');
    if (!slides.length) {
        return;
    }

    let current = 0;
    setInterval(function () {
        slides[current].classList.remove('active');
        current = (current + 1) % slides.length;
        slides[current].classList.add('active');
    }, 5000);
});
