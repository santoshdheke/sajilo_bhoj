{{-- Attribute inputs for a category; values are comma separated (e.g. "Red,Blue"). --}}
@forelse ($attributes as $attribute)
    <div class="col-md-6">
        <label class="form-label fw-semibold">{{ $attribute->attribute_title }}</label>
        <input class="form-control" name="attributes[{{ $attribute->id }}]" value="{{ old('attributes.'.$attribute->id, $values[$attribute->id] ?? '') }}"
               placeholder="{{ $attribute->input_type === 'color' ? 'e.g. Red, Blue' : 'Values, separated by commas' }}" maxlength="500">
    </div>
@empty
    <div class="col-12 text-muted small">This category has no attributes. Attach some on the <a href="{{ route('shop.admin.category.index') }}">Categories</a> page.</div>
@endforelse
