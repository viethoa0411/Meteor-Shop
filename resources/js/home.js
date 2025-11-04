// Home page specific JS
document.addEventListener('DOMContentLoaded', () => {
  // Hero slider
  const slides = document.querySelectorAll('.hero-slider .slide');
  const dots = document.querySelectorAll('.slider-dot');
  let idx = 0;
  const go = (i) => {
    slides.forEach(s => s.classList.remove('active'));
    dots.forEach(d => d.classList.remove('active'));
    idx = (i + slides.length) % slides.length;
    slides[idx]?.classList.add('active');
    dots[idx]?.classList.add('active');
  };
  let timer = null;
  const start = () => {
    if (slides.length <= 1) return;
    stop();
    timer = setInterval(() => go(idx + 1), 4000);
  };
  const stop = () => { if (timer) clearInterval(timer); };
  dots.forEach((d, i) => d.addEventListener('click', () => go(i)));
  const hero = document.querySelector('.hero-slider');
  hero?.addEventListener('mouseenter', stop);
  hero?.addEventListener('mouseleave', start);
  start();
});

document.addEventListener('DOMContentLoaded', function () {
  // Hero Slider
  const slider = document.querySelector('.hero-slider');
  if (!slider) return;

  const slides = slider.querySelectorAll('.slide');
  const dots = document.querySelectorAll('.slider-dot');
  let currentSlide = 0;
  const slideInterval = 4000; // 4s

  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === index);
      if (dots[i]) dots[i].classList.toggle('active', i === index);
    });
  }

  function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
  }

  let slideTimer = setInterval(nextSlide, slideInterval);

  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
      currentSlide = index;
      showSlide(currentSlide);
      clearInterval(slideTimer);
      slideTimer = setInterval(nextSlide, slideInterval);
    });
  });

  slider.addEventListener('mouseenter', () => clearInterval(slideTimer));
  slider.addEventListener('mouseleave', () => {
    slideTimer = setInterval(nextSlide, slideInterval);
  });
});
