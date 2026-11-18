<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-soft h-100">
            <div class="card-body">
                <h5 class="mb-1">Store details</h5>
                <p class="text-secondary mb-4">Your store name and the details printed on receipts.</p>
                <div class="row g-3">
                    <div class="col-12"><label class="form-label fw-semibold">Store name <span class="text-danger">*</span></label>
                        <input class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $company->name) }}" required maxlength="255"></div>
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
    </div>
    <div class="col-lg-4">
        <div class="card-soft h-100">
            <div class="card-body text-center">
                <h5 class="mb-1 text-start">Logo</h5>
                <p class="text-secondary mb-4 text-start">Shown in the store header and as the browser icon.</p>
                @if ($shopLogoUrl)
                    <img src="{{ $shopLogoUrl }}" alt="" style="max-width:140px;max-height:140px;border-radius:16px;object-fit:contain;" class="mb-3">
                    <div class="form-check d-flex justify-content-center gap-2 mb-3"><input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="removeLogo"><label class="form-check-label small" for="removeLogo">Remove logo</label></div>
                @else
                    <span class="d-inline-flex align-items-center justify-content-center mb-2" style="width:96px;height:96px;border-radius:22px;background:color-mix(in srgb, var(--primary) 14%, white);color:var(--primary);font-size:2.6rem;"><i class="bi bi-shop"></i></span>
                    <div class="text-secondary small mb-3">No logo uploaded — the store icon is used.</div>
                @endif
                <input class="form-control" type="file" name="logo" accept="image/*">
            </div>
        </div>
    </div>
</div>
