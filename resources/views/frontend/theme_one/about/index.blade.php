@extends('frontend.theme_one.component.layout')

@section('content')
    @php
        $s      = $settings;
        $brand  = $brandName ?? ($s['restaurant_name'] ?: 'SajiloBhoj');
        $points = collect(range(1, 4))->map(fn ($i) => $s["about_point_{$i}"] ?? '')->filter();
        $stats  = collect(range(1, 3))->map(fn ($i) => [$s["about_stat_{$i}_value"] ?? '', $s["about_stat_{$i}_label"] ?? ''])->filter(fn ($st) => $st[0] !== '' || $st[1] !== '');
        $values = collect(range(1, 3))->map(fn ($i) => [$s["about_value_{$i}_icon"] ?: 'bi-star', $s["about_value_{$i}_title"] ?? '', $s["about_value_{$i}_desc"] ?? ''])->filter(fn ($v) => $v[1] !== '');
    @endphp

    @include('frontend.theme_one.component.page-banner', [
        'title'    => 'About ' . $brand,
        'subtitle' => ($s['about_banner_subtitle'] ?? '') ?: ($s['tagline'] ?: 'A restaurant with a fresh, modern and welcoming identity.'),
    ])

    {{-- ── Our story ── --}}
    <section class="section-space">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="about-chef">@include('partials.chef-cooking', ['class' => 'about-chef__art'])</div>
                </div>
                <div class="col-lg-6">
                    @if (!empty($s['about_badge']))<span class="pill pill-green mb-3">{{ $s['about_badge'] }}</span>@endif
                    <h2 class="section-title">{{ $s['about_title'] ?: 'Built around taste, service, and comfort.' }}</h2>
                    <p class="section-subtitle about-story">{{ $s['about_text'] ?: $brand . ' brings people together over honest, freshly prepared food. From our kitchen to your table, every dish is cooked with care, served with a smile and priced to keep you coming back.' }}</p>
                    @if ($points->isNotEmpty())
                        <ul class="about-points">
                            @foreach ($points as $point)
                                <li><i class="bi bi-check-circle-fill"></i>{{ $point }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ── Numbers ── --}}
    @if ($stats->isNotEmpty())
    <section class="pb-5">
        <div class="container">
            <div class="about-stats">
                @foreach ($stats as [$val, $label])
                    <div class="about-stat">
                        <strong>{{ $val }}</strong>
                        <span>{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ── Values ── --}}
    @if ($values->isNotEmpty())
    <section class="section-space pt-4">
        <div class="container">
            <div class="row g-4 justify-content-center">
                @foreach ($values as [$icon, $title, $desc])
                    <div class="col-md-6 col-lg-4">
                        <div class="about-value">
                            <span class="about-value__ico"><i class="bi {{ $icon }}"></i></span>
                            <h5>{{ $title }}</h5>
                            @if ($desc)<p>{{ $desc }}</p>@endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ── Call to action ── --}}
    @if (!empty($s['about_cta_title']))
    <section class="pb-5">
        <div class="container">
            <div class="about-cta">
                <div>
                    <h3>{{ $s['about_cta_title'] }}</h3>
                    @if (!empty($s['about_cta_text']))<p>{{ $s['about_cta_text'] }}</p>@endif
                </div>
                <div class="about-cta__actions">
                    <a href="{{ route('frontend.menu.index') }}" class="about-cta__btn light">View menu <i class="bi bi-arrow-right"></i></a>
                    <a href="{{ route('frontend.table.index') }}" class="about-cta__btn line"><i class="bi bi-calendar-check"></i> Book a table</a>
                </div>
            </div>
        </div>
    </section>
    @endif
@endsection
