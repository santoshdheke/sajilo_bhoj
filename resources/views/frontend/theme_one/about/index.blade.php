@extends('frontend.theme_one.component.layout')

@section('content')
    <section class="page-banner">
        <div class="container text-center">
            <h1>About {{ $settings['restaurant_name'] ?: 'SajiloBhoj' }}</h1>
            <p class="mb-0 text-white-50">{{ $settings['tagline'] ?: 'A restaurant brand with a fresh, modern, and welcoming identity.' }}</p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 fade-up">
                    <img src="{{ asset('assets/frontend/img/logo.png') }}" alt="{{ $settings['restaurant_name'] ?: 'SajiloBhoj' }}" class="img-fluid rounded-4 shadow-sm">
                </div>
                <div class="col-lg-6 fade-up">
                    <span class="pill pill-green mb-3">Our Story</span>
                    <h2 class="section-title">Built around taste, service, and comfort.</h2>
                    @if($settings['about_text'])
                    <p class="section-subtitle" style="white-space:pre-line;">{{ $settings['about_text'] }}</p>
                    @else
                    <p class="section-subtitle">{{ $settings['restaurant_name'] ?: 'SajiloBhoj' }} is designed as a complete restaurant brand presence. This website
                        uses the same green-focused palette and soft card layout from your admin panel so customers feel one
                        consistent identity from browsing to ordering.</p>
                    <ul class="list-check mt-4">
                        <li>Dashboard-matching color palette and card design.</li>
                        <li>Restaurant homepage, about, menu, and contact pages.</li>
                        <li>Bootstrap and jQuery based layout for easy editing.</li>
                        <li>Ready to expand into Laravel or CMS integration later.</li>
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="section-space pt-0">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4 fade-up">
                    <div class="feature-card">
                        <div class="icon-wrap"><i class="bi bi-eye"></i></div>
                        <h5 class="fw-bold">Our Vision</h5>
                        <p class="section-subtitle mb-0">To offer warm hospitality with modern digital convenience and
                            memorable flavors.</p>
                    </div>
                </div>
                <div class="col-md-4 fade-up">
                    <div class="feature-card">
                        <div class="icon-wrap"><i class="bi bi-bullseye"></i></div>
                        <h5 class="fw-bold">Our Mission</h5>
                        <p class="section-subtitle mb-0">To serve quality dishes with quick service and a polished customer
                            experience.</p>
                    </div>
                </div>
                <div class="col-md-4 fade-up">
                    <div class="feature-card">
                        <div class="icon-wrap"><i class="bi bi-heart"></i></div>
                        <h5 class="fw-bold">Our Promise</h5>
                        <p class="section-subtitle mb-0">Fresh preparation, clean presentation, and a trustworthy restaurant
                            brand.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
