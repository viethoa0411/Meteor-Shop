/**
 * Banner Carousel JavaScript
 * Handles all carousel functionality including autoplay, navigation, and touch support
 */
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('bannerCarousel');
    if (!carousel) return;

    const slides = carousel.querySelectorAll('.banner-slide');
    const indicators = document.querySelectorAll('.banner-indicator');
    const prevBtn = document.querySelector('.banner-prev');
    const nextBtn = document.querySelector('.banner-next');
    const progressBar = document.querySelector('.banner-progress-bar');
    
    let currentIndex = 0;
    let autoplayInterval;
    let progressInterval;
    const autoplayDelay = 5000; // 5 giây
    let isPaused = false;

    // Hàm hiển thị slide
    function showSlide(index) {
        // Xóa active từ tất cả slides và indicators
        slides.forEach(slide => slide.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('active'));
        
        // Thêm active cho slide và indicator hiện tại
        if (slides[index]) {
            slides[index].classList.add('active');
        }
        if (indicators[index]) {
            indicators[index].classList.add('active');
        }
        
        currentIndex = index;
        resetProgress();
    }

    // Hàm chuyển slide tiếp theo
    function nextSlide() {
        const nextIndex = (currentIndex + 1) % slides.length;
        showSlide(nextIndex);
    }

    // Hàm chuyển slide trước
    function prevSlide() {
        const prevIndex = (currentIndex - 1 + slides.length) % slides.length;
        showSlide(prevIndex);
    }

    // Hàm reset progress bar
    function resetProgress() {
        if (progressBar) {
            progressBar.style.width = '0%';
            progressBar.style.transition = 'none';
            setTimeout(() => {
                progressBar.style.transition = `width ${autoplayDelay}ms linear`;
                progressBar.style.width = '100%';
            }, 50);
        }
    }

    // Hàm bắt đầu autoplay
    function startAutoplay() {
        if (isPaused) return;
        
        clearInterval(autoplayInterval);
        autoplayInterval = setInterval(() => {
            if (!isPaused) {
                nextSlide();
            }
        }, autoplayDelay);
        
        resetProgress();
    }

    // Hàm dừng autoplay
    function stopAutoplay() {
        clearInterval(autoplayInterval);
        clearInterval(progressInterval);
        if (progressBar) {
            progressBar.style.width = '0%';
        }
    }

    // Event listeners cho navigation buttons
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            startAutoplay();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            startAutoplay();
        });
    }

    // Event listeners cho indicators
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            showSlide(index);
            startAutoplay();
        });
    });

    // Pause on hover
    carousel.addEventListener('mouseenter', () => {
        isPaused = true;
        stopAutoplay();
    });

    carousel.addEventListener('mouseleave', () => {
        isPaused = false;
        startAutoplay();
    });

    // Touch/swipe support cho mobile
    let touchStartX = 0;
    let touchEndX = 0;

    carousel.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    });

    carousel.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });

    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                nextSlide();
            } else {
                prevSlide();
            }
            startAutoplay();
        }
    }

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            prevSlide();
            startAutoplay();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
            startAutoplay();
        }
    });

    // Khởi động autoplay
    startAutoplay();

    // Cleanup khi component unmount
    window.addEventListener('beforeunload', () => {
        stopAutoplay();
    });
});

