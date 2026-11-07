{{--
    Chef cooking: tossing food in a pan over a flame, with rising steam.
    Brand accents (scarf, spatula handle, counter, steam, leaves) follow --primary.
    Gentle animation; stops for visitors who prefer reduced motion.
--}}
<svg class="{{ $class ?? 'chef-cooking' }}" viewBox="0 0 360 260" role="img" aria-label="Chef cooking">
    <style>
        .ck-steam path { animation: ckSteam 3s ease-in-out infinite; opacity: 0; }
        .ck-steam path:nth-child(2) { animation-delay: 1s; }
        .ck-steam path:nth-child(3) { animation-delay: 2s; }
        .ck-flame { transform-origin: 300px 232px; animation: ckFlicker .5s ease-in-out infinite alternate; }
        .ck-flame.f2 { animation-duration: .38s; }
        .ck-pan { transform-origin: 242px 195px; animation: ckPan 1.8s ease-in-out infinite; }
        .ck-food { animation: ckToss 1.8s ease-in-out infinite; }
        .ck-food circle:nth-child(2), .ck-food rect { animation: ckToss 1.8s ease-in-out infinite .08s; }
        .ck-spatula { transform-origin: 76px 156px; animation: ckWave 1.8s ease-in-out infinite; }
        .ck-sparkle { animation: ckTwinkle 2.4s ease-in-out infinite; }
        .ck-sparkle:nth-of-type(2) { animation-delay: .8s; }
        @keyframes ckSteam  { 0% { transform: translateY(6px); opacity: 0 } 30% { opacity: .75 } 100% { transform: translateY(-26px); opacity: 0 } }
        @keyframes ckFlicker{ from { transform: scaleY(.82) } to { transform: scaleY(1.1) } }
        @keyframes ckPan    { 0%, 100% { transform: rotate(0) } 35% { transform: rotate(-6deg) } 55% { transform: rotate(2deg) } }
        @keyframes ckToss   { 0%, 100% { transform: translateY(0) } 40% { transform: translateY(-22px) } 60% { transform: translateY(-6px) } }
        @keyframes ckWave   { 0%, 100% { transform: rotate(0) } 50% { transform: rotate(-10deg) } }
        @keyframes ckTwinkle{ 0%, 100% { opacity: 0; transform: scale(.6) } 50% { opacity: 1; transform: scale(1) } }
        @media (prefers-reduced-motion: reduce) { .ck-steam path, .ck-flame, .ck-pan, .ck-food, .ck-food *, .ck-spatula, .ck-sparkle { animation: none; opacity: 1; } }
    </style>

    {{-- background leaves --}}
    <g style="fill: color-mix(in srgb, var(--primary) 40%, white)" opacity=".5">
        <path d="M14 258c-12-46 8-92 52-110 10 46-12 92-52 110z"/>
        <path d="M70 250c-26-14-38-40-30-66 26 10 38 38 30 66z" opacity=".7"/>
        <path d="M352 120c-26-4-44 10-50 30 24 6 44-6 50-30z" opacity=".8"/>
    </g>

    {{-- sparkles --}}
    <g style="fill: color-mix(in srgb, var(--primary) 60%, white)">
        <path class="ck-sparkle" d="M214 70l3 8 8 3-8 3-3 8-3-8-8-3 8-3z"/>
        <path class="ck-sparkle" d="M326 70l2 6 6 2-6 2-2 6-2-6-6-2 6-2z"/>
    </g>

    {{-- counter + burner --}}
    <rect x="228" y="232" width="132" height="28" rx="7" style="fill: color-mix(in srgb, var(--primary) 55%, black)"/>
    <rect x="268" y="228" width="64" height="7" rx="3.5" fill="#3a4044"/>

    {{-- flames --}}
    <path class="ck-flame" d="M300 232c-14 0-20-10-16-20 3 6 7 7 9 4-3-9 2-18 9-22-2 8 3 12 7 9 1 7 9 11 8 19-1 6-7 10-17 10z" fill="#f97316"/>
    <path class="ck-flame f2" d="M300 232c-8 0-12-6-9-12 2 3 5 3 6 1-1-5 2-10 6-12-1 5 2 7 4 5 1 4 5 7 4 11-1 4-4 7-11 7z" fill="#fbbf24"/>

    {{-- steam --}}
    <g class="ck-steam" fill="none" stroke-width="5" stroke-linecap="round" style="stroke: color-mix(in srgb, var(--primary) 30%, white)">
        <path d="M286 160c-8-9 8-13 0-24"/>
        <path d="M302 154c-8-9 8-13 0-24"/>
        <path d="M318 160c-8-9 8-13 0-24"/>
    </g>

    {{-- pan (tilts) + tossed food --}}
    <g class="ck-pan">
        <line x1="242" y1="195" x2="264" y2="192" stroke="#2f3437" stroke-width="8" stroke-linecap="round"/>
        <path d="M258 190q42 30 84 0z" fill="#3d4448"/>
        <ellipse cx="300" cy="190" rx="42" ry="9" fill="#2a2f32"/>
        <g class="ck-food">
            <circle cx="288" cy="182" r="5.5" fill="#ef4444"/>
            <circle cx="303" cy="176" r="4.5" fill="#22c55e"/>
            <circle cx="316" cy="183" r="5" fill="#facc15"/>
            <rect x="294" y="183" width="10" height="4" rx="2" fill="#fb923c" transform="rotate(-20 299 185)"/>
        </g>
    </g>

    {{-- chef body --}}
    <path d="M95 260c0-48 24-78 58-78s58 30 58 78z" fill="#fff" stroke="#dfe6e2" stroke-width="2"/>
    <path d="M136 186l17 20 17-20" fill="none" stroke="var(--primary)" stroke-width="8" stroke-linejoin="round" stroke-linecap="round"/>
    <circle cx="153" cy="222" r="4" fill="#1c2620"/><circle cx="153" cy="243" r="4" fill="#1c2620"/>

    {{-- right arm to the pan --}}
    <path d="M204 212c14-4 26-10 34-16" fill="none" stroke="#fff" stroke-width="16" stroke-linecap="round"/>
    <path d="M204 212c14-4 26-10 34-16" fill="none" stroke="#dfe6e2" stroke-width="1.5" stroke-linecap="round" opacity=".6"/>
    <circle cx="242" cy="195" r="10" fill="#f6c9a0"/>

    {{-- left arm with spatula (waves) --}}
    <path d="M104 210c-14-10-22-28-26-46" fill="none" stroke="#fff" stroke-width="16" stroke-linecap="round"/>
    <g class="ck-spatula">
        <line x1="76" y1="156" x2="60" y2="108" stroke-width="5" stroke-linecap="round" style="stroke: color-mix(in srgb, var(--primary) 75%, black)"/>
        <rect x="49" y="86" width="20" height="26" rx="4" fill="#c9d1cd" transform="rotate(-18 59 99)"/>
        <rect x="54" y="92" width="10" height="3" rx="1.5" fill="#aab6b1" transform="rotate(-18 59 99)"/>
    </g>
    <circle cx="76" cy="156" r="11" fill="#f6c9a0"/>

    {{-- head --}}
    <circle cx="153" cy="140" r="32" fill="#f6c9a0"/>
    <path d="M122 134c4-16 17-23 31-23s27 7 31 23c-9-7-19-9-31-9s-22 2-31 9z" fill="#3b2a20"/>
    <path d="M137 139q5-6 10 0M159 139q5-6 10 0" fill="none" stroke="#1c2620" stroke-width="3" stroke-linecap="round"/>
    <path d="M142 152q11 13 22 0z" fill="#9b3d2a"/>
    <path d="M147 156q6 4 12 0" fill="#f28b82"/>
    <circle cx="133" cy="150" r="5" fill="#f4a3a3" opacity=".6"/><circle cx="173" cy="150" r="5" fill="#f4a3a3" opacity=".6"/>

    {{-- hat --}}
    <rect x="125" y="100" width="56" height="16" rx="4" fill="#fff" stroke="#dfe6e2" stroke-width="2"/>
    <path d="M125 104c-17-2-24-20-13-31 7-8 19-8 24-2 4-13 30-13 34 0 5-6 17-6 24 2 11 11 4 29-13 31z" fill="#fff" stroke="#dfe6e2" stroke-width="2"/>
</svg>
