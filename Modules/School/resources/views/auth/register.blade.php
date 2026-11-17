<x-school::layouts.auth title="Register your school" :wide="true">
    <span class="sms-drawer__logo sms-auth__logo">{{ strtoupper(mb_substr($domain, 0, 1)) }}</span>
    <h1 class="sms-auth__title">Register your school</h1>
    <p class="sms-auth__subtitle">
        <strong>{{ $domain }}</strong> isn't registered yet. Sign up to claim it for your school — you'll be its first administrator.
    </p>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('school.register.store') }}" method="POST" novalidate>
        @csrf

        <div class="sms-nav__label px-0 pt-0">School</div>
        <div class="row g-3 mb-4">
            <div class="col-12">
                <label for="school_name" class="form-label">School name</label>
                <input type="text" id="school_name" name="school_name" class="form-control @error('school_name') is-invalid @enderror"
                    value="{{ old('school_name') }}" required autofocus>
                @error('school_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-5">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-7">
                <label for="address" class="form-label">Address</label>
                <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}">
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="sms-nav__label px-0 pt-0">Administrator</div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="admin_name" class="form-label">Your name</label>
                <input type="text" id="admin_name" name="admin_name" class="form-control @error('admin_name') is-invalid @enderror"
                    value="{{ old('admin_name') }}" required autocomplete="name">
                @error('admin_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="admin_email" class="form-label">Email</label>
                <input type="email" id="admin_email" name="admin_email" class="form-control @error('admin_email') is-invalid @enderror"
                    value="{{ old('admin_email') }}" required autocomplete="username">
                @error('admin_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    required autocomplete="new-password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Confirm password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required autocomplete="new-password">
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">Register school</button>
    </form>
</x-school::layouts.auth>
