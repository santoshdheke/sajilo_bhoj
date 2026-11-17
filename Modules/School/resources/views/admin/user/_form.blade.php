@php
    $user = $user ?? null;
    $editing = $user !== null;
    $currentRole = old('role', $user?->role?->value ?? $presetRole ?? 'teacher');
    $isSelf = $editing && auth()->id() === $user->id;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Name</label>
        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user?->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user?->email) }}" required autocomplete="off">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="role" class="form-label">Role</label>
        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" @disabled($isSelf)>
            @foreach (\Modules\School\Enums\UserRoleEnum::cases() as $role)
                <option value="{{ $role->value }}" @selected($currentRole === $role->value)>{{ $role->label() }}</option>
            @endforeach
        </select>
        @if ($isSelf)
            <div class="form-text">You can't change your own role.</div>
        @endif
        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
            @required(! $editing) autocomplete="new-password">
        <div class="form-text">{{ $editing ? 'Leave blank to keep the current password.' : 'At least 8 characters.' }}</div>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
