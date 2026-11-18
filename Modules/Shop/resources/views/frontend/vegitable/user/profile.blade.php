@extends($theme_path . 'common.layout.master_layout')
@section('hide_flash', 1)

@section('contant')
@php $user = auth('shop_customer')->user(); @endphp

<div class="eh-breadcrumb-bar">
    <div class="container">
        <h1 class="mb-1">My Profile</h1>
        <nav><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('shop.front.home') }}">Home</a></li>
            <li class="breadcrumb-item active">My Profile</li>
        </ol></nav>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">

        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="eh-summary-card text-center mb-3">
                <div style="width:72px;height:72px;border-radius:50%;background:var(--primary-light);color:var(--primary);font-size:28px;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>
                <div style="font-size:16px;font-weight:700;">{{ $user->name }}</div>
                <div style="font-size:13px;color:var(--muted);">{{ $user->email }}</div>
            </div>
            <div class="eh-sidebar-box">
                <a href="{{ route('shop.front.account.profile') }}" class="eh-mobile-menu-link" style="{{ request()->routeIs('user.profile.index') ? 'background:var(--primary-light);color:var(--primary);' : '' }}">
                    <i class="bi bi-person"></i> My Profile
                </a>
                <a href="{{ route('shop.front.account.orders', ['order-status' => 'pending']) }}" class="eh-mobile-menu-link">
                    <i class="bi bi-box-seam"></i> My Orders
                </a>
                <a href="#" onclick="event.preventDefault();document.getElementById('veg-logout-form').submit();" class="eh-mobile-menu-link">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>

        <!-- Profile form -->
        <div class="col-lg-9">

            <div class="eh-summary-card">
                <h5 style="font-size:14px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;padding-bottom:12px;border-bottom:2px solid var(--primary);margin-bottom:20px;">
                    <i class="bi bi-person-fill" style="color:var(--primary);"></i> Personal Information
                </h5>
                <form action="{{ route('shop.front.account.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-sm-6 eh-form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" value="{{ $user->first_name ?? '' }}">
                        </div>
                        <div class="col-sm-6 eh-form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="{{ $user->last_name ?? '' }}">
                        </div>
                        <div class="col-sm-6 eh-form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                        </div>
                        <div class="col-sm-6 eh-form-group">
                            <label>Phone</label>
                            <input type="text" name="phone_number" class="form-control" value="{{ $user->phone_number ?? '' }}">
                        </div>
                        <div class="col-12 eh-form-group">
                            <label>Address</label>
                            <input type="text" name="address" class="form-control" value="{{ $user->address ?? '' }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="eh-btn-primary">
                                <i class="bi bi-check-lg"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
