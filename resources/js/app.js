import './bootstrap';

// Global search suggest (desktop)
document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('globalSearch');
  const box = document.getElementById('searchSuggest');
  const content = document.getElementById('suggestContent');
  if (!input || !box || !content) return;

  let timer = null;
  const endpoint = '/users/api/search/suggest';

  const hide = () => box.classList.add('hidden');
  const show = () => box.classList.remove('hidden');

  const render = (data) => {
    const items = [];
    if (data.categories?.length) {
      items.push(`<div class="text-[12px] text-gray-500 mt-1">Danh mục</div>`);
      data.categories.forEach(c => {
        items.push(`<a class="block py-1 text-sm hover:text-blue-600" href="/users/products?category=${c.slug}">${c.name}</a>`);
      });
    }
    if (data.brands?.length) {
      items.push(`<div class="text-[12px] text-gray-500 mt-2">Thương hiệu</div>`);
      data.brands.forEach(b => {
        items.push(`<a class="block py-1 text-sm hover:text-blue-600" href="/users/products?brand=${b.slug}">${b.name}</a>`);
      });
    }
    if (data.products?.length) {
      items.push(`<div class="text-[12px] text-gray-500 mt-2">Sản phẩm</div>`);
      data.products.forEach(p => {
        const img = p.image ? `/storage/${p.image}` : 'https://via.placeholder.com/60x60?text=No+Image';
        items.push(`<a class="flex items-center gap-3 py-1" href="/users/product/${p.slug}">
          <img src="${img}" class="w-10 h-10 rounded object-cover"/>
          <span class="text-sm text-gray-800">${p.name}</span>
          <span class="ml-auto text-[12px] text-red-600 font-semibold">${(p.price||0).toLocaleString('vi-VN')} đ</span>
        </a>`);
      });
    }
    content.innerHTML = items.join('') || '<div class="text-sm text-gray-500">Không có gợi ý.</div>';
    show();
  }

  input.addEventListener('input', () => {
    const q = input.value.trim();
    if (q === '') { hide(); return; }
    clearTimeout(timer);
    timer = setTimeout(async () => {
      try {
        const res = await fetch(`${endpoint}?q=${encodeURIComponent(q)}`);
        const data = await res.json();
        render(data);
      } catch (e) {
        hide();
      }
    }, 200);
  });

  document.addEventListener('click', (e) => {
    if (!box.contains(e.target) && e.target !== input) hide();
  });
});

// Mega menu hover intent (desktop)
document.addEventListener('DOMContentLoaded', () => {
  const groups = document.querySelectorAll('header li.group');
  groups.forEach((el) => {
    let hideTimer;
    el.addEventListener('mouseenter', () => {
      clearTimeout(hideTimer);
      el.classList.add('is-open');
    });
    el.addEventListener('mouseleave', () => {
      hideTimer = setTimeout(() => el.classList.remove('is-open'), 120);
    });
  });
});
