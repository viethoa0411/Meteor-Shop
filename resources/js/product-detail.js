document.addEventListener('DOMContentLoaded', function () {
  // Gallery thumbnails
  const mainImage = document.getElementById('pd-main-image');
  const thumbs = document.querySelectorAll('.thumb');
  thumbs.forEach((btn) => {
    btn.addEventListener('click', () => {
      thumbs.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const src = btn.getAttribute('data-src');
      if (src && mainImage) mainImage.src = src;
    });
  });

  // Tabs
  const tabLinks = document.querySelectorAll('.tab-link');
  const panes = document.querySelectorAll('.tab-pane');
  tabLinks.forEach((link) => {
    link.addEventListener('click', () => {
      const target = link.getAttribute('data-target');
      tabLinks.forEach(l => l.classList.remove('active'));
      panes.forEach(p => p.classList.remove('active'));
      link.classList.add('active');
      const pane = target ? document.querySelector(target) : null;
      if (pane) pane.classList.add('active');
    });
  });

  // Variant selections
  const colorDots = document.querySelectorAll('.color-dot');
  colorDots.forEach(dot => {
    dot.addEventListener('click', () => {
      colorDots.forEach(d => d.classList.remove('active'));
      dot.classList.add('active');
    });
  });
  const sizePills = document.querySelectorAll('.size-pill');
  sizePills.forEach(pill => {
    pill.addEventListener('click', () => {
      sizePills.forEach(p => p.classList.remove('active'));
      pill.classList.add('active');
    });
  });

  // Quantity controls (desktop)
  document.querySelectorAll('[data-qty]')?.forEach(btn => {
    btn.addEventListener('click', () => {
      const step = parseInt(btn.getAttribute('data-qty') || '1', 10);
      const input = btn.parentElement?.querySelector('input[type="number"]');
      if (!input) return;
      const min = parseInt(input.getAttribute('min') || '1', 10);
      let val = parseInt(input.value || '1', 10) + step;
      if (val < min) val = min;
      input.value = String(val);
    });
  });
});
