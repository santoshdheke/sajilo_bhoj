{{--
    "12 / 25 menu items" chip for a plan limit. Usage: @include('partials.plan-usage', ['key' => 'items'|'staff'|'customers'])
    Amber from 80 % of the limit, red when it's reached. Hidden when the plan is unlimited.
--}}
@php
    $puCompany = \App\Support\Tenancy::company();
    $puLimit   = \App\Support\PlanLimits::limit($puCompany, $key);
    $puUsed    = \App\Support\PlanLimits::used($puCompany, $key);
    $puLabel   = \App\Support\PlanLimits::RESOURCES[$key][1];
    $puState   = $puLimit === null ? '' : ($puUsed >= $puLimit ? 'full' : ($puUsed >= 0.8 * $puLimit ? 'near' : ''));
@endphp
@if ($puLimit !== null)
<span class="plan-usage {{ $puState }}" title="{{ $puCompany?->currentPlan()?->name }} plan limit">
    <i class="bi {{ $puState === 'full' ? 'bi-exclamation-circle-fill' : 'bi-pie-chart-fill' }}"></i>
    <strong>{{ number_format($puUsed) }} / {{ number_format($puLimit) }}</strong> {{ $puLabel }}
    @if ($puState === 'full')<span class="pu-note">· limit reached, upgrade to add more</span>@endif
</span>
@once
<style>
    .plan-usage { display:inline-flex; align-items:center; gap:6px; margin-top:8px; padding:4px 12px; border-radius:999px; font-size:.8rem;
                  background:color-mix(in srgb, var(--primary) 9%, white); color:color-mix(in srgb, var(--primary) 70%, #222); }
    .plan-usage.near { background:#fff6e0; color:#8a5a00; }
    .plan-usage.full { background:#fdecee; color:#b42335; }
    .plan-usage .pu-note { font-weight:500; }
</style>
@endonce
@endif
