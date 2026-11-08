@php
    $platforms = \App\Support\SocialLinks::PLATFORMS;
    // After a failed save, show what was typed; otherwise the saved links.
    $links = old('social_links') !== null
        ? collect(old('social_links'))->values()
        : $socialLinks->map(fn ($l) => ['platform' => $l['platform'], 'url' => $l['url']]);
@endphp

<div class="card-soft">
    <div class="card-body">
        <h5 class="mb-1">Contact Details</h5>
        <p class="text-secondary small mb-3">Shown on the Contact page and in the website footer. Leave a field empty to hide it.</p>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror" maxlength="255" value="{{ old('contact_email', $settings['contact_email']) }}">
                @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="contact_phone" class="form-control" maxlength="50" value="{{ old('contact_phone', $settings['contact_phone']) }}">
            </div>
            <div class="col-12">
                <label class="form-label">Address</label>
                <textarea name="contact_address" class="form-control" rows="2" maxlength="500">{{ old('contact_address', $settings['contact_address']) }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="card-soft mt-4">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h5 class="mb-1">Social Media</h5>
                <p class="text-secondary small mb-0">Add as many links as you like. They appear as icons in the footer and on the Contact page.</p>
            </div>
            <button type="button" class="btn btn-soft btn-sm" id="addSocial"><i class="bi bi-plus-lg me-1"></i>Add social link</button>
        </div>

        <div id="socialList" class="d-flex flex-column gap-2">
            @foreach ($links as $i => $link)
                @php $p = $platforms[$link['platform'] ?? ''] ?? null; @endphp
                <div class="social-row">
                    <span class="social-ico"><i class="bi {{ $p[1] ?? 'bi-link-45deg' }}"></i></span>
                    <select name="social_links[{{ $i }}][platform]" class="form-select social-platform" aria-label="Platform">
                        @foreach ($platforms as $key => [$label, $icon])
                            <option value="{{ $key }}" data-icon="{{ $icon }}" @selected(($link['platform'] ?? '') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="url" name="social_links[{{ $i }}][url]" class="form-control @error("social_links.$i.url") is-invalid @enderror"
                           value="{{ $link['url'] ?? '' }}" placeholder="https://" maxlength="255" aria-label="Link URL">
                    <button type="button" class="btn social-remove" aria-label="Remove link"><i class="bi bi-trash3"></i></button>
                </div>
            @endforeach
        </div>
        <div id="socialEmpty" class="social-empty {{ $links->isEmpty() ? '' : 'd-none' }}">
            <i class="bi bi-share"></i>No social links yet — click <strong>Add social link</strong>.
        </div>
        @error('social_links.*')<div class="text-danger small mt-2">{{ $message }}</div>@enderror

        <template id="socialTpl">
            <div class="social-row">
                <span class="social-ico"><i class="bi bi-facebook"></i></span>
                <select name="social_links[__I__][platform]" class="form-select social-platform" aria-label="Platform">
                    @foreach ($platforms as $key => [$label, $icon])
                        <option value="{{ $key }}" data-icon="{{ $icon }}">{{ $label }}</option>
                    @endforeach
                </select>
                <input type="url" name="social_links[__I__][url]" class="form-control" placeholder="https://" maxlength="255" aria-label="Link URL">
                <button type="button" class="btn social-remove" aria-label="Remove link"><i class="bi bi-trash3"></i></button>
            </div>
        </template>
    </div>
</div>

<style>
    .social-row { display: grid; grid-template-columns: 44px 200px minmax(0, 1fr) 44px; gap: 10px; align-items: center; }
    .social-ico { width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;
                  background: color-mix(in srgb, var(--primary) 12%, white); color: var(--primary); }
    .social-remove { width: 44px; height: 44px; padding: 0; border-radius: 50% !important; border: 1px solid var(--line); background: #fff; color: #dc3545; }
    .social-remove:hover { background: #fdecee; border-color: #f5c2c7; color: #dc3545; }
    .social-empty { display: flex; align-items: center; gap: 10px; padding: 16px; border: 1px dashed color-mix(in srgb, var(--primary) 30%, white); border-radius: 14px; color: var(--muted); }
    .social-empty .bi { color: var(--primary); font-size: 1.2rem; }
    @media (max-width: 575px) { .social-row { grid-template-columns: 44px minmax(0, 1fr) 44px; } .social-row input { grid-column: 1 / -1; } }
</style>

<script>
(function () {
    var list = document.getElementById('socialList'), tpl = document.getElementById('socialTpl').innerHTML,
        empty = document.getElementById('socialEmpty'), next = {{ $links->count() }};

    function refreshEmpty() { empty.classList.toggle('d-none', list.children.length > 0); }
    function syncIcon(select) {
        var icon = select.options[select.selectedIndex].dataset.icon;
        select.closest('.social-row').querySelector('.social-ico').innerHTML = '<i class="bi ' + icon + '"></i>';
    }

    document.getElementById('addSocial').addEventListener('click', function () {
        // Suggest the first platform not used yet
        var used = Array.from(list.querySelectorAll('.social-platform')).map(function (s) { return s.value; });
        list.insertAdjacentHTML('beforeend', tpl.replace(/__I__/g, next++));
        var row = list.lastElementChild, select = row.querySelector('.social-platform');
        var free = Array.from(select.options).find(function (o) { return used.indexOf(o.value) === -1; });
        if (free) select.value = free.value;
        syncIcon(select);
        row.querySelector('input').focus();
        refreshEmpty();
    });
    list.addEventListener('change', function (e) { if (e.target.classList.contains('social-platform')) syncIcon(e.target); });
    list.addEventListener('click', function (e) {
        var btn = e.target.closest('.social-remove'); if (!btn) return;
        btn.closest('.social-row').remove();
        refreshEmpty();
    });
})();
</script>
