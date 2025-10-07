// Script tambahan (bisa diisi efek interaktif nanti)
console.log("Dashboard Lokasi Kesehatan siap!");

const links = document.querySelectorAll('.sidebar-link');
  const currentUrl = window.location.href;

  links.forEach(link => {
    if (currentUrl.includes(link.getAttribute('href'))) {
      link.classList.add('active');
      link.style.setProperty('--active-color', link.dataset.color);
    }
  });

  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');
  const toggleBtn = document.getElementById('toggleSidebar');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
    overlay.classList.toggle('active');
  });

  overlay.addEventListener('click', () => {
    sidebar.classList.remove('show');
    overlay.classList.remove('active');
  });