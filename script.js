// Client-side only: theme toggle, password show/hide, lightbox, auth modal tabs.
document.addEventListener('DOMContentLoaded', function () {
  var root = document.documentElement;

  // 1. Theme toggle (remembered in localStorage)
  var themeBtn = document.getElementById('theme-toggle');
  if (themeBtn) {
    themeBtn.addEventListener('click', function () {
      var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      root.setAttribute('data-theme', next);
      try { localStorage.setItem('theme', next); } catch (e) {}
    });
  }

  // 2. Password show/hide
  document.querySelectorAll('.toggle-pass').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = btn.parentElement.querySelector('input');
      input.type = input.type === 'password' ? 'text' : 'password';
    });
  });

  // 3. Lightbox: open pre-rendered <dialog>
  document.querySelectorAll('[data-lightbox]').forEach(function (tile) {
    tile.addEventListener('click', function () {
      var dlg = document.getElementById(tile.getAttribute('data-lightbox'));
      if (dlg) dlg.showModal();
    });
  });

  // Close buttons + click on backdrop for every dialog
  document.querySelectorAll('dialog').forEach(function (dlg) {
    dlg.addEventListener('click', function (ev) { if (ev.target === dlg) dlg.close(); });
    var x = dlg.querySelector('[data-close]');
    if (x) x.addEventListener('click', function () { dlg.close(); });
  });

  // Re-open a meme popup after liking/disliking (URL ends with #meme-<id>)
  if (location.hash.indexOf('#meme-') === 0) {
    var target = document.getElementById(location.hash.slice(1));
    if (target && target.showModal) target.showModal();
  }

  // 4. Auth modal tabs
  var auth = document.getElementById('auth-dialog');
  if (auth) {
    function showTab(name) {
      auth.querySelectorAll('.tab').forEach(function (t) {
        t.classList.toggle('active', t.getAttribute('data-tab') === name);
      });
      auth.querySelectorAll('.auth-panel').forEach(function (p) {
        p.hidden = p.getAttribute('data-panel') !== name;
      });
    }
    auth.querySelectorAll('.tab').forEach(function (t) {
      t.addEventListener('click', function () { showTab(t.getAttribute('data-tab')); });
    });
    var hint = document.getElementById('auth-hint');
    // Like/dislike buttons for logged-out visitors: close the popup, ask them to log in
    document.querySelectorAll('[data-login-required]').forEach(function (b) {
      b.addEventListener('click', function () {
        var open = b.closest('dialog');
        if (open) open.close();
        showTab('login');
        if (hint) hint.hidden = false;
        auth.showModal();
      });
    });
    var openBtn = document.querySelector('[data-open-auth-btn]');
    if (openBtn) openBtn.addEventListener('click', function () { showTab('login'); if (hint) hint.hidden = true; auth.showModal(); });

    // Re-open with the right tab after a failed login/signup
    var pending = document.body.getAttribute('data-open-auth');
    if (pending) { showTab(pending); auth.showModal(); } else { showTab('login'); }
  }
});
