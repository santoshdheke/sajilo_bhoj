{{-- ═══ SHARED ACCOUNT SECTION CSS ═══ --}}
<style>
/* ── Page wrapper ── */
.account-page { padding: 36px 0 72px; }

/* ── Layout ── */
.account-layout {
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 28px;
    align-items: start;
}
@media (max-width: 991px) {
    .account-layout { grid-template-columns: 1fr; }
}

/* ── Sidebar card ── */
.account-sidebar {
    background: #fff;
    border: 1px solid #e8eaed;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    position: sticky;
    top: 82px;
}

/* User header */
.acc-user-head {
    padding: 28px 20px 20px;
    background: linear-gradient(135deg, #{{ $brand['primary'] }}18 0%, #{{ $brand['secondary'] }}10 100%);
    border-bottom: 1px solid #e8eaed;
    text-align: center;
}
.acc-avatar {
    width: 72px; height: 72px; border-radius: 50%;
    background: linear-gradient(135deg, #{{ $brand['primary'] }}, #{{ $brand['secondary'] }});
    color: #fff; font-size: 26px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,.15);
    border: 3px solid #fff;
}
.acc-user-name {
    font-size: 16px; font-weight: 700; color: #111; margin-bottom: 3px;
}
.acc-user-email {
    font-size: 12px; color: #9ca3af; font-weight: 400;
}

/* Nav items */
.acc-nav { padding: 10px 0 14px; }
.acc-nav-item {
    display: flex; align-items: center; gap: 12px;
    padding: 11px 20px;
    font-size: 13.5px; font-weight: 500; color: #374151;
    text-decoration: none;
    transition: background .12s, color .12s;
    border-left: 3px solid transparent;
    position: relative;
}
.acc-nav-item:hover {
    background: #f8f9fb;
    color: #111;
    text-decoration: none;
    border-left-color: #e8eaed;
}
.acc-nav-item.active {
    background: #{{ $brand['primary'] }}12;
    color: #{{ $brand['primary'] }};
    font-weight: 700;
    border-left-color: #{{ $brand['primary'] }};
    text-decoration: none;
}
.acc-nav-item .nav-icon {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; flex-shrink: 0;
    background: #f3f4f6; color: #6b7280;
    transition: background .12s, color .12s;
}
.acc-nav-item.active .nav-icon {
    background: #{{ $brand['primary'] }}20;
    color: #{{ $brand['primary'] }};
}
.acc-nav-item:hover .nav-icon { background: #ebebeb; color: #374151; }
.acc-nav-divider { height: 1px; background: #f3f4f6; margin: 4px 0; }

/* Mobile nav toggle */
.acc-mobile-toggle {
    display: none;
    width: 100%; padding: 13px 18px;
    background: #fff; border: 1px solid #e8eaed;
    border-radius: 10px; cursor: pointer;
    font-size: 14px; font-weight: 600; color: #111;
    text-align: left; align-items: center; gap: 10px;
    margin-bottom: 16px; font-family: inherit;
}
@media (max-width: 991px) {
    .acc-mobile-toggle { display: flex; }
    .account-sidebar { position: static; }
    .acc-nav-collapse { display: none; }
    .acc-nav-collapse.open { display: block; }
}

/* ── Content card ── */
.account-content {
    background: #fff;
    border: 1px solid #e8eaed;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    min-height: 400px;
}
.acc-content-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid #e8eaed;
    background: #fafbfc;
}
.acc-content-title {
    font-size: 16px; font-weight: 800; color: #111;
    display: flex; align-items: center; gap: 10px; margin: 0;
}
.acc-content-title i {
    width: 34px; height: 34px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
    background: #{{ $brand['primary'] }}18; color: #{{ $brand['primary'] }};
}
.acc-content-body { padding: 24px; }

/* ── Status badges ── */
.order-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 700;
    padding: 4px 10px; border-radius: 20px;
    white-space: nowrap;
}
.badge-pending    { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.badge-processing { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
.badge-shipped    { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
.badge-delivered  { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.badge-cancelled  { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.badge-paid       { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.badge-unpaid     { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

/* ── Order row card ── */
.order-row {
    display: flex; align-items: center; gap: 16px;
    padding: 16px 0;
    border-bottom: 1px solid #f3f4f6;
    flex-wrap: wrap;
}
.order-row:last-child { border-bottom: none; }
.order-num {
    font-size: 14px; font-weight: 700; color: #111;
}
.order-meta { font-size: 12px; color: #9ca3af; margin-top: 2px; }

/* ── Empty state ── */
.acc-empty {
    text-align: center; padding: 64px 24px; color: #9ca3af;
}
.acc-empty-icon {
    width: 64px; height: 64px; border-radius: 16px;
    background: #f3f4f6;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; color: #d1d5db; margin: 0 auto 16px;
}
.acc-empty h4 { font-size: 17px; font-weight: 700; color: #374151; margin-bottom: 8px; }
.acc-empty p  { font-size: 13.5px; margin-bottom: 20px; }

/* ── Profile fields ── */
.pf-row {
    display: flex; align-items: center; gap: 16px;
    padding: 14px 0;
    border-bottom: 1px solid #f3f4f6;
}
.pf-row:last-child { border-bottom: none; }
.pf-icon {
    width: 36px; height: 36px; border-radius: 9px;
    background: #{{ $brand['primary'] }}15;
    color: #{{ $brand['primary'] }};
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
}
.pf-label { font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: .06em; }
.pf-value { font-size: 14px; font-weight: 600; color: #111; margin-top: 1px; }

/* ── Stat cards ── */
.acc-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 28px;
}
@media (max-width: 575px) { .acc-stats { grid-template-columns: 1fr; } }
.acc-stat-card {
    background: #fff;
    border: 1px solid #e8eaed;
    border-radius: 12px;
    padding: 18px 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
    text-align: center;
    transition: box-shadow .15s, transform .15s;
    text-decoration: none; display: block; color: inherit;
}
.acc-stat-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,.09);
    transform: translateY(-2px); color: inherit; text-decoration: none;
}
.acc-stat-card::before {
    content: ''; display: block; height: 3px;
    border-radius: 12px 12px 0 0;
    margin: -18px -20px 16px;
}
.stat-blue::before   { background: linear-gradient(90deg,#3b82f6,#60a5fa); }
.stat-green::before  { background: linear-gradient(90deg,#10b981,#34d399); }
.stat-red::before    { background: linear-gradient(90deg,#ef4444,#f87171); }
.acc-stat-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; margin: 0 auto 12px;
}
.si-blue  { background: #eff6ff; color: #3b82f6; }
.si-green { background: #ecfdf5; color: #10b981; }
.si-red   { background: #fef2f2; color: #ef4444; }
.acc-stat-value { font-size: 28px; font-weight: 800; color: #111; line-height: 1; margin-bottom: 4px; }
.acc-stat-label { font-size: 12px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: .06em; }
</style>

{{-- ═══ SIDEBAR HTML ═══ --}}
@php $user = auth('shop_customer')->user(); @endphp

<div class="account-sidebar">

    {{-- User header --}}
    <div class="acc-user-head">
        <div class="acc-avatar">
            {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
        </div>
        <div class="acc-user-name">{{ $user?->name ?? 'My Account' }}</div>
        <div class="acc-user-email">{{ $user?->email ?? '' }}</div>
    </div>

    {{-- Mobile toggle --}}
    <button class="acc-mobile-toggle" id="accNavToggle">
        <i class="fas fa-bars" style="color:#{{ $brand['primary'] }};"></i>
        Account Menu
        <i class="fas fa-chevron-down" style="font-size:11px;color:#9ca3af;margin-left:auto;"></i>
    </button>

    {{-- Nav --}}
    <nav class="acc-nav acc-nav-collapse" id="accNavMenu">
        <a href="{{ route('shop.front.account.dashboard') }}"
           class="acc-nav-item {{ request()->is('user/dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-home"></i></span>
            Dashboard
        </a>

        <a href="{{ route('shop.front.account.profile') }}"
           class="acc-nav-item {{ request()->is('user/profile') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-user"></i></span>
            My Profile
        </a>

        <div class="acc-nav-divider"></div>

        <a href="{{ route('shop.front.account.orders', ['order-status' => 'pending']) }}"
           class="acc-nav-item {{ request()->is('user/order') && request('order-status') == 'pending' ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-clock"></i></span>
            Pending Orders
        </a>

        <a href="{{ route('shop.front.account.orders', ['order-status' => 'delivered']) }}"
           class="acc-nav-item {{ request()->is('user/order') && request('order-status') == 'delivered' ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-check-circle"></i></span>
            Purchase History
        </a>

        <a href="{{ route('shop.front.account.orders', ['order-status' => 'cancelled']) }}"
           class="acc-nav-item {{ request()->is('user/order') && request('order-status') == 'cancelled' ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-times-circle"></i></span>
            Cancelled Orders
        </a>

        <div class="acc-nav-divider"></div>

        <form action="{{ route('shop.front.logout') }}" method="post">
            @csrf
            <button type="submit"
                    style="display:flex;align-items:center;gap:12px;padding:11px 20px;width:100%;background:none;border:none;border-left:3px solid transparent;font-size:13.5px;font-weight:500;color:#ef4444;cursor:pointer;font-family:inherit;transition:background .12s;"
                    onmouseover="this.style.background='#fff5f5'" onmouseout="this.style.background='none'">
                <span class="nav-icon" style="background:#fef2f2;color:#ef4444;"><i class="fas fa-sign-out-alt"></i></span>
                Logout
            </button>
        </form>
    </nav>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('accNavToggle');
    var menu   = document.getElementById('accNavMenu');
    if (toggle && menu) {
        toggle.addEventListener('click', function () {
            menu.classList.toggle('open');
        });
    }
});
</script>
