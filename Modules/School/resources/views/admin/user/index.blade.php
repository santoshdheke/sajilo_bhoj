<x-school::layouts.admin :title="$roleLabel ?? 'All Users'" group="Users">
    <div class="sms-page-header">
        <div>
            <h1 class="sms-page-title">{{ $roleLabel ?? 'Users' }}</h1>
            <p class="sms-page-subtitle">
                @if ($roleLabel)
                    {{ $roleLabel }}s of this school.
                @else
                    Admins, teachers and students of this school.
                @endif
            </p>
        </div>
        <a href="{{ route('school.admin.users.create', array_filter(['role' => $role])) }}" class="btn btn-primary">
            <span class="material-icons-outlined">add</span>Add {{ $roleLabel ?? 'user' }}
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="sms-paper">
        <div class="sms-table-wrap">
            <table class="sms-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Added</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="fw-medium">{{ $user->name }}@if (auth()->id() === $user->id) <span class="text-muted fw-normal">(you)</span>@endif</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="sms-chip {{ $user->role->value === 'admin' ? 'sms-chip--success' : '' }}">{{ $user->role->label() }}</span></td>
                            <td class="text-nowrap">{{ $user->created_at?->format('M j, Y') }}</td>
                            <td class="sms-table__actions">
                                <a href="{{ route('school.admin.users.edit', $user->id) }}" class="sms-icon-btn" title="Edit" aria-label="Edit {{ $user->name }}">
                                    <span class="material-icons-outlined">edit</span>
                                </a>
                                @if (auth()->id() !== $user->id)
                                    <form action="{{ route('school.admin.users.destroy', $user->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete {{ e($user->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sms-icon-btn sms-icon-btn--danger" title="Delete" aria-label="Delete {{ $user->name }}">
                                            <span class="material-icons-outlined">delete_outline</span>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="sms-empty">No {{ $roleLabel ? strtolower($roleLabel).'s' : 'users' }} yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $users->links('pagination::bootstrap-5') }}</div>
</x-school::layouts.admin>
