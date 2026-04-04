// ==============================
// slider.js — Hero Image Slider
// ==============================

(function () {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.slider-dot');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    if (!slides.length) return;

    let current = 0;
    let autoplay = null;
    const INTERVAL = 5000;

    function goTo(index) {
        slides[current].classList.remove('active');
        dots[current]?.classList.remove('active');
        current = (index + slides.length) % slides.length;
        slides[current].classList.add('active');
        dots[current]?.classList.add('active');
    }

    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }

    function startAutoplay() {
        autoplay = setInterval(next, INTERVAL);
    }
    function resetAutoplay() {
        clearInterval(autoplay);
        startAutoplay();
    }

    if (prevBtn) prevBtn.addEventListener('click', () => { prev(); resetAutoplay(); });
    if (nextBtn) nextBtn.addEventListener('click', () => { next(); resetAutoplay(); });

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => { goTo(i); resetAutoplay(); });
    });

    // Touch / swipe support
    let touchStartX = 0;
    const sliderEl = document.querySelector('.hero-slider');
    if (sliderEl) {
        sliderEl.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
        sliderEl.addEventListener('touchend', e => {
            const diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) {
                diff > 0 ? next() : prev();
                resetAutoplay();
            }
        });
    }

    goTo(0);
    startAutoplay();
})();
