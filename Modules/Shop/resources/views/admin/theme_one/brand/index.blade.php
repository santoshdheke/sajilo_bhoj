@extends('shop::admin.theme_one.component.layout')
@section('title', 'Brands')

@section('content')
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" id="editForm" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Brand</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label fw-semibold">Name</label><input class="form-control" name="name" id="editName" required maxlength="255"></div>
                <div class="mb-3"><label class="form-label fw-semibold">Slug</label><input class="form-control" name="slug" id="editSlug" required maxlength="255" pattern="[a-z0-9-]+"></div>
                <div class="mb-3"><label class="form-label fw-semibold">New logo</label><input class="form-control" type="file" name="logo" accept="image/*"></div>
                <div class="form-check form-switch"><input type="hidden" name="status" value="0">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="editStatus"><label class="form-check-label" for="editStatus">Active</label></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
            </div>
        </form>
    </div>
</div>

<div class="page-title mb-4">
    <h2>Brands</h2>
    <p>Brands of the products you sell. Use "Local" for unbranded goods.</p>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-soft form-panel">
            <div class="card-header d-flex align-items-center gap-2"><i class="bi bi-award text-primary"></i><h5 class="mb-0">Add Brand</h5></div>
            <div class="card-body">
                <form action="{{ route('shop.admin.brand.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3"><label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input class="form-control" name="name" value="{{ old('name') }}" required maxlength="255" data-slug-source="#addSlug" placeholder="e.g. Samsung"></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                        <input class="form-control" name="slug" id="addSlug" value="{{ old('slug') }}" required maxlength="255" pattern="[a-z0-9-]+"></div>
                    <div class="mb-4"><label class="form-label fw-semibold">Logo</label>
                        <input class="form-control" type="file" name="logo" accept="image/*"></div>
                    <button class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Save Brand</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header"><h5 class="mb-0">Brand List <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $brands->count() }})</span></h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th style="width:56px;">Logo</th><th>Name</th><th class="text-center">Products</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($brands as $brand)
                                <tr>
                                    <td>@if ($brand->logo_url)<img class="thumb" src="{{ $brand->logo_url }}" alt="">@else<span class="thumb"><i class="bi bi-award"></i></span>@endif</td>
                                    <td><span class="fw-semibold">{{ $brand->name }}</span><div class="text-muted small">{{ $brand->slug }}</div></td>
                                    <td class="text-center fw-bold">{{ $brand->products_count }}</td>
                                    <td><span class="pill {{ $brand->status ? 'pill-green' : 'pill-gray' }}">{{ $brand->status ? 'Active' : 'Hidden' }}</span></td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="tbl-action edit" title="Edit" data-url="{{ route('shop.admin.brand.update', $brand) }}"
                                                data-name="{{ $brand->name }}" data-slug="{{ $brand->slug }}" data-status="{{ (int) $brand->status }}"><i class="bi bi-pencil"></i></button>
                                            <form action="{{ route('shop.admin.brand.destroy', $brand) }}" method="POST" data-confirm="Delete brand &quot;{{ $brand->name }}&quot;? Its products stay, without a brand.">
                                                @csrf @method('DELETE')<button class="tbl-action del" title="Delete"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-row"><i class="bi bi-award"></i>No brands yet.</td></tr>
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
        var d = btn.dataset;
        document.getElementById('editForm').action = d.url;
        document.getElementById('editName').value = d.name;
        document.getElementById('editSlug').value = d.slug;
        document.getElementById('editStatus').checked = d.status === '1';
        bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal')).show();
    });
});
</script>
@endpush
