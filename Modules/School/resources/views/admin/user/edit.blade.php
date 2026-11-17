<x-school::layouts.admin title="Edit user" group="Users">
    <div class="sms-page-header">
        <div>
            <h1 class="sms-page-title">Edit user</h1>
            <p class="sms-page-subtitle">{{ $user->name }}</p>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('school.admin.users.update', $user->id) }}" method="POST" class="sms-paper" style="max-width: 720px;">
        @csrf
        @method('PUT')
        <div class="sms-paper__body">
            @include('school::admin.user._form', ['user' => $user])
        </div>
        <div class="d-flex justify-content-end gap-2 px-4 py-3 border-top" style="border-color: var(--sms-border) !important;">
            <a href="{{ route('school.admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update user</button>
        </div>
    </form>
</x-school::layouts.admin>
