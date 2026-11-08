@extends('frontend.theme_one.component.layout')

@section('content')
    @php
        $points = collect(range(1, 4))->map(fn ($i) => $settings["about_point_{$i}"] ?? '')->filter();
        $values = collect(range(1, 3))->map(fn ($i) => [
            $settings["about_value_{$i}_icon"] ?: 'bi-star',
            $settings["about_value_{$i}_title"] ?? '',
            $settings["about_value_{$i}_desc"] ?? '',
        ])->filter(fn ($v) => $v[1] !== '');
    @endphp

    @include('frontend.theme_one.component.page-banner', ['title' => 'About ' . ($settings['restaurant_name'] ?: 'SajiloBhoj'), 'subtitle' => $settings['tagline'] ?: 'A restaurant brand with a fresh, modern, and welcoming identity.'])

    <section class="section-space">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="about-chef">@include('partials.chef-cooking', ['class' => 'about-chef__art'])</div>
                </div>
                <div class="col-lg-6">
                    @if (!empty($settings['about_badge']))
                    <span class="pill pill-green mb-3">{{ $settings['about_badge'] }}</span>
                    @endif
                    <h2 class="section-title">{{ $settings['about_title'] ?: 'Built around taste, service, and comfort.' }}</h2>
                    @if ($settings['about_text'])
                    <p class="section-subtitle" style="white-space:pre-line;">{{ $settings['about_text'] }}</p>
                    @endif
                    @if ($points->isNotEmpty())
                    <ul class="list-check mt-4">
                        @foreach ($points as $point)
                        <li>{{ $point }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if ($values->isNotEmpty())
    <section class="section-space pt-0">
        <div class="container">
            <div class="row g-4">
                @foreach ($values as [$icon, $title, $desc])
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-wrap"><i class="bi {{ $icon }}"></i></div>
                        <h5 class="fw-bold">{{ $title }}</h5>
                        <p class="section-subtitle mb-0">{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection
