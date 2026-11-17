<x-school::layouts.admin title="Class Teachers" group="Classes">
    <div class="sms-page-header">
        <div>
            <h1 class="sms-page-title">Class Teachers</h1>
            <p class="sms-page-subtitle">Pick the teacher in charge of each section.</p>
        </div>
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
                <thead><tr><th>Class</th><th>Section</th><th>Class teacher</th></tr></thead>
                <tbody>
                    @forelse ($sections as $section)
                        <tr>
                            <td>{{ $section->classroom->name }}</td>
                            <td class="fw-medium">{{ $section->name }}</td>
                            <td>
                                <form action="{{ route('school.admin.class-teachers.update', $section->id) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="class_teacher_id" class="form-select form-select-sm" style="max-width:260px" aria-label="Class teacher for {{ $section->classroom->name }} {{ $section->name }}">
                                        <option value="">None</option>
                                        @foreach ($teachers as $t)
                                            <option value="{{ $t->id }}" @selected($section->class_teacher_id === $t->id)>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="sms-empty">Add sections first, then assign class teachers.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $sections->links('pagination::bootstrap-5') }}</div>
</x-school::layouts.admin>
