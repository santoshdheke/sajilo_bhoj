<div class="card-soft">
    <div class="card-body">
        <h5 class="mb-3">General Info</h5>
        <div class="mb-4">
            <label class="form-label">Company Logo</label>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="logo-preview" id="logoPreview">
                    @include('partials.brand-logo', ['variant' => 'color', 'height' => 56])
                </div>
                <div class="flex-grow-1" style="min-width:220px;">
                    <input type="file" name="logo" id="logoInput" class="form-control @error('logo') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
                    <div class="form-text">PNG, JPG or WEBP, up to 2 MB. Without a logo, the dish icon in your theme colour and your restaurant name are shown.</div>
                    @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @if (!empty($brandLogoUrl))
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="removeLogo">
                            <label class="form-check-label" for="removeLogo">Remove logo (use the default dish icon)</label>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <style>
            .logo-preview { min-width: 200px; min-height: 80px; padding: 12px 16px; border: 1px dashed color-mix(in srgb, var(--primary) 35%, white); border-radius: 14px; display: flex; align-items: center; justify-content: center; background: #fff; }
            .logo-preview img { max-height: 64px; max-width: 220px; object-fit: contain; }
        </style>
        <script>
            document.getElementById('logoInput').addEventListener('change', function () {
                var f = this.files && this.files[0];
                if (!f) return;
                var img = document.createElement('img');
                img.src = URL.createObjectURL(f);
                img.alt = 'Logo preview';
                var box = document.getElementById('logoPreview');
                box.innerHTML = '';
                box.appendChild(img);
            });
        </script>
        <div class="mb-3"><label class="form-label">Restaurant Name</label>
            <input type="text" name="restaurant_name" class="form-control" maxlength="150" value="{{ old('restaurant_name', $settings['restaurant_name']) }}"></div>
        <div class="mb-3"><label class="form-label">Tagline</label>
            <input type="text" name="tagline" class="form-control" maxlength="255" value="{{ old('tagline', $settings['tagline']) }}"></div>
        <div class="row g-3">
            <div class="col-md-6 mb-3"><label class="form-label">Currency</label>
                <select name="currency" class="form-select">
                    @foreach(['NPR','USD','INR','EUR','GBP'] as $cur)
                        <option value="{{ $cur }}" {{ old('currency', $settings['currency']) === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                    @endforeach
                </select></div>
            <div class="col-md-6 mb-3"><label class="form-label">VAT / Tax (%)</label>
                <input type="number" step="0.01" min="0" max="100" name="tax_percent" class="form-control" value="{{ old('tax_percent', $settings['tax_percent']) }}"></div>
        </div>
    </div>
</div>
