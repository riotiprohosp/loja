$(function () {
  const key = 'PROHOSP_cart_count';
  let count = Number(localStorage.getItem(key) || 0);
  $('.cart-count').text(count);

  const mainNav = $('.main-nav');
  const mobileToggle = $('.mobile-toggle');

  mobileToggle.attr('aria-expanded', 'false');
  mobileToggle.attr('aria-controls', 'main-nav');

  mobileToggle.on('click', function () {
    mainNav.toggleClass('open');
    const expanded = mainNav.hasClass('open') ? 'true' : 'false';
    mobileToggle.attr('aria-expanded', expanded);
  });

  function normalizePath(path) {
    if (path === '' || path === '/') {
      return '/index.php';
    }
    if (path.endsWith('/')) {
      return path + 'index.php';
    }
    return path;
  }

  function markActiveMenu() {
    const current = new URL(location.href, location.origin);
    const currentPath = normalizePath(current.pathname);
    $('.main-nav .nav-link').removeClass('active');
    $('.main-nav .nav-link').each(function () {
      if ($(this).attr('href') === '#') {
        return;
      }
      const link = new URL(this.href, location.origin);
      const linkPath = normalizePath(link.pathname);
      if (linkPath !== currentPath) {
        return;
      }
      if (link.search === current.search && link.hash === current.hash) {
        $(this).addClass('active');
        return;
      }
      if (!link.search && !link.hash && !current.search && !current.hash) {
        $(this).addClass('active');
      }
    });

    $('.main-nav .has-submenu').each(function () {
      const parent = $(this);
      parent.find('.submenu a').each(function () {
        if ($(this).attr('href') === '#') {
          return;
        }
        const link = new URL(this.href, location.origin);
        const linkPath = normalizePath(link.pathname);
        if (linkPath === currentPath && link.search === current.search && link.hash === current.hash) {
          parent.find('> .nav-link').addClass('active');
        }
      });
    });
  }

  markActiveMenu();
  window.addEventListener('hashchange', markActiveMenu);

  $('.main-nav .has-submenu > .nav-link[href="#"]').on('click', function (event) {
    event.preventDefault();
  });

  $('.add-cart').on('click', function () {
    count += 1;
    localStorage.setItem(key, count);
    $('.cart-count').text(count);
    showToast('Produto adicionado ao carrinho.');
  });

  $('.favorite').on('click', function () {
    $(this).toggleClass('active').text($(this).hasClass('active') ? '♥' : '♡');
  });

  function showToast(message) {
    let toast = $('.toast');
    if (!toast.length) {
      toast = $('<div class="toast"></div>').appendTo('body');
    }
    toast.text(message).addClass('show');
    setTimeout(() => toast.removeClass('show'), 1800);
  }
});
