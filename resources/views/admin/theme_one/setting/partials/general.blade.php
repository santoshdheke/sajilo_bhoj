<div class="card-soft">
    <div class="card-body">
        <h5 class="mb-3">General Info</h5>
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
