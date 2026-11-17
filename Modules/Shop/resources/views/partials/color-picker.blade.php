{{--
    Brand colour picker: preset swatches + custom colour. Picking a colour updates the hidden input
    and the page's --primary colour live. Params: $name (input name), $value (#rrggbb).
    Styles: .ws-colors / .ws-color (partials/auth-split/styles, copied into the admin profile page).
--}}
@php($presets = ['#f97316', '#2563eb', '#0d9488', '#16a34a', '#f59e0b', '#e11d48', '#ec4899', '#8b5cf6', '#1e3a8a', '#111827'])
<input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ $value }}">
<div class="ws-colors" role="radiogroup" aria-label="Theme colour" data-color-picker="{{ $name }}">
    @foreach ($presets as $p)
        <button type="button" class="ws-color" data-color="{{ $p }}" style="background: {{ $p }}" role="radio" aria-label="{{ $p }}"></button>
    @endforeach
    <label class="ws-color ws-color-custom" title="Custom colour" aria-label="Custom colour">
        <input type="color" value="{{ $value }}">
    </label>
</div>
<script>
(function () {
    var box = document.querySelector('[data-color-picker="{{ $name }}"]'), hidden = document.getElementById(@json($name)),
        custom = box.querySelector('input[type=color]'), swatches = box.querySelectorAll('.ws-color[data-color]'),
        customBox = box.querySelector('.ws-color-custom');

    function setColor(hex) {
        hex = hex.toLowerCase();
        hidden.value = hex;
        document.documentElement.style.setProperty('--primary', hex);
        var matched = false;
        swatches.forEach(function (b) {
            var on = b.dataset.color.toLowerCase() === hex;
            b.classList.toggle('active', on); b.setAttribute('aria-checked', on ? 'true' : 'false');
            matched = matched || on;
        });
        customBox.classList.toggle('active', !matched);
        customBox.style.background = matched ? '' : hex;
    }
    swatches.forEach(function (b) { b.addEventListener('click', function () { setColor(b.dataset.color); }); });
    custom.addEventListener('input', function () { setColor(custom.value); });
    setColor(hidden.value);
})();
</script>
