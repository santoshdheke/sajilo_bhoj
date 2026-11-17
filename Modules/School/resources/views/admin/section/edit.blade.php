<x-school::layouts.admin title="Edit Section" group="Classes">
    <div class="sms-page-header"><div><h1 class="sms-page-title">Edit Section</h1></div></div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <form action="{{ route('school.admin.sections.update', $section->id) }}" method="POST" class="sms-paper" style="max-width: 720px;">
        @csrf
        @method('PUT')
        <div class="sms-paper__body">@include('school::admin.section._form')</div>
        <div class="d-flex justify-content-end gap-2 px-4 py-3 border-top" style="border-color: var(--sms-border) !important;">
            <a href="{{ route('school.admin.sections.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
    </form>
</x-school::layouts.admin>
