@php
    $roleLabel = $presetRole ? \Modules\School\Enums\UserRoleEnum::tryFrom($presetRole)?->label() : null;
    $pageTitle = $roleLabel ? "Add {$roleLabel}" : 'Add user';
@endphp
<x-school::layouts.admin :title="$pageTitle" group="Users">
    <div class="sms-page-header">
        <div>
            <h1 class="sms-page-title">{{ $roleLabel ? "Add {$roleLabel}" : 'Add user' }}</h1>
            <p class="sms-page-subtitle">They'll sign in on this school's domain.</p>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('school.admin.users.store') }}" method="POST" class="sms-paper" style="max-width: 720px;">
        @csrf
        <div class="sms-paper__body">
            @include('school::admin.user._form')
        </div>
        <div class="d-flex justify-content-end gap-2 px-4 py-3 border-top" style="border-color: var(--sms-border) !important;">
            <a href="{{ route('school.admin.users.index', array_filter(['role' => $presetRole])) }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Add user</button>
        </div>
    </form>
</x-school::layouts.admin>
