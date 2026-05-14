$(function () {
  function closeAdminMenu() {
    $('.admin-sidebar').removeClass('open');
    $('#adminOverlay').removeClass('show');
  }

  $('.admin-menu-toggle').on('click', function () {
    $('.admin-sidebar').toggleClass('open');
    $('#adminOverlay').toggleClass('show');
  });

  $('#sidebarCollapse').on('click', function () {
    const sidebar = $('.admin-sidebar');
    const collapsed = sidebar.toggleClass('collapsed').hasClass('collapsed');
    $('.admin-shell').toggleClass('collapsed');
    if (collapsed) {
      $('.admin-nav-group').removeClass('open').find('.nav-group-toggle').attr('aria-expanded', 'false');
    }
  });

  $('.nav-group-toggle').on('click', function () {
    const group = $(this).closest('.admin-nav-group');
    const isOpen = group.hasClass('open');

    // Close all other groups to enforce accordion behavior
    $('.admin-nav-group').not(group).removeClass('open').find('.nav-group-toggle').attr('aria-expanded', 'false');

    if (isOpen) {
      group.removeClass('open');
      $(this).attr('aria-expanded', 'false');
    } else {
      group.addClass('open');
      $(this).attr('aria-expanded', 'true');
    }
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
