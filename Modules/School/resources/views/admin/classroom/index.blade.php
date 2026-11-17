<x-school::layouts.admin title="Classes" group="Classes">
    <div class="sms-page-header">
        <div>
            <h1 class="sms-page-title">Classes</h1>
            <p class="sms-page-subtitle">Classes (grades) of this school.</p>
        </div>
        <a href="{{ route('school.admin.classrooms.create') }}" class="btn btn-primary"><span class="material-icons-outlined">add</span>Add Class</a>
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
                <thead><tr><th>Name</th><th>Sections</th><th>Order</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($classrooms as $classroom)
                        <tr>
                            <td class="fw-medium">{{ $classroom->name }}</td>
                            <td><a href="{{ route('school.admin.sections.index', ['classroom' => $classroom->id]) }}">{{ $classroom->sections_count }}</a></td>
                            <td>{{ $classroom->display_order }}</td>
                            <td><span class="sms-chip {{ $classroom->status->value === 'active' ? 'sms-chip--success' : '' }}">{{ $classroom->status->label() }}</span></td>
                            <td class="sms-table__actions">
                                <a href="{{ route('school.admin.classrooms.edit', $classroom->id) }}" class="sms-icon-btn" title="Edit" aria-label="Edit {{ $classroom->name }}"><span class="material-icons-outlined">edit</span></a>
                                <form action="{{ route('school.admin.classrooms.destroy', $classroom->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete {{ e($classroom->name) }}?')">     @csrf     @method('DELETE')     <button type="submit" class="sms-icon-btn sms-icon-btn--danger" title="Delete" aria-label="Delete {{ $classroom->name }}"><span class="material-icons-outlined">delete_outline</span></button> </form> 
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="sms-empty">Nothing here yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $classrooms->links('pagination::bootstrap-5') }}</div>
</x-school::layouts.admin>
