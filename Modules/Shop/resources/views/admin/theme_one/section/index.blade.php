@extends('shop::admin.theme_one.component.layout')
@section('title', 'Home sections')

@section('content')
<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>Home sections</h2><p>Product rows on your storefront home page, e.g. "New Arrivals" or "Best Sellers". Without sections, the latest products are shown.</p></div>
    <a class="btn btn-outline-primary" href="{{ route('shop.front.home') }}" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>View store</a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-soft form-panel">
            <div class="card-header d-flex align-items-center gap-2"><i class="bi bi-layout-text-window text-primary"></i><h5 class="mb-0">Add section</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('shop.admin.section.store') }}" class="row g-3">
                    @csrf
                    <div class="col-12"><label class="form-label fw-semibold">Title <span class="text-danger">*</span></label><input class="form-control" name="title" value="{{ old('title') }}" required maxlength="255" placeholder="New Arrivals"></div>
                    <div class="col-8"><label class="form-label fw-semibold">Layout</label>
                        <select class="form-select" name="type">@foreach (\Modules\Shop\Models\Section::TYPES as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
                    <div class="col-4"><label class="form-label fw-semibold">Order</label><input class="form-control" type="number" min="0" name="sort_order" value="{{ old('sort_order', 0) }}"></div>
                    <div class="col-12"><button class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Add &amp; pick products</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header"><h5 class="mb-0">Sections <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $sections->count() }})</span></h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Title</th><th>Layout</th><th class="text-center">Products</th><th class="text-center">Order</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($sections as $section)
                                <tr>
                                    <td class="fw-semibold">{{ $section->title }}</td>
                                    <td><span class="pill pill-gray">{{ \Modules\Shop\Models\Section::TYPES[$section->type] ?? $section->type }}</span></td>
                                    <td class="text-center">{{ $section->section_items_count }}</td>
                                    <td class="text-center">{{ $section->sort_order }}</td>
                                    <td><div class="d-flex gap-1">
                                        <a class="tbl-action edit" href="{{ route('shop.admin.section.edit', $section) }}" title="Edit &amp; products"><i class="bi bi-pencil"></i></a>
                                        <form method="POST" action="{{ route('shop.admin.section.destroy', $section) }}" data-confirm="Delete section &quot;{{ $section->title }}&quot;?">@csrf @method('DELETE')<button class="tbl-action del" title="Delete"><i class="bi bi-trash"></i></button></form>
                                    </div></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-row"><i class="bi bi-layout-text-window"></i>No sections yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
