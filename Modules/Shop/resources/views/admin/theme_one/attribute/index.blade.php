@extends('shop::admin.theme_one.component.layout')
@section('title', 'Attributes')

@php($types = \Modules\Shop\Models\Attribute::INPUT_TYPES)

@section('content')
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Attribute</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label fw-semibold">Title</label><input class="form-control" name="attribute_title" id="editTitle" required maxlength="255"></div>
                <div><label class="form-label fw-semibold">Input type</label>
                    <select class="form-select" name="input_type" id="editType">@foreach ($types as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
            </div>
        </form>
    </div>
</div>

<div class="page-title mb-4">
    <h2>Attributes</h2>
    <p>Product properties such as Color, Size or RAM. Attach them to categories.</p>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-soft form-panel">
            <div class="card-header d-flex align-items-center gap-2"><i class="bi bi-sliders text-primary"></i><h5 class="mb-0">Add Attribute</h5></div>
            <div class="card-body">
                <form action="{{ route('shop.admin.attribute.store') }}" method="POST">
                    @csrf
                    <div class="mb-3"><label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input class="form-control" name="attribute_title" value="{{ old('attribute_title') }}" required maxlength="255" placeholder="e.g. Weight"></div>
                    <div class="mb-4"><label class="form-label fw-semibold">Input type</label>
                        <select class="form-select" name="input_type">@foreach ($types as $k => $v)<option value="{{ $k }}" @selected(old('input_type', 'text') === $k)>{{ $v }}</option>@endforeach</select></div>
                    <button class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Save Attribute</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header"><h5 class="mb-0">Attribute List <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $attributes->count() }})</span></h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Title</th><th>Input type</th><th class="text-center">Categories</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($attributes as $attribute)
                                <tr>
                                    <td class="fw-semibold">{{ $attribute->attribute_title }}</td>
                                    <td><span class="pill pill-gray">{{ $types[$attribute->input_type] ?? $attribute->input_type }}</span></td>
                                    <td class="text-center fw-bold">{{ $attribute->categories_count }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="tbl-action edit" title="Edit" data-url="{{ route('shop.admin.attribute.update', $attribute) }}"
                                                data-title="{{ $attribute->attribute_title }}" data-type="{{ $attribute->input_type }}"><i class="bi bi-pencil"></i></button>
                                            <form action="{{ route('shop.admin.attribute.destroy', $attribute) }}" method="POST" data-confirm="Delete attribute &quot;{{ $attribute->attribute_title }}&quot;? Product values for it are removed too.">
                                                @csrf @method('DELETE')<button class="tbl-action del" title="Delete"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="empty-row"><i class="bi bi-sliders"></i>No attributes yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.querySelectorAll('.tbl-action.edit').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.getElementById('editForm').action = btn.dataset.url;
        document.getElementById('editTitle').value = btn.dataset.title;
        document.getElementById('editType').value = btn.dataset.type;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal')).show();
    });
});
</script>
@endpush
