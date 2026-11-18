@extends($theme_path . 'common.layout.master_layout')
@section('title', $pageTitle.' - '.$shop->name)

@section('contant')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a class="text-brand text-decoration-none" href="{{ route('shop.front.home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                </ol>
            </nav>
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h3 fw-bold mb-4 border-start border-4 border-brand ps-3">{{ $pageTitle }}</h1>
                    <div class="text-secondary lh-lg">{!! nl2br(e($pageText)) !!}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
