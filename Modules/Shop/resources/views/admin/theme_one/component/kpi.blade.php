{{-- KPI card. Params: $icon, $label, $value, $tone (blue|green|amber|red|purple|gray), optional $sub, $href. --}}
@php($tones = ['blue' => ['#dbeafe', '#1d4ed8'], 'green' => ['#dcfce7', '#15803d'], 'amber' => ['#fef3c7', '#b45309'], 'red' => ['#fee2e2', '#b91c1c'], 'purple' => ['#ede9fe', '#6d28d9'], 'gray' => ['#f1f5f9', '#475569']])
@php([$bg, $fg] = $tones[$tone ?? 'blue'])
<{!! isset($href) ? 'a href="'.e($href).'"' : 'div' !!} class="card-soft d-flex align-items-center gap-3 p-3 h-100 text-decoration-none text-reset">
    <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:52px;height:52px;border-radius:14px;background:{{ $bg }};color:{{ $fg }};font-size:1.35rem;"><i class="bi {{ $icon }}"></i></span>
    <span class="min-w-0">
        <span class="d-block text-muted small">{{ $label }}</span>
        <span class="d-block fw-bold fs-4 lh-sm money">{{ $value }}</span>
        @isset($sub)<span class="d-block text-muted" style="font-size:.78rem;">{{ $sub }}</span>@endisset
    </span>
</{{ isset($href) ? 'a' : 'div' }}>
