{{--
    Product order form (fields set on the product in admin → "Customer order form").
    Posted as custom[key]; validated by ProductOptions::fromCustomerInput(). Self-styled so it works in every theme.
    Usage: @include('shop::partials.product-options', ['product' => $product])
--}}
@php $poFields = $product->custom_fields ?? []; @endphp
@if ($poFields)
<style>
    .po-wrap { display: grid; gap: 14px; margin-bottom: 18px; }
    .po-label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: inherit; }
    .po-label .po-req { color: #dc3545; margin-left: 2px; }
    .po-input { width: 100%; padding: 8px 12px; font-size: 14px; border: 1px solid #d0d5dd; border-radius: 8px; background: #fff; color: #111; }
    .po-input:focus { outline: none; border-color: var(--primary, #0d9488); box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary, #0d9488) 18%, transparent); }
    .po-input.po-invalid { border-color: #dc3545; }
    .po-chips { display: flex; flex-wrap: wrap; gap: 8px; }
    .po-chip { position: relative; margin: 0; cursor: pointer; }
    .po-chip input { position: absolute; opacity: 0; pointer-events: none; }
    .po-chip span { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; font-size: 13px; border: 1px solid #d0d5dd; border-radius: 999px; background: #fff; color: #333; transition: all .15s; }
    .po-chip input:checked + span { border-color: var(--primary, #0d9488); background: color-mix(in srgb, var(--primary, #0d9488) 10%, #fff); color: var(--primary, #0d9488); font-weight: 600; }
    .po-chip input:focus-visible + span { box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary, #0d9488) 25%, transparent); }
    .po-swatch { width: 18px; height: 18px; border-radius: 50%; border: 1px solid rgba(0,0,0,.15); flex-shrink: 0; }
    .po-error { color: #dc3545; font-size: 12px; margin-top: 4px; }
</style>
<div class="po-wrap">
    <input type="hidden" name="custom[__form]" value="1">{{-- marks that the order form was shown, even if every box is left empty --}}
    @foreach ($poFields as $field)
        @php
            $name  = 'custom['.$field['key'].']';
            $id    = 'po_'.$product->id.'_'.$field['key'];
            $old   = old('custom.'.$field['key']);
            $error = $errors->first('custom.'.$field['key']) ?: $errors->first('custom.'.$field['key'].'.*');
            $req   = ! empty($field['required']);
        @endphp
        <div class="po-group">
            <label class="po-label" @unless (in_array($field['type'], ['radio', 'color', 'checkbox'])) for="{{ $id }}" @endunless>
                {{ $field['label'] }}@if ($req)<span class="po-req">*</span>@endif
            </label>

            @switch($field['type'])
                @case('select')
                    <select class="po-input {{ $error ? 'po-invalid' : '' }}" id="{{ $id }}" name="{{ $name }}" @required($req)>
                        <option value="">Choose {{ strtolower($field['label']) }}…</option>
                        @foreach ($field['options'] as $opt)<option value="{{ $opt }}" @selected($old === $opt)>{{ $opt }}</option>@endforeach
                    </select>
                    @break

                @case('radio')
                @case('color')
                    <div class="po-chips" role="radiogroup" aria-label="{{ $field['label'] }}">
                        @foreach ($field['options'] as $i => $opt)
                            <label class="po-chip" title="{{ $opt }}">
                                <input type="radio" name="{{ $name }}" value="{{ $opt }}" @checked($old === $opt) @required($req && $i === 0)>
                                <span>
                                    @if ($field['type'] === 'color')<i class="po-swatch" style="background: {{ preg_match('/^#?[0-9a-f]{3,8}$/i', $opt) ? '#'.ltrim($opt, '#') : e(preg_replace('/[^a-zA-Z]/', '', $opt)) }};"></i>@endif
                                    {{ $opt }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @break

                @case('checkbox')
                    <div class="po-chips" data-po-required="{{ $req ? 1 : 0 }}">
                        @foreach ($field['options'] as $opt)
                            <label class="po-chip">
                                <input type="checkbox" name="{{ $name }}[]" value="{{ $opt }}" @checked(in_array($opt, (array) $old, true))>
                                <span>{{ $opt }}</span>
                            </label>
                        @endforeach
                    </div>
                    @break

                @case('textarea')
                    <textarea class="po-input {{ $error ? 'po-invalid' : '' }}" id="{{ $id }}" name="{{ $name }}" rows="3" maxlength="1000" placeholder="{{ $field['placeholder'] ?? '' }}" @required($req)>{{ $old }}</textarea>
                    @break

                @default
                    <input class="po-input {{ $error ? 'po-invalid' : '' }}" id="{{ $id }}" type="{{ $field['type'] }}" name="{{ $name }}" value="{{ $old }}"
                           placeholder="{{ $field['placeholder'] ?? '' }}" @required($req)
                           @if ($field['type'] === 'text') maxlength="255" @endif
                           @if ($field['type'] === 'date') min="{{ now()->toDateString() }}" @endif
                           @if ($field['type'] === 'number') step="any" @endif>
            @endswitch

            @if ($error)<div class="po-error">{{ $error }}</div>@endif
        </div>
    @endforeach
</div>
<script>
// Required checkbox groups: browsers can't enforce "at least one", so check on submit.
document.querySelectorAll('[data-po-required="1"]').forEach(function (group) {
    var form = group.closest('form');
    if (!form || form.dataset.poBound) return;
    form.dataset.poBound = 1;
    form.addEventListener('submit', function (e) {
        var missing = Array.prototype.find.call(form.querySelectorAll('[data-po-required="1"]'), function (g) { return !g.querySelector('input:checked'); });
        if (missing) {
            e.preventDefault();
            var label = missing.parentElement.querySelector('.po-label').textContent.replace('*', '').trim();
            (window.notify && notify.error) ? notify.error('Please choose ' + label + '.') : alert('Please choose ' + label + '.');
            missing.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
</script>
@endif
