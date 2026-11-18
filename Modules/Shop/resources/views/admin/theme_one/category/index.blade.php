@extends('shop::admin.theme_one.component.layout')
@section('title', 'Categories')

@section('content')
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label fw-semibold">Title</label><input class="form-control" name="title" id="editTitle" required maxlength="255"></div>
                <div class="mb-3"><label class="form-label fw-semibold">Slug</label><input class="form-control" name="slug" id="editSlug" required maxlength="255" pattern="[a-z0-9-]+"></div>
                <div class="mb-3"><label class="form-label fw-semibold">Parent</label>
                    <select class="form-select js-select2" name="parent_id" id="editParent" data-placeholder="Choose a parent">
                        <option value="0">No parent (root)</option>
                        @foreach ($parentOptions as $id => $label)<option value="{{ $id }}">{{ $label }}</option>@endforeach
                    </select></div>
                <div><label class="form-label fw-semibold">Attributes</label>
                    <select class="form-select js-select2" name="attribute_ids[]" id="editAttributes" multiple data-placeholder="Pick attributes">
                        @foreach ($attributes as $id => $title)<option value="{{ $id }}">{{ $title }}</option>@endforeach
                    </select>
                    <div class="form-text">Products in this category can have these attributes. Type to search; pick as many as you need.</div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
            </div>
        </form>
    </div>
</div>

<div class="page-title mb-4">
    <h2>Categories</h2>
    <p>Group your products. A category can have sub-categories, up to three levels.</p>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-soft form-panel">
            <div class="card-header d-flex align-items-center gap-2"><i class="bi bi-diagram-3 text-primary"></i><h5 class="mb-0">Add Category</h5></div>
            <div class="card-body">
                <form action="{{ route('shop.admin.category.store') }}" method="POST">
                    @csrf
                    <div class="mb-3"><label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input class="form-control" name="title" value="{{ old('title') }}" required maxlength="255" data-slug-source="#addSlug" placeholder="e.g. Groceries"></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                        <input class="form-control" name="slug" id="addSlug" value="{{ old('slug') }}" required maxlength="255" pattern="[a-z0-9-]+" placeholder="groceries"></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Parent</label>
                        <select class="form-select js-select2" name="parent_id" data-placeholder="Choose a parent">
                            <option value="0">No parent (root)</option>
                            @foreach ($parentOptions as $id => $label)<option value="{{ $id }}" @selected(old('parent_id') == $id)>{{ $label }}</option>@endforeach
                        </select></div>
                    <div class="mb-4"><label class="form-label fw-semibold">Attributes</label>
                        <select class="form-select js-select2" name="attribute_ids[]" multiple data-placeholder="Pick attributes">
                            @foreach ($attributes as $id => $title)<option value="{{ $id }}" @selected(in_array($id, (array) old('attribute_ids', [])))>{{ $title }}</option>@endforeach
                        </select></div>
                    <button class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Save Category</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header"><h5 class="mb-0">Category List <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $categories->count() }})</span></h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Title</th><th>Parent</th><th>Attributes</th><th class="text-center">Products</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td><span class="fw-semibold">{{ $category->title }}</span><div class="text-muted small">{{ $category->slug }}</div></td>
                                    <td class="text-muted">{{ $category->parent_name }}</td>
                                    <td>@foreach ($category->attributes as $a)<span class="pill pill-gray me-1">{{ $a->attribute_title }}</span>@endforeach</td>
                                    <td class="text-center fw-bold">{{ $category->products_count }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="tbl-action edit" title="Edit"
                                                data-url="{{ route('shop.admin.category.update', $category) }}" data-title="{{ $category->title }}" data-slug="{{ $category->slug }}"
                                                data-parent="{{ $category->parent_id }}" data-attributes="{{ $category->attributes->pluck('id')->toJson() }}"><i class="bi bi-pencil"></i></button>
                                            <form action="{{ route('shop.admin.category.destroy', $category) }}" method="POST" data-confirm="Delete category &quot;{{ $category->title }}&quot;?">
                                                @csrf @method('DELETE')<button class="tbl-action del" title="Delete"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-row"><i class="bi bi-diagram-3"></i>No categories yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function () {
    // Inside a Bootstrap modal the dropdown must attach to the modal, or it opens behind it and can't be typed into.
    $('.js-select2').each(function () {
        var $el = $(this), $modal = $el.closest('.modal');
        $el.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: $el.data('placeholder'),
            closeOnSelect: !$el.prop('multiple'),
            dropdownParent: $modal.length ? $modal : $(document.body)
        });
    });

    $('.tbl-action.edit').on('click', function () {
        var d = this.dataset, attrs = JSON.parse(d.attributes || '[]').map(String);
        $('#editForm').attr('action', d.url);
        $('#editTitle').val(d.title);
        $('#editSlug').val(d.slug);
        $('#editParent').val(d.parent || '0').trigger('change');
        $('#editAttributes').val(attrs).trigger('change');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal')).show();
    });
});
</script>
@endpush
