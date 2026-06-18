
$(function(){
  $('.menu-toggle').on('click', function(e){e.preventDefault();$(this).closest('.has-sub').toggleClass('open');});
  $('.mobile-menu-btn').on('click', function(){$('.sidebar').toggleClass('show');});
  $('.sidebar .nav-link').each(function(){const href=$(this).attr('href');const page=window.location.pathname.split('/').pop()||'dashboard.html';if(href===page){$(this).addClass('active');$(this).closest('.submenu').show();$(this).closest('.has-sub').addClass('open');}});
  $('[data-demo-action="add-item"]').on('click', function(){const row=`<tr><td>ITM-${Math.floor(Math.random()*900+100)}</td><td>Dine In</td><td>T-07</td><td>Walk-in</td><td>$18.00</td><td><span class="status-badge status-pending">New</span></td></tr>`;$('#demo-order-items').prepend(row);});
});
