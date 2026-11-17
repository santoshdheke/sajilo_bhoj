{{-- Left brand panel with a shop-front scene. Optional: $title, $highlight (underlined end of the title), $lead, $note (HTML). --}}
        <section class="ws-side">
            <a class="ws-logo" href="#" aria-label="SajiloBhoj">
                <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M8 18l3-9h26l3 9"/><path d="M8 18c0 3 2.5 5 5.3 5s5.4-2 5.4-5c0 3 2.5 5 5.3 5s5.3-2 5.3-5c0 3 2.6 5 5.4 5S40 21 40 18"/>
                    <path d="M11 23v16h26V23"/><path d="M20 39v-9h8v9"/>
                </svg>
                <span>Sajilo<span>Bhoj</span></span>
            </a>

            <h1>{{ $title ?? 'Run your shop' }} <em>{{ $highlight ?? 'the easy way' }}<svg viewBox="0 0 200 12" preserveAspectRatio="none" aria-hidden="true"><path d="M4 9C60 2 140 2 196 7" fill="none" stroke="#f5a524" stroke-width="5" stroke-linecap="round"/></svg></em></h1>
            <p class="lead">{{ $lead ?? 'Sell fast at the counter, track every item in stock and keep your credit book in order.' }}</p>
            @isset($note)<span class="ws-note">{!! $note !!}</span>@endisset

            <div class="ws-scene" aria-hidden="true">
                <svg class="ws-hills" viewBox="0 0 600 260" preserveAspectRatio="none">
                    <path d="M0 120C120 60 240 70 360 110s170 30 240 0V260H0z" style="fill: color-mix(in srgb, var(--primary) 22%, white)"/>
                    <path d="M0 170C140 120 280 140 420 160s140 0 180-20V260H0z" style="fill: color-mix(in srgb, var(--primary) 45%, white)"/>
                    <path d="M0 210C160 180 320 200 460 205s110-10 140-20V260H0z" style="fill: color-mix(in srgb, var(--primary) 70%, white)"/>
                </svg>

                {{-- shopping bag with a tag --}}
                <svg class="ws-table" viewBox="0 0 260 200">
                    <ellipse cx="120" cy="190" rx="90" ry="9" fill="rgba(0,0,0,.08)"/>
                    <path d="M60 70h120l14 118H46z" fill="#f6e9cf" stroke="#e2cfa6" stroke-width="3"/>
                    <path d="M92 82V58a28 28 0 0156 0v24" fill="none" stroke="#a0673a" stroke-width="7" stroke-linecap="round"/>
                    <g class="sway" style="fill: var(--primary)">
                        <path d="M150 120h44l16 16-16 16h-44z"/>
                        <circle cx="160" cy="136" r="4" fill="#fff"/>
                    </g>
                    <text x="120" y="160" text-anchor="middle" font-family="'Comic Sans MS','Segoe Print',cursive" font-size="16" fill="#3b2a20">Sale!</text>
                </svg>

                {{-- shop front with an awning --}}
                <svg class="ws-chef" viewBox="0 0 320 260">
                    <rect x="34" y="70" width="252" height="176" rx="6" fill="#fff" stroke="#dfe5ee" stroke-width="3"/>
                    <path d="M24 70l20-46h232l20 46z" style="fill: color-mix(in srgb, var(--primary) 85%, #000)"/>
                    <g style="fill: var(--primary)">
                        <path d="M24 70h34a17 17 0 01-34 0z"/><path d="M92 70h34a17 17 0 01-34 0z"/><path d="M160 70h34a17 17 0 01-34 0z"/><path d="M228 70h34a17 17 0 01-34 0z"/>
                    </g>
                    <g fill="#fff">
                        <path d="M58 70h34a17 17 0 01-34 0z"/><path d="M126 70h34a17 17 0 01-34 0z"/><path d="M194 70h34a17 17 0 01-34 0z"/><path d="M262 70h34a17 17 0 01-34 0z"/>
                    </g>
                    <rect x="56" y="112" width="120" height="84" rx="4" fill="#eaf2ff" stroke="#c9d8f2" stroke-width="3"/>
                    <rect x="72" y="150" width="22" height="30" rx="3" fill="#f5a524"/><rect x="100" y="140" width="22" height="40" rx="3" style="fill: var(--primary)"/><rect x="128" y="158" width="30" height="22" rx="3" fill="#ec4899"/>
                    <rect x="196" y="112" width="70" height="134" rx="4" fill="#f4f6fb" stroke="#dfe5ee" stroke-width="3"/>
                    <circle cx="252" cy="180" r="4" fill="#a0673a"/>
                    <rect x="200" y="122" width="62" height="20" rx="4" fill="#fff" stroke="#dfe5ee" stroke-width="2"/>
                    <text x="231" y="136" text-anchor="middle" font-family="Inter, sans-serif" font-weight="700" font-size="11" fill="#16a34a">OPEN</text>
                    <rect x="120" y="36" width="80" height="24" rx="5" fill="#fff"/>
                    <text x="160" y="53" text-anchor="middle" font-family="Inter, sans-serif" font-weight="800" font-size="13" fill="#1f2937">SHOP</text>
                </svg>
            </div>

            <div class="ws-features">
                <span><i class="bi bi-upc-scan"></i>Quick POS</span>
                <span><i class="bi bi-box-seam"></i>Stock Tracking</span>
                <span><i class="bi bi-journal-text"></i>Credit Book</span>
            </div>
        </section>
