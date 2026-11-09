{{-- Left brand panel with the cooking chef scene. Optional: $title, $highlight (underlined end of the title), $lead, $note (HTML) shown under the intro. --}}
        {{-- ── Left: brand + cooking chef ── --}}
        <section class="ws-side">
            <a class="ws-logo" href="#" aria-label="SajiloBhoj">
                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M13 26c-5 0-8-4-8-8 0-5 4-8 9-7 1-5 6-8 10-8s9 3 10 8c5-1 9 2 9 7 0 4-3 8-8 8"/>
                    <path d="M13 24v16h22V24"/><path d="M13 33h22"/><path d="M20 15v4M28 15v4"/>
                </svg>
                <span>Sajilo<span>Bhoj</span></span>
            </a>

            <h1>{{ $title ?? 'Bring your restaurant online' }} <em>{{ $highlight ?? 'in minutes' }}<svg viewBox="0 0 200 12" preserveAspectRatio="none" aria-hidden="true"><path d="M4 9C60 2 140 2 196 7" fill="none" stroke="#f5a524" stroke-width="5" stroke-linecap="round"/></svg></em></h1>
            <p class="lead">{{ $lead ?? 'Manage orders, tables and grow your business with ease.' }}</p>
            @isset($note)<span class="ws-note">{!! $note !!}</span>@endisset

            <div class="ws-scene" aria-hidden="true">
                <svg class="ws-hills" viewBox="0 0 600 260" preserveAspectRatio="none">
                    <path d="M0 120C120 60 240 70 360 110s170 30 240 0V260H0z" style="fill: color-mix(in srgb, var(--primary) 22%, white)"/>
                    <path d="M0 170C140 120 280 140 420 160s140 0 180-20V260H0z" style="fill: color-mix(in srgb, var(--primary) 45%, white)"/>
                    <path d="M0 210C160 180 320 200 460 205s110-10 140-20V260H0z" style="fill: color-mix(in srgb, var(--primary) 70%, white)"/>
                </svg>

                {{-- table with plant and "Good Food Good Mood" sign --}}
                <svg class="ws-table" viewBox="0 0 260 200">
                    <ellipse cx="130" cy="150" rx="120" ry="20" fill="#a0673a"/>
                    <rect x="10" y="140" width="240" height="12" rx="6" fill="#c08350"/>
                    <rect x="122" y="152" width="16" height="48" fill="#8a5630"/>
                    {{-- plant --}}
                    <path d="M54 140h32l-4-26H58z" fill="#f4efe6" stroke="#e1d7c4" stroke-width="2"/>
                    <g class="sway" style="fill: color-mix(in srgb, var(--primary) 85%, #22c55e)">
                        <path d="M70 114V70" fill="none" stroke="#3f7a3a" stroke-width="3"/>
                        <path d="M70 90c-16-2-24-12-24-22 14 0 24 8 24 22z"/>
                        <path d="M70 84c14-2 22-12 22-22-13 0-22 8-22 22z"/>
                        <path d="M70 74c-10-4-14-12-12-20 10 2 14 10 12 20z"/>
                        <path d="M70 102c12 0 20-6 22-14-12-2-20 4-22 14z"/>
                    </g>
                    {{-- sign --}}
                    <g transform="rotate(-4 170 105)">
                        <rect x="128" y="64" width="84" height="76" rx="6" fill="#f6e9cf" stroke="#e2cfa6" stroke-width="2"/>
                        <text x="170" y="88" text-anchor="middle" font-family="'Comic Sans MS','Segoe Print',cursive" font-size="14" fill="#3b2a20">Good</text>
                        <text x="170" y="106" text-anchor="middle" font-family="'Comic Sans MS','Segoe Print',cursive" font-size="14" fill="#3b2a20">Food</text>
                        <text x="170" y="124" text-anchor="middle" font-family="'Comic Sans MS','Segoe Print',cursive" font-size="12.5" fill="#3b2a20">Good Mood</text>
                        <path d="M164 131q6 5 12 0" fill="none" stroke="#c08350" stroke-width="2" stroke-linecap="round"/>
                    </g>
                </svg>

                @include('partials.chef-cooking', ['class' => 'ws-chef'])
            </div>

            <div class="ws-features">
                <span><i class="bi bi-lightning-charge-fill"></i>Fast Setup</span>
                <span><i class="bi bi-bar-chart-fill"></i>Grow Business</span>
                <span><i class="bi bi-heart-fill"></i>Easy to Use</span>
            </div>
        </section>
