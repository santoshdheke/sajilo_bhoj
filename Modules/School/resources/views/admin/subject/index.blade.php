<x-school::layouts.admin title="Subjects" group="Classes">
    <div class="sms-page-header">
        <div>
            <h1 class="sms-page-title">Subjects</h1>
            <p class="sms-page-subtitle">Subjects taught at this school and the classes they belong to.</p>
        </div>
        <a href="{{ route('school.admin.subjects.create') }}" class="btn btn-primary"><span class="material-icons-outlined">add</span>Add Subject</a>
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
                <thead><tr><th>Name</th><th>Code</th><th>Type</th><th>Classes</th><th></th></tr></thead>
                <tbody>
                    @forelse ($subjects as $subject)
                        <tr>
                            <td class="fw-medium">{{ $subject->name }}</td>
                            <td>{{ $subject->code ?? '—' }}</td>
                            <td><span class="sms-chip">{{ $subject->type->label() }}</span></td>
                            <td>{{ $subject->classrooms->pluck('name')->join(', ') ?: '—' }}</td>
                            <td class="sms-table__actions">
                                <a href="{{ route('school.admin.subjects.edit', $subject->id) }}" class="sms-icon-btn" title="Edit" aria-label="Edit {{ $subject->name }}"><span class="material-icons-outlined">edit</span></a>
                                <form action="{{ route('school.admin.subjects.destroy', $subject->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete {{ e($subject->name) }}?')">     @csrf     @method('DELETE')     <button type="submit" class="sms-icon-btn sms-icon-btn--danger" title="Delete" aria-label="Delete {{ $subject->name }}"><span class="material-icons-outlined">delete_outline</span></button> </form> 
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="sms-empty">Nothing here yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $subjects->links('pagination::bootstrap-5') }}</div>
</x-school::layouts.admin>
