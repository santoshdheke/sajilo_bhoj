@extends('shop::admin.theme_one.component.layout')
@section('title', 'Banners')

@section('content')
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" id="editForm" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit banner</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body row g-3">
                <div class="col-12"><label class="form-label fw-semibold">Heading</label><input class="form-control" name="title" id="eTitle" maxlength="255"></div>
                <div class="col-12"><label class="form-label fw-semibold">Text</label><input class="form-control" name="sub_title" id="eSub" maxlength="255"></div>
                <div class="col-8"><label class="form-label fw-semibold">Link</label><input class="form-control" name="url" id="eUrl" maxlength="255" placeholder="/store or https://…"></div>
                <div class="col-4"><label class="form-label fw-semibold">Order</label><input class="form-control" type="number" min="0" name="sort_order" id="eSort"></div>
                <div class="col-12"><label class="form-label fw-semibold">New image</label><input class="form-control" type="file" name="image" accept="image/*"></div>
                <div class="col-12"><div class="form-check form-switch"><input type="hidden" name="status" value="0"><input class="form-check-input" type="checkbox" name="status" value="1" id="eStatus"><label class="form-check-label" for="eStatus">Show on the storefront</label></div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
            </div>
        </form>
    </div>
</div>

<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>Banners</h2><p>Slides at the top of your storefront home page. Without banners, sample slides are shown.</p></div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-soft form-panel">
            <div class="card-header d-flex align-items-center gap-2"><i class="bi bi-images text-primary"></i><h5 class="mb-0">Add banner</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('shop.admin.banner.store') }}" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    <div class="col-12"><label class="form-label fw-semibold">Image <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" name="image" accept="image/*" required><div class="form-text">Wide image, e.g. 1600 × 600.</div></div>
                    <div class="col-12"><label class="form-label fw-semibold">Heading</label><input class="form-control" name="title" value="{{ old('title') }}" maxlength="255" placeholder="Timeless Beauty"></div>
                    <div class="col-12"><label class="form-label fw-semibold">Text</label><input class="form-control" name="sub_title" value="{{ old('sub_title') }}" maxlength="255"></div>
                    <div class="col-8"><label class="form-label fw-semibold">Link</label><input class="form-control" name="url" value="{{ old('url') }}" maxlength="255" placeholder="/store"></div>
                    <div class="col-4"><label class="form-label fw-semibold">Order</label><input class="form-control" type="number" min="0" name="sort_order" value="{{ old('sort_order', 0) }}"></div>
                    <div class="col-12"><button class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Save banner</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header"><h5 class="mb-0">Banners <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $banners->count() }})</span></h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th style="width:140px;">Image</th><th>Heading</th><th class="text-center">Order</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($banners as $banner)
                                <tr>
                                    <td><img src="{{ $banner->image_path }}" alt="" style="width:120px;height:52px;object-fit:cover;border-radius:10px;"></td>
                                    <td><span class="fw-semibold">{{ $banner->title ?: '—' }}</span><div class="text-muted small">{{ $banner->sub_title }}</div></td>
                                    <td class="text-center">{{ $banner->sort_order }}</td>
                                    <td><span class="pill {{ $banner->status ? 'pill-green' : 'pill-gray' }}">{{ $banner->status ? 'Shown' : 'Hidden' }}</span></td>
                                    <td><div class="d-flex gap-1">
                                        <button type="button" class="tbl-action edit" data-url="{{ route('shop.admin.banner.update', $banner) }}" data-title="{{ $banner->title }}" data-sub="{{ $banner->sub_title }}"
                                            data-link="{{ $banner->url }}" data-sort="{{ $banner->sort_order }}" data-status="{{ (int) $banner->status }}" title="Edit"><i class="bi bi-pencil"></i></button>
                                        <form method="POST" action="{{ route('shop.admin.banner.destroy', $banner) }}" data-confirm="Delete this banner?">@csrf @method('DELETE')<button class="tbl-action del" title="Delete"><i class="bi bi-trash"></i></button></form>
                                    </div></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-row"><i class="bi bi-images"></i>No banners yet.</td></tr>
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
        document.getElementById('eTitle').value = d.title || '';
        document.getElementById('eSub').value = d.sub || '';
        document.getElementById('eUrl').value = d.link || '';
        document.getElementById('eSort').value = d.sort || 0;
        document.getElementById('eStatus').checked = d.status === '1';
        bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal')).show();
    });
});
</script>
@endpush
