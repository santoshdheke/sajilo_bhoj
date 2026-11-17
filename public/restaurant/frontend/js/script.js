$(function () {

  /* ── Fade-up on scroll ── */
  const onScroll = function () {
    $('.fade-up').each(function () {
      if (this.getBoundingClientRect().top < window.innerHeight - 60) {
        $(this).addClass('show');
      }
    });
  };
  onScroll();
  $(window).on('scroll', onScroll);

  /* ── Menu category filter (menu page) ── */
  $(document).on('click', '.menu-filter .btn', function () {
    const filter = $(this).data('filter');
    $('.menu-filter .btn').removeClass('active');
    $(this).addClass('active');
    if (filter === 'all') {
      $('.menu-item-card').show();
    } else {
      $('.menu-item-card').hide();
      $('.menu-item-card[data-category="' + filter + '"]').show();
    }
  });

  /* ── Menu ordering filter (table detail page) ── */
  $(document).on('click', '.menu-order-filter .btn', function () {
    const f = $(this).data('ofilter');
    $('.menu-order-filter .btn').removeClass('active');
    $(this).addClass('active');
    if (f === 'all') {
      $('.order-item-col').show();
    } else {
      $('.order-item-col').hide();
      $('.order-item-col[data-ocategory="' + f + '"]').show();
    }
  });

  /* ── Forms ── */
  $('#bookingForm').on('submit', function (e) {
    e.preventDefault();
    alert('Your booking request has been sent successfully.');
    this.reset();
  });
  $('#contactForm').on('submit', function (e) {
    e.preventDefault();
    alert('Thanks for contacting SajiloBhoj. We will reach out soon.');
    this.reset();
  });

  /* ── Close sidebar/cart on Escape ── */
  $(document).on('keydown', function (e) {
    if (e.key === 'Escape') {
      if (typeof closeSidebar === 'function') closeSidebar();
      if (typeof closeCartModal === 'function') closeCartModal();
    }
  });

});
