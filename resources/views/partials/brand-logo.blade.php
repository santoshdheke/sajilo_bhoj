{{--
    Business logo. Shows the uploaded company logo when there is one; otherwise a
    serving-dish mark in the theme colour with the business name beside it.

    @include('partials.brand-logo', ['variant' => 'color' | 'light' | 'white', 'height' => 48, 'showName' => true])
      color - theme colour mark, dark name   (white backgrounds)
      light - softened theme colour, white name (dark theme-coloured backgrounds)
      white - all white                        (dark backgrounds, e.g. footer)
--}}
@php
    $variant  = $variant ?? 'color';
    $height   = $height ?? 48;
    $showName = $showName ?? true;
    $name     = $brandName ?? 'SajiloBhoj';
@endphp

@if (!empty($brandLogoUrl))
    <img src="{{ $brandLogoUrl }}" alt="{{ $name }}" class="brand-logo-img" style="height:{{ $height }}px;width:auto;max-width:100%;object-fit:contain;">
@else
    <span class="brand-mark brand-mark--{{ $variant }}" style="--bm-h:{{ $height }}px;">
        <svg class="brand-mark__icon" viewBox="0 0 240 150" aria-hidden="true">
            <g fill="none" stroke-width="7" stroke-linecap="round" style="stroke:var(--bm-soft)">
                <path d="M104 40c-9-11 9-15 0-27"/><path d="M122 36c-9-11 9-15 0-27"/>
            </g>
            <path d="M58 104a62 56 0 0 1 124 0z" style="fill:var(--bm-main)"/>
            <path d="M78 86a42 38 0 0 1 28-26" fill="none" stroke="#fff" stroke-width="7" stroke-linecap="round" opacity=".5"/>
            <circle cx="120" cy="47" r="9" style="fill:var(--bm-main)"/>
            <rect x="50" y="100" width="140" height="11" rx="5.5" style="fill:var(--bm-dark)"/>
            <path d="M176 88c10-20 30-26 50-24-8 20-28 30-50 24z" style="fill:var(--bm-main)" opacity=".85"/>
            <path d="M182 102c14-10 32-10 46 0-14 10-32 10-46 0z" style="fill:var(--bm-soft)"/>
            <path d="M12 136c40-22 92-28 150-16 26 6 46 2 70-8-20 22-62 30-102 24-44-6-84-6-118 0z" style="fill:var(--bm-main)"/>
        </svg>
        @if ($showName)
            <span class="brand-mark__name">{{ $name }}</span>
        @endif
    </span>
@endif

@once
<style>
    .brand-mark { display: inline-flex; align-items: center; gap: calc(var(--bm-h) * .22); line-height: 1; max-width: 100%;
                  --bm-main: var(--primary, #198754);
                  --bm-dark: color-mix(in srgb, var(--primary, #198754) 78%, black);
                  --bm-soft: color-mix(in srgb, var(--primary, #198754) 30%, white); }
    .brand-mark__icon { height: var(--bm-h); width: auto; flex-shrink: 0; }
    .brand-mark__name { font-weight: 800; letter-spacing: -.02em; font-size: calc(var(--bm-h) * .42); color: color-mix(in srgb, var(--primary, #198754) 55%, black);
                        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .brand-mark--light { --bm-main: color-mix(in srgb, var(--primary, #198754) 55%, white); --bm-dark: color-mix(in srgb, var(--primary, #198754) 30%, white); --bm-soft: rgba(255,255,255,.75); }
    .brand-mark--light .brand-mark__name { color: #fff; }
    .brand-mark--white { --bm-main: #fff; --bm-dark: rgba(255,255,255,.85); --bm-soft: rgba(255,255,255,.6); }
    .brand-mark--white .brand-mark__name { color: #fff; }
</style>
@endonce
