@extends('shop::admin.theme_one.component.layout')
@section('title', 'Settings')

@php
    $tab   = request('tab', 'store');
    $brand = $settings->brand();
    $tabs  = ['store' => ['bi-shop', 'Store'], 'branding' => ['bi-palette', 'Branding'], 'payment' => ['bi-wallet2', 'Payments'], 'social' => ['bi-share', 'Social links']];
@endphp

@push('css')
<style>
.set-tabs .nav-link { color: #475569; font-weight: 600; border-radius: 10px; }
.set-tabs .nav-link.active { background: var(--primary); color: #fff; }
.swatch-row { display: flex; align-items: center; gap: 10px; }
.swatch-row input[type=color] { width: 48px; height: 40px; padding: 2px; border-radius: 10px; }
.brand-preview { border-radius: 16px; overflow: hidden; border: 1px solid #e5e7eb; }
.brand-preview .bp-bar { padding: 8px 14px; font-size: .78rem; text-align: center; }
.brand-preview .bp-head { padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; background: #fff; border-bottom: 1px solid #eee; }
.brand-preview .bp-logo { font-weight: 700; font-style: italic; letter-spacing: 1.5px; text-transform: uppercase; }
.brand-preview .bp-body { padding: 22px 18px; background: #faf9f7; }
.brand-preview .bp-btn { border: 0; border-radius: 50px; padding: 9px 20px; font-weight: 600; font-size: .85rem; }
.brand-preview .bp-foot { padding: 12px 18px; font-size: .78rem; }
</style>
@endpush

@section('content')
<div class="page-head mb-4">
    <div class="page-title mb-0"><h2>Settings</h2><p>Your store details, branding colours and how customers pay online.</p></div>
    <a class="btn btn-outline-primary" href="{{ route('shop.front.home') }}" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>View store</a>
</div>

<form method="POST" action="{{ route('shop.admin.setting.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <input type="hidden" name="tab" id="tabInput" value="{{ $tab }}">

    <ul class="nav set-tabs gap-2 mb-4" role="tablist">
        @foreach ($tabs as $key => [$icon, $label])
            <li class="nav-item"><button type="button" class="nav-link {{ $tab === $key ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-{{ $key }}" data-tab="{{ $key }}"><i class="bi {{ $icon }} me-1"></i>{{ $label }}</button></li>
        @endforeach
    </ul>

    <div class="tab-content">
        {{-- Store --}}
        <div class="tab-pane fade {{ $tab === 'store' ? 'show active' : '' }}" id="tab-store">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card-soft">
                        <div class="card-header"><h5 class="mb-0">Store details</h5></div>
                        <div class="card-body row g-3">
                            <div class="col-md-6"><label class="form-label fw-semibold">Store name <span class="text-danger">*</span></label>
                                <input class="form-control" name="name" value="{{ old('name', $company->name) }}" required maxlength="255"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Email</label>
                                <input class="form-control" type="email" name="email" value="{{ old('email', $company->email) }}"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Phone</label>
                                <input class="form-control" name="phone" value="{{ old('phone', $company->phone) }}" maxlength="30"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Address</label>
                                <input class="form-control" name="address" value="{{ old('address', $company->address) }}" maxlength="255"></div>
                            <div class="col-12"><label class="form-label fw-semibold">About the store</label>
                                <textarea class="form-control" name="about_us" rows="3" maxlength="2000" placeholder="Shown in the storefront footer">{{ old('about_us', $settings->about_us) }}</textarea></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">VAT / PAN number</label>
                                <input class="form-control" name="vat_number" value="{{ old('vat_number', $settings->vat_number) }}" maxlength="50"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Default tax rate (%)</label>
                                <input class="form-control" type="number" step="0.01" min="0" max="100" name="default_tax_rate" value="{{ old('default_tax_rate', $settings->default_tax_rate) }}"></div>
                            <div class="col-12"><label class="form-label fw-semibold">Receipt footer</label>
                                <input class="form-control" name="receipt_footer" value="{{ old('receipt_footer', $settings->receipt_footer) }}" maxlength="500" placeholder="e.g. Goods once sold are not returnable."></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-soft">
                        <div class="card-header"><h5 class="mb-0">Logo</h5></div>
                        <div class="card-body text-center">
                            @if ($shopLogoUrl)
                                <img src="{{ $shopLogoUrl }}" alt="" style="max-width:140px;max-height:140px;border-radius:16px;object-fit:contain;" class="mb-3">
                                <div class="form-check d-inline-block mb-3"><input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="removeLogo"><label class="form-check-label small" for="removeLogo">Remove logo</label></div>
                            @else
                                <div class="text-muted small mb-3">No logo yet — the store name is shown instead.</div>
                            @endif
                            <input class="form-control" type="file" name="logo" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Branding --}}
        <div class="tab-pane fade {{ $tab === 'branding' ? 'show active' : '' }}" id="tab-branding">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card-soft">
                        <div class="card-header"><h5 class="mb-0">Brand colours &amp; font</h5></div>
                        <div class="card-body row g-3">
                            @foreach (['logo_color' => ['Primary colour', 'Logo, buttons, links, announcement bar'], 'logo_color_two' => ['Secondary colour', 'Footer and dark areas'], 'text_color' => ['Text on primary', 'Text on primary-coloured buttons and bars']] as $field => [$label, $help])
                                <div class="col-12">
                                    <label class="form-label fw-semibold">{{ $label }}</label>
                                    <div class="swatch-row">
                                        <input type="color" class="form-control form-control-color" data-color-for="{{ $field }}" value="#{{ old($field, $settings->$field) }}">
                                        <input class="form-control" name="{{ $field }}" id="{{ $field }}" value="#{{ old($field, $settings->$field) }}" pattern="#?[0-9A-Fa-f]{6}" maxlength="7" style="max-width:130px;">
                                        <small class="text-muted">{{ $help }}</small>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-md-6"><label class="form-label fw-semibold">Font</label>
                                <select class="form-select" name="font_family" id="font_family">
                                    @foreach (\Modules\Shop\Models\ShopSetting::FONTS as $font)<option value="{{ $font }}" @selected(old('font_family', $settings->font_family) === $font)>{{ $font }}</option>@endforeach
                                </select></div>
                            <div class="col-12"><label class="form-label fw-semibold">Announcement bar</label>
                                <input class="form-control" name="announcement" id="announcement" value="{{ old('announcement', $settings->announcement) }}" maxlength="255" placeholder="Free delivery across Nepal • Easy 7-day returns"></div>
                            <div class="col-12 text-muted small"><i class="bi bi-info-circle me-1"></i>The primary colour is also used for this back office.</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-soft">
                        <div class="card-header"><h5 class="mb-0">Preview</h5></div>
                        <div class="card-body">
                            <link id="previewFont" rel="stylesheet" href="https://fonts.googleapis.com/css2?family={{ $brand['font'] }}:wght@400;600;700&display=swap">
                            <div class="brand-preview" id="brandPreview" style="font-family:'{{ $brand['font_css'] }}',sans-serif;">
                                <div class="bp-bar" data-bg="logo_color" data-fg="text_color"><span id="pvAnnouncement">{{ $settings->announcement ?: 'Free delivery across Nepal • Easy 7-day returns' }}</span></div>
                                <div class="bp-head"><span class="bp-logo" data-fg="logo_color">{{ $company->name }}</span><span><i class="bi bi-bag" data-fg="logo_color"></i></span></div>
                                <div class="bp-body">
                                    <div class="fw-bold fs-5 mb-1">New collection</div>
                                    <div class="text-muted small mb-3">This is how your storefront buttons look.</div>
                                    <button type="button" class="bp-btn" data-bg="logo_color" data-fg="text_color">Shop now</button>
                                </div>
                                <div class="bp-foot" data-bg="logo_color_two" style="color:#fff;">© {{ date('Y') }} {{ $company->name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payments --}}
        <div class="tab-pane fade {{ $tab === 'payment' ? 'show active' : '' }}" id="tab-payment">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card-soft h-100">
                        <div class="card-header"><h5 class="mb-0"><i class="bi bi-qr-code me-2 text-primary"></i>QR payments</h5></div>
                        <div class="card-body row g-3">
                            <p class="text-muted small mb-0">Customers scan a QR made from your ID, pay in their app, then upload a screenshot. Verify it on the order page.</p>
                            <div class="col-md-6"><label class="form-label fw-semibold">eSewa ID</label>
                                <input class="form-control" name="esewa_id" value="{{ old('esewa_id', $settings->esewa_id) }}" maxlength="100" placeholder="98XXXXXXXX"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">eSewa account name</label>
                                <input class="form-control" name="esewa_name" value="{{ old('esewa_name', $settings->esewa_name) }}" maxlength="100"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Khalti ID</label>
                                <input class="form-control" name="khalti_id" value="{{ old('khalti_id', $settings->khalti_id) }}" maxlength="100" placeholder="98XXXXXXXX"></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Khalti account name</label>
                                <input class="form-control" name="khalti_name" value="{{ old('khalti_name', $settings->khalti_name) }}" maxlength="100"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-soft h-100">
                        <div class="card-header"><h5 class="mb-0"><i class="bi bi-lightning-charge me-2 text-primary"></i>Khalti online payment</h5></div>
                        <div class="card-body row g-3">
                            <p class="text-muted small mb-0">Customers pay on Khalti and the order is marked paid automatically. Use the secret key from your Khalti merchant dashboard.</p>
                            <div class="col-12"><label class="form-label fw-semibold">Secret key</label>
                                <input class="form-control" type="password" name="khalti_secret_key" autocomplete="new-password" placeholder="{{ $settings->khalti_secret_key ? '•••••••• saved — type to replace' : 'live_secret_key_… or test key' }}">
                                @if ($settings->khalti_secret_key)
                                    <div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="remove_khalti_key" value="1" id="removeKey"><label class="form-check-label small" for="removeKey">Remove the saved key (turns off Khalti online payment)</label></div>
                                @endif
                            </div>
                            <div class="col-12"><label class="form-label fw-semibold">Mode</label>
                                <select class="form-select" name="khalti_env">
                                    <option value="test" @selected(old('khalti_env', $settings->khalti_env) === 'test')>Test (sandbox, no real money)</option>
                                    <option value="live" @selected(old('khalti_env', $settings->khalti_env) === 'live')>Live (real payments)</option>
                                </select></div>
                            <div class="col-12"><span class="pill {{ $settings->khalti_secret_key ? 'pill-green' : 'pill-gray' }}">{{ $settings->khalti_secret_key ? 'Khalti online payment is on' : 'Khalti online payment is off' }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Social --}}
        <div class="tab-pane fade {{ $tab === 'social' ? 'show active' : '' }}" id="tab-social">
            <div class="card-soft" style="max-width:720px;">
                <div class="card-header"><h5 class="mb-0">Social links</h5></div>
                <div class="card-body row g-3">
                    <p class="text-muted small mb-0">Shown as icons in the storefront footer. A WhatsApp link (e.g. https://wa.me/9779800000000) also adds a chat button.</p>
                    @foreach (\Modules\Shop\Models\ShopSetting::SOCIAL as $platform => $meta)
                        <div class="col-md-6"><label class="form-label fw-semibold">{{ $meta['label'] }}</label>
                            <input class="form-control" type="url" name="social[{{ $platform }}]" value="{{ old('social.'.$platform, $settings->social_links[$platform] ?? '') }}" placeholder="https://"></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4"><button class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Save settings</button></div>
</form>
@endsection

@push('js')
<script>
(function () {
    // Remember the open tab across the save.
    document.querySelectorAll('[data-tab]').forEach(function (b) { b.addEventListener('shown.bs.tab', function () { document.getElementById('tabInput').value = b.dataset.tab; }); });

    // Colour picker <-> hex field, and the live preview.
    function hex(field) { var v = document.getElementById(field).value.trim(); return /^#?[0-9a-f]{6}$/i.test(v) ? (v[0] === '#' ? v : '#' + v) : null; }
    function paint() {
        document.querySelectorAll('#brandPreview [data-bg]').forEach(function (el) { var c = hex(el.dataset.bg); if (c) el.style.background = c; });
        document.querySelectorAll('#brandPreview [data-fg]').forEach(function (el) { var c = hex(el.dataset.fg); if (c) el.style.color = c; });
    }
    document.querySelectorAll('[data-color-for]').forEach(function (picker) {
        var text = document.getElementById(picker.dataset.colorFor);
        picker.addEventListener('input', function () { text.value = picker.value; paint(); });
        text.addEventListener('input', function () { var c = hex(picker.dataset.colorFor); if (c) picker.value = c; paint(); });
    });
    document.getElementById('font_family').addEventListener('change', function () {
        var f = this.value;
        document.getElementById('previewFont').href = 'https://fonts.googleapis.com/css2?family=' + f.replace(/ /g, '+') + ':wght@400;600;700&display=swap';
        document.getElementById('brandPreview').style.fontFamily = "'" + f + "', sans-serif";
    });
    document.getElementById('announcement').addEventListener('input', function () { document.getElementById('pvAnnouncement').textContent = this.value || 'Free delivery across Nepal • Easy 7-day returns'; });
    paint();
})();
</script>
@endpush
