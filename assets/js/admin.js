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

  $('.admin-sub-toggle').on('click', function () {
    const sub = $(this).closest('.admin-nav-sub');
    const isOpen = sub.toggleClass('open').hasClass('open');
    $(this).attr('aria-expanded', isOpen ? 'true' : 'false');
  });

  function openAdminModal(modal) {
    modal.addClass('show').attr('aria-hidden', 'false');
    $('body').addClass('modal-open');
  }

  function closeAdminModal(modal) {
    modal.removeClass('show').attr('aria-hidden', 'true');
    $('body').removeClass('modal-open');
  }

  $('.open-product-modal').on('click', function () {
    openAdminModal($('#productModal'));
  });

  $('.admin-modal .admin-modal-backdrop, .admin-modal .modal-close').on('click', function () {
    closeAdminModal($(this).closest('.admin-modal'));
  });

  $('[data-auto-open="true"]').each(function () {
    openAdminModal($(this));
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

  $('.variable-toggle').on('click', function () {
    const picker = $(this).closest('.variable-picker');
    const isOpen = picker.toggleClass('open').hasClass('open');
    $(this).attr('aria-expanded', isOpen ? 'true' : 'false');
  });

  $('.variable-list button').on('click', function () {
    const variable = $(this).data('variable');
    const textarea = $('#mensagemPadrao').get(0);
    if (!textarea || !variable) return;

    const start = textarea.selectionStart ?? textarea.value.length;
    const end = textarea.selectionEnd ?? textarea.value.length;
    textarea.value = textarea.value.slice(0, start) + variable + textarea.value.slice(end);
    const nextPosition = start + variable.length;
    textarea.focus();
    textarea.setSelectionRange(nextPosition, nextPosition);
    $(this).closest('.variable-picker').removeClass('open').find('.variable-toggle').attr('aria-expanded', 'false');
  });

  $(document).on('click', function (event) {
    if ($(event.target).closest('.variable-picker').length) return;
    $('.variable-picker').removeClass('open').find('.variable-toggle').attr('aria-expanded', 'false');
  });

  // Promoção: habilita/desabilita o campo de valor promocional
  $(document).on('change', 'input[name="promocao_ativa"]', function () {
    const form = $(this).closest('form');
    const promoInput = form.find('input[name="promocao"]');
    if ($(this).is(':checked')) {
      promoInput.prop('disabled', false);
    } else {
      promoInput.prop('disabled', true).val('0.00');
    }
  });
});
