{{-- Shared light page banner. Usage: @include('frontend.theme_one.component.page-banner', ['title' => '...', 'subtitle' => '...']) --}}
@php $leaf = '<svg viewBox="0 0 40 60" fill="currentColor" aria-hidden="true"><path d="M20 0C5 15 0 30 6 45c3 8 8 13 14 15 6-2 11-7 14-15C40 30 35 15 20 0z"/><path d="M20 8v50" stroke="#fff" stroke-width="1.5" opacity=".6"/></svg>'; @endphp
<section class="page-banner">
    <span class="pb-leaf pb-leaf-1">{!! $leaf !!}</span>
    <span class="pb-leaf pb-leaf-2">{!! $leaf !!}</span>
    <span class="pb-leaf pb-leaf-3">{!! $leaf !!}</span>

    <svg class="pb-art" viewBox="0 0 260 150" aria-hidden="true">
        <g fill="var(--sb-green-soft)">
            <rect x="20" y="20" width="5" height="40" rx="2"/><rect x="30" y="20" width="5" height="40" rx="2"/><rect x="40" y="20" width="5" height="40" rx="2"/>
            <path d="M18 56h29c0 8-6 12-12 12H30c-6 0-12-4-12-12z"/><rect x="29" y="64" width="7" height="66" rx="3"/>
            <ellipse cx="72" cy="40" rx="11" ry="20"/><rect x="69" y="56" width="7" height="74" rx="3"/>
        </g>
        <g fill="none" stroke="var(--sb-green-soft)" stroke-width="4" stroke-linecap="round">
            <path d="M150 36c-7-9 7-12 0-22"/><path d="M164 32c-7-9 7-12 0-22"/>
        </g>
        <path d="M100 112a64 58 0 0 1 128 0z" fill="color-mix(in srgb, var(--primary) 55%, white)"/>
        <path d="M118 92a46 42 0 0 1 32-28" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round" opacity=".45"/>
        <circle cx="164" cy="50" r="8" fill="color-mix(in srgb, var(--primary) 65%, white)"/>
        <rect x="90" y="110" width="148" height="10" rx="5" fill="color-mix(in srgb, var(--primary) 70%, white)"/>
        <path d="M84 136c40-16 90-20 130-10 18 4 30 2 42-4-14 16-44 22-76 18-36-4-66-4-96-4z" fill="var(--sb-green-soft)"/>
    </svg>

    <div class="container text-center position-relative">
        <h1>{{ $title }}</h1>
        @if (!empty($subtitle))<p>{{ $subtitle }}</p>@endif
        <span class="pb-bar"></span>
    </div>
</section>
