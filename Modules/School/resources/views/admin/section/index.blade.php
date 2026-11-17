<x-school::layouts.admin title="Sections" group="Classes">
    <div class="sms-page-header">
        <div>
            <h1 class="sms-page-title">Sections</h1>
            <p class="sms-page-subtitle">Sections within each class.</p>
        </div>
        <a href="{{ route('school.admin.sections.create') }}" class="btn btn-primary"><span class="material-icons-outlined">add</span>Add Section</a>
    </div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
<form method="GET" action="{{ route('school.admin.sections.index') }}" class="mb-3 d-flex gap-2" style="max-width:360px">
        <select name="classroom" class="form-select" aria-label="Filter by class" onchange="this.form.submit()">
            <option value="">All classes</option>
            @foreach ($classrooms as $c)<option value="{{ $c->id }}" @selected($classroomId === $c->id)>{{ $c->name }}</option>@endforeach
        </select>
    </form>
    <div class="sms-paper">
        <div class="sms-table-wrap">
            <table class="sms-table">
                <thead><tr><th>Class</th><th>Section</th><th>Capacity</th><th>Class teacher</th><th></th></tr></thead>
                <tbody>
                    @forelse ($sections as $section)
                        <tr>
                            <td>{{ $section->classroom->name }}</td>
                            <td class="fw-medium">{{ $section->name }}</td>
                            <td>{{ $section->capacity ?? '—' }}</td>
                            <td>{{ $section->classTeacher?->name ?? '—' }}</td>
                            <td class="sms-table__actions">
                                <a href="{{ route('school.admin.sections.edit', $section->id) }}" class="sms-icon-btn" title="Edit" aria-label="Edit {{ $section->name }}"><span class="material-icons-outlined">edit</span></a>
                                <form action="{{ route('school.admin.sections.destroy', $section->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete {{ e($section->name) }}?')">     @csrf     @method('DELETE')     <button type="submit" class="sms-icon-btn sms-icon-btn--danger" title="Delete" aria-label="Delete {{ $section->name }}"><span class="material-icons-outlined">delete_outline</span></button> </form> 
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="sms-empty">Nothing here yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $sections->links('pagination::bootstrap-5') }}</div>
</x-school::layouts.admin>
