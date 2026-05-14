$(function () {
  const key = 'PROHOSP_cart_count';
  let count = Number(localStorage.getItem(key) || 0);
  $('.cart-count').text(count);

  $('.mobile-toggle').on('click', function () {
    $('.main-nav').toggleClass('open');
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
