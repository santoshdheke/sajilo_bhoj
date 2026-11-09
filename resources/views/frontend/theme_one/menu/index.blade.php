@extends('frontend.theme_one.component.layout')

@section('content')

    @include('frontend.theme_one.component.page-banner', ['title' => 'Our Menu', 'subtitle' => $items->count() . ' dishes across ' . $categories->count() . ' categories — fresh every day.'])

<section class="section-space">
    <div class="container">

        @if($table)
        <div class="alert alert-success rounded-4 mb-4 d-flex align-items-center gap-3">
            <i class="bi bi-table fs-4"></i>
            <div>
                <strong>Ordering for {{ $table->name }}</strong> — add items and place your order below.
            </div>
        </div>
        @endif

        @include('frontend.theme_one.component.menu-boards', ['categories' => $categories, 'items' => $items])

    </div>
</section>

{{-- Toast notification --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1100;">
    <div id="cartToast" class="toast align-items-center text-white border-0"
         role="alert" style="background:var(--sb-green-dark);border-radius:14px;">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle me-2"></i>
                <span id="cartToastItem"></span> added to order!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@endsection
