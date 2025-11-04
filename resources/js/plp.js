document.addEventListener('DOMContentLoaded', () => {
  // Submit on change for radio/price inputs after short debounce
  const form = document.getElementById('filterForm');
  if (!form) return;

  let timer = null;
  const autoSubmit = () => {
    clearTimeout(timer);
    timer = setTimeout(() => form.requestSubmit(), 250);
  };

  form.querySelectorAll('input, select').forEach((el) => {
    if (el.name === 'price_min' || el.name === 'price_max' || el.name === 'q') {
      el.addEventListener('input', autoSubmit);
    } else {
      el.addEventListener('change', autoSubmit);
    }
  });

  // Mobile drawer
  const openBtn = document.getElementById('openFilter');
  const drawer = document.getElementById('filterDrawer');
  const closeBtn = document.getElementById('closeFilter');
  const formMobile = document.getElementById('filterFormMobile');
  const open = () => drawer?.classList.remove('hidden');
  const close = () => drawer?.classList.add('hidden');
  openBtn?.addEventListener('click', open);
  closeBtn?.addEventListener('click', close);
  drawer?.addEventListener('click', (e) => { if (e.target === drawer) close(); });

  // Keep query on sort/view bar already via hidden inputs in template
});


