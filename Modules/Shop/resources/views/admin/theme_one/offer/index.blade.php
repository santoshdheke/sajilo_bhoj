@extends('shop::admin.theme_one.component.layout')
@section('title', 'Special offers')

@section('content')
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" id="editForm" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit offer</h5>
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
    <div class="page-title mb-0"><h2>Special offers</h2><p>Offer slides beside the home banner in the "Market" store design.</p></div>
    <a class="btn btn-outline-primary" href="{{ route('shop.front.home') }}" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>View store</a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-soft form-panel">
            <div class="card-header d-flex align-items-center gap-2"><i class="bi bi-gift text-primary"></i><h5 class="mb-0">Add offer</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('shop.admin.offer.store') }}" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    <div class="col-12"><label class="form-label fw-semibold">Image <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" name="image" accept="image/*" required><div class="form-text">Portrait or square image works best.</div></div>
                    <div class="col-12"><label class="form-label fw-semibold">Heading</label><input class="form-control" name="title" value="{{ old('title') }}" maxlength="255" placeholder="Timeless Beauty"></div>
                    <div class="col-12"><label class="form-label fw-semibold">Text</label><input class="form-control" name="sub_title" value="{{ old('sub_title') }}" maxlength="255"></div>
                    <div class="col-8"><label class="form-label fw-semibold">Link</label><input class="form-control" name="url" value="{{ old('url') }}" maxlength="255" placeholder="/store"></div>
                    <div class="col-4"><label class="form-label fw-semibold">Order</label><input class="form-control" type="number" min="0" name="sort_order" value="{{ old('sort_order', 0) }}"></div>
                    <div class="col-12"><button class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Save offer</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header"><h5 class="mb-0">Offers <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $offers->count() }})</span></h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th style="width:140px;">Image</th><th>Heading</th><th class="text-center">Order</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($offers as $offer)
                                <tr>
                                    <td><img src="{{ $offer->image_path }}" alt="" style="width:120px;height:52px;object-fit:cover;border-radius:10px;"></td>
                                    <td><span class="fw-semibold">{{ $offer->title ?: '—' }}</span><div class="text-muted small">{{ $offer->sub_title }}</div></td>
                                    <td class="text-center">{{ $offer->sort_order }}</td>
                                    <td><span class="pill {{ $offer->status ? 'pill-green' : 'pill-gray' }}">{{ $offer->status ? 'Shown' : 'Hidden' }}</span></td>
                                    <td><div class="d-flex gap-1">
                                        <button type="button" class="tbl-action edit" data-url="{{ route('shop.admin.offer.update', $offer) }}" data-title="{{ $offer->title }}" data-sub="{{ $offer->sub_title }}"
                                            data-link="{{ $offer->url }}" data-sort="{{ $offer->sort_order }}" data-status="{{ (int) $offer->status }}" title="Edit"><i class="bi bi-pencil"></i></button>
                                        <form method="POST" action="{{ route('shop.admin.offer.destroy', $offer) }}" data-confirm="Delete this offer?">@csrf @method('DELETE')<button class="tbl-action del" title="Delete"><i class="bi bi-trash"></i></button></form>
                                    </div></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-row"><i class="bi bi-gift"></i>No special offers yet.</td></tr>
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
