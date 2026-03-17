/**
 * Header UI — chargé une seule fois via importmap (head).
 * turbo:load se déclenche sur chaque navigation Turbo sans accumulation.
 */

/* ── Dark mode : appliqué immédiatement au chargement initial ── */
(function () {
  var t = localStorage.getItem('theme');
  if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
  }
}());

document.addEventListener('turbo:load', function () {

  /* ── Guard : header absent sur cette page (ex: login) ── */
  var darkBtn = document.getElementById('dark-toggle');
  if (!darkBtn) return;

  var moonIcon = document.getElementById('dark-icon-moon');
  var sunIcon  = document.getElementById('dark-icon-sun');

  /* ── Dark mode ──────────────────────────────────────────────── */
  function syncDarkIcons() {
    var isDark = document.documentElement.classList.contains('dark');
    if (moonIcon) moonIcon.classList.toggle('hidden', isDark);
    if (sunIcon)  sunIcon.classList.toggle('hidden', !isDark);
    darkBtn.setAttribute('aria-label', isDark ? 'Passer en mode clair' : 'Passer en mode sombre');
  }
  syncDarkIcons();

  darkBtn.addEventListener('click', function () {
    var isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    syncDarkIcons();
  });

  /* ── Éléments DOM (mode connecté) ──────────────────────────── */
  var userBtn       = document.getElementById('user-menu-btn');
  var userDropdown  = document.getElementById('user-menu-dropdown');
  var notifBtn      = document.getElementById('notif-btn');
  var notifDropdown = document.getElementById('notif-dropdown');
  var searchToggle  = document.getElementById('search-toggle');
  var searchBox     = document.getElementById('search-box');
  var searchInput   = document.getElementById('search-input');
  var hamburger     = document.getElementById('hamburger-btn');
  var mobileMenu    = document.getElementById('mobile-menu');
  var hamburgerIcon = document.getElementById('hamburger-icon');
  var closeIcon     = document.getElementById('close-icon');
  var badge         = document.getElementById('notif-badge');

  if (!userBtn) return; // utilisateur non connecté : pas de menu

  /* ── Utilitaires dropdown ───────────────────────────────────── */
  function toggleDropdown(btn, dropdown) {
    var isHidden = dropdown.classList.contains('hidden');
    dropdown.classList.toggle('hidden', !isHidden);
    btn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
  }

  function closeDropdown(btn, dropdown) {
    dropdown.classList.add('hidden');
    btn.setAttribute('aria-expanded', 'false');
  }

  /* ── Recherche ──────────────────────────────────────────────── */
  function openSearchBox() {
    closeDropdown(userBtn, userDropdown);
    closeDropdown(notifBtn, notifDropdown);
    searchBox.classList.remove('hidden');
    searchToggle.setAttribute('aria-expanded', 'true');
    setTimeout(function () { searchInput.focus(); }, 50);
  }

  function closeSearchBox() {
    searchBox.classList.add('hidden');
    searchToggle.setAttribute('aria-expanded', 'false');
    searchInput.value = '';
  }

  /* ── Listeners sur éléments (nœuds remplacés à chaque nav) ─── */
  userBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    closeDropdown(notifBtn, notifDropdown);
    closeSearchBox();
    toggleDropdown(userBtn, userDropdown);
  });

  notifBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    closeDropdown(userBtn, userDropdown);
    closeSearchBox();
    toggleDropdown(notifBtn, notifDropdown);
  });

  searchToggle.addEventListener('click', function (e) {
    e.stopPropagation();
    searchBox.classList.contains('hidden') ? openSearchBox() : closeSearchBox();
  });

  searchBox.addEventListener('click', function (e) { e.stopPropagation(); });

  hamburger.addEventListener('click', function (e) {
    e.stopPropagation();
    var willBeVisible = mobileMenu.classList.contains('hidden');
    mobileMenu.classList.toggle('hidden');
    hamburgerIcon.classList.toggle('hidden', willBeVisible);
    closeIcon.classList.toggle('hidden', !willBeVisible);
    hamburger.setAttribute('aria-expanded', willBeVisible ? 'true' : 'false');
  });

  /* ── Listeners sur document (ajoutés via AbortController) ──── */
  // On crée un AbortController pour annuler les listeners précédents
  // avant d'en ajouter de nouveaux sur chaque turbo:load
  if (window._headerAbortController) {
    window._headerAbortController.abort();
  }
  var ctrl = new AbortController();
  window._headerAbortController = ctrl;
  var sig = { signal: ctrl.signal };

  document.addEventListener('click', function () {
    closeDropdown(userBtn, userDropdown);
    closeDropdown(notifBtn, notifDropdown);
    closeSearchBox();
  }, sig);

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      closeDropdown(userBtn, userDropdown);
      closeDropdown(notifBtn, notifDropdown);
      closeSearchBox();
      if (!mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.add('hidden');
        hamburgerIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
        hamburger.setAttribute('aria-expanded', 'false');
      }
    }
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
      e.preventDefault();
      searchBox.classList.contains('hidden') ? openSearchBox() : closeSearchBox();
    }
  }, sig);

  /* ── Marquer toutes les notifs comme lues ───────────────────── */
  var markAllReadBtn = document.getElementById('notif-mark-all-read');
  if (markAllReadBtn) {
    markAllReadBtn.addEventListener('click', function () {
      notifDropdown.querySelectorAll('.bg-blue-500.rounded-full').forEach(function (dot) {
        dot.classList.add('hidden');
      });
      if (badge) { badge.textContent = '0'; badge.classList.add('hidden'); }
    });
  }

  if (badge && badge.textContent.trim() === '0') {
    badge.classList.add('hidden');
  }
});
