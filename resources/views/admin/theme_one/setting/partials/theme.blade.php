@php
    $defaultColor = \App\Http\Controllers\Admin\SettingController::SECTIONS['theme']['fields']['theme_color'];
    $currentColor = old('theme_color', $settings['theme_color'] ?: $defaultColor);
    $presets = ['#198754', '#0f766e', '#2563eb', '#4f46e5', '#7c3aed', '#c026d3', '#e91e8c', '#dc2626', '#ea580c', '#b45309', '#8b5e34', '#111827'];
@endphp

<div class="card-soft">
    <div class="card-body">
        <h5 class="mb-1">Theme</h5>
        <p class="text-secondary mb-4">Pick a colour to preview it across the whole dashboard right away. Click <strong>Save Settings</strong> to keep it.</p>

        <div class="row g-4 align-items-start">
            <div class="col-12">
                <div class="brand-color-card">
                    <div class="brand-color-label"><i class="bi bi-palette"></i>Brand / Primary Color</div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <label class="color-swatch" id="themeSwatch" style="background: {{ $currentColor }};" title="Choose colour">
                            <input type="color" id="themePicker" value="{{ $currentColor }}" aria-label="Choose brand colour">
                        </label>
                        <div class="flex-grow-1">
                            <input type="text" name="theme_color" id="themeHex" maxlength="7"
                                   class="form-control color-hex-input @error('theme_color') is-invalid @enderror"
                                   value="{{ $currentColor }}" pattern="^#[0-9A-Fa-f]{6}$" aria-label="Hex colour code">
                            @error('theme_color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="brand-color-label mb-2">Presets</div>
                    <div class="color-presets">
                        @foreach ($presets as $p)
                            <button type="button" class="color-preset" data-color="{{ $p }}" style="background: {{ $p }};" title="{{ $p }}" aria-label="Use {{ $p }}"></button>
                        @endforeach
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-3 small text-secondary">
                        <span id="themeChanged" class="d-none"><i class="bi bi-dot text-warning" style="font-size:1.4rem;vertical-align:-6px;"></i>Previewing — not saved yet</span>
                        <button type="button" class="btn btn-link btn-sm p-0 d-none" id="themeUndo">Undo changes</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



<div class="card-soft mt-3">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h5 class="mb-0">Live preview — Home page</h5>
                <small class="text-secondary">Your real website, recoloured as you pick — scroll inside to see the whole page. Nothing is saved until you click Save Settings.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="tp-devices" role="group" aria-label="Preview size">
                    <button type="button" class="active" data-device="desktop"><i class="bi bi-display me-1"></i>Desktop</button>
                    <button type="button" data-device="mobile"><i class="bi bi-phone me-1"></i>Mobile</button>
                </div>
                <a href="{{ route('frontend.home.index') }}" target="_blank" rel="noopener" class="btn btn-sm theme-reset-btn"><i class="bi bi-box-arrow-up-right me-1"></i>Open website</a>
            </div>
        </div>
        <div class="tp-stage" id="tpStage">
            <div class="tp-frame" id="tpFrame">
                <div class="tp-bar"><span></span><span></span><span></span><em>{{ request()->getHost() }}</em></div>
                <div class="tp-viewport" id="tpViewport">
                    <iframe id="tpIframe" src="{{ route('frontend.home.index') }}" title="Home page preview (scroll to explore)" loading="lazy"></iframe>
                    <div class="tp-loading" id="tpLoading"><span class="spinner-border spinner-border-sm me-2"></span>Loading preview…</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .brand-color-card .color-presets { gap: 12px; }
    .tp-devices { display: inline-flex; background: color-mix(in srgb, var(--primary) 8%, white); border-radius: 999px; padding: 3px; }
    .tp-devices button { border: 0; background: none; border-radius: 999px; padding: 6px 14px; font-size: .85rem; font-weight: 600; color: #2f3b34; }
    .tp-devices button.active { background: var(--primary); color: #fff; }
    .tp-stage { background: color-mix(in srgb, var(--primary) 5%, #f3f5f4); border-radius: 16px; padding: 18px; display: flex; justify-content: center; }
    .tp-frame { width: 100%; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 14px 40px rgba(15, 40, 25, .14); transition: width .25s; }
    .tp-frame.mobile { width: 406px; max-width: 100%; border-radius: 28px; border: 8px solid #1c2620; }
    .tp-frame.mobile .tp-bar { display: none; }
    .tp-bar { display: flex; align-items: center; gap: 6px; padding: 9px 12px; background: #eef1ef; }
    .tp-bar span { width: 10px; height: 10px; border-radius: 50%; background: #d3d9d6; }
    .tp-bar em { margin-left: 10px; font-style: normal; font-size: .75rem; color: #4b5a51; background: #fff; border-radius: 999px; padding: 2px 12px; }
    .tp-viewport { position: relative; overflow: hidden; height: 620px; }
    .tp-viewport iframe { position: absolute; top: 0; left: 0; border: 0; transform-origin: 0 0; background: #fff; }
    .tp-loading { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: #fff; color: #4b5a51; font-size: .9rem; }
    .tp-loading.done { display: none; }
    .color-preset.active { box-shadow: 0 0 0 2px #fff, 0 0 0 4px var(--primary); }
    .theme-reset-btn { border: 1.5px solid var(--line); background: #fff; color: #1c2620; }
    .theme-reset-btn:hover { border-color: var(--primary); color: var(--primary); }
</style>

<script>
(function () {
    var saved  = @json($currentColor).toLowerCase();
    var picker = document.getElementById('themePicker'), hex = document.getElementById('themeHex'),
        swatch = document.getElementById('themeSwatch'), changed = document.getElementById('themeChanged'),
        undo   = document.getElementById('themeUndo');

    // Apply a colour to the whole page right away (preview only until saved).
    function apply(color, from) {
        if (!/^#[0-9a-f]{6}$/i.test(color)) return;
        color = color.toLowerCase();
        document.documentElement.style.setProperty('--primary', color);
        paintPreview(color);
        swatch.style.background = color;
        if (from !== 'picker') picker.value = color;
        if (from !== 'hex') hex.value = color;
        document.querySelectorAll('.color-preset').forEach(function (b) { b.classList.toggle('active', b.dataset.color.toLowerCase() === color); });
        var dirty = color !== saved;
        changed.classList.toggle('d-none', !dirty);
        undo.classList.toggle('d-none', !dirty);
    }


    // ── Home page preview (same-site iframe, scaled to fit) ──
    var iframe = document.getElementById('tpIframe'), viewport = document.getElementById('tpViewport'),
        frame = document.getElementById('tpFrame'), device = 'desktop', current = saved;
    var SIZES = { desktop: 1366, mobile: 390 };

    function paintPreview(color) {
        current = color;
        try {
            var doc = iframe.contentDocument;
            if (doc && doc.documentElement) doc.documentElement.style.setProperty('--primary', color);
        } catch (e) { /* cross-origin: preview cannot be recoloured */ }
    }
    function fit() {
        var w = SIZES[device], avail = viewport.clientWidth || w, scale = Math.min(1, avail / w);
        iframe.style.width = w + 'px';
        iframe.style.height = (viewport.clientHeight / scale) + 'px';
        iframe.style.transform = 'scale(' + scale + ')';
    }
    iframe.addEventListener('load', function () {
        document.getElementById('tpLoading').classList.add('done');
        paintPreview(current);
        // The preview scrolls, but links, buttons and forms inside it must not navigate away.
        try {
            var d = iframe.contentDocument;
            d.addEventListener('click', function (e) { if (e.target.closest('a, button, [onclick], input[type=submit]')) { e.preventDefault(); e.stopPropagation(); } }, true);
            d.addEventListener('submit', function (e) { e.preventDefault(); }, true);
            d.documentElement.style.scrollBehavior = 'smooth';
        } catch (e) {}
    });
    document.querySelectorAll('.tp-devices button').forEach(function (b) {
        b.addEventListener('click', function () {
            document.querySelectorAll('.tp-devices button').forEach(function (x) { x.classList.toggle('active', x === b); });
            device = b.dataset.device;
            frame.classList.toggle('mobile', device === 'mobile');
            viewport.style.height = device === 'mobile' ? '720px' : '620px';
            fit();
            setTimeout(fit, 260);
        });
    });
    window.addEventListener('resize', fit);
    fit();

    picker.addEventListener('input', function () { apply(picker.value, 'picker'); });
    hex.addEventListener('input', function () {
        var v = hex.value.trim(); if (v && v[0] !== '#') v = '#' + v;
        apply(v, 'hex');
    });
    document.querySelectorAll('.color-preset').forEach(function (b) { b.addEventListener('click', function () { apply(b.dataset.color); }); });
    undo.addEventListener('click', function () { apply(saved); });

    // Warn before leaving with an unsaved preview.
    var submitting = false;
    hex.form.addEventListener('submit', function () { submitting = true; });
    window.addEventListener('beforeunload', function (e) {
        if (!submitting && hex.value.toLowerCase() !== saved) { e.preventDefault(); e.returnValue = ''; }
    });

    apply(saved);
})();
</script>
