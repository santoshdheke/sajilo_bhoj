<div class="card-soft">
    <div class="card-body">
        <h5 class="mb-3">Theme</h5>
        <label class="form-label">Brand / Primary Color</label>
        <div class="d-flex align-items-center gap-3">
            <input type="color" name="theme_color" class="form-control form-control-color @error('theme_color') is-invalid @enderror"
                   style="width:64px;height:48px;padding:.35rem" value="{{ old('theme_color', $settings['theme_color']) }}">
            <span class="text-secondary">Used for buttons, links, and highlights across your dashboard and storefront.</span>
        </div>
    </div>
</div>
