$(function () {
  function closeAdminMenu() {
    $('.admin-sidebar').removeClass('open');
    $('#adminOverlay').removeClass('show');
  }

  $('.admin-menu-toggle').on('click', function () {
    $('.admin-sidebar').toggleClass('open');
    $('#adminOverlay').toggleClass('show');
  });

  $('#adminOverlay').on('click', closeAdminMenu);

  $('.admin-nav a').on('click', function () {
    if ($(window).width() <= 960) closeAdminMenu();
  });

  $('input[name="cep_inicio"], input[name="cep_fim"]').on('input', function () {
    let v = $(this).val().replace(/\D/g, '').slice(0, 8);
    if (v.length > 5) v = v.slice(0, 5) + '-' + v.slice(5);
    $(this).val(v);
  });

  $('table').on('click', 'button[name="excluir"], button[name="excluir_menu"]', function () {
    return confirm('Confirma a exclusão?');
  });
});
