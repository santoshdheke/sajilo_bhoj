{{-- Shared look + behaviour for long settings forms (section menu, cards, icon preview, counters).
     Uses the .hp-* classes. Include once per page. --}}
@once
<style>
    .hp-edit { display: grid; grid-template-columns: 210px minmax(0, 1fr); gap: 22px; align-items: start; }
    .hp-nav { position: sticky; top: 16px; background: #fff; border-radius: 16px; padding: 14px; box-shadow: 0 8px 26px rgba(15, 40, 25, .06); }
    .hp-nav-title { font-size: .72rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); padding: 2px 10px 8px; }
    .hp-nav a { display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-radius: 10px; color: #1c2620; font-weight: 500; font-size: .92rem; }
    .hp-nav a i { color: var(--primary); width: 18px; text-align: center; }
    .hp-nav a:hover { background: color-mix(in srgb, var(--primary) 7%, white); }
    .hp-nav a.active { background: color-mix(in srgb, var(--primary) 12%, white); color: color-mix(in srgb, var(--primary) 70%, black); font-weight: 600; }
    .hp-nav .hp-nav-view { margin-top: 8px; border-top: 1px solid var(--line); border-radius: 0 0 10px 10px; padding-top: 12px; color: var(--primary); font-weight: 600; }

    .hp-forms { display: flex; flex-direction: column; gap: 18px; min-width: 0; }
    .hp-card { background: #fff; border-radius: 18px; padding: 22px 24px; box-shadow: 0 8px 26px rgba(15, 40, 25, .06); scroll-margin-top: 16px; }
    .hp-card > header, .hp-more-toggle { display: flex; align-items: center; gap: 14px; margin-bottom: 18px; }
    .hp-card h5 { margin: 0; font-size: 1.1rem; font-weight: 700; color: #14201a; }
    .hp-card header small, .hp-more-toggle small { color: var(--muted); font-size: .85rem; }
    .hp-ico { width: 42px; height: 42px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;
              background: color-mix(in srgb, var(--primary) 12%, white); color: var(--primary); }
    .hp-help { font-size: .8rem; color: var(--muted); margin-top: 5px; }
    .hp-help a { color: var(--primary); font-weight: 600; }
    .hp-count { float: right; font-size: .72rem; color: var(--muted); margin-top: 4px; }
    .hp-count.warn { color: #b45309; }

    .hp-features { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .hp-feature { border: 1px solid var(--line); border-radius: 14px; padding: 14px; background: color-mix(in srgb, var(--primary) 2%, white); }
    .hp-feature-head { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
    .hp-feature-ico { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;
                      background: color-mix(in srgb, var(--primary) 14%, white); color: var(--primary); }
    .hp-feature-no { font-weight: 700; font-size: .85rem; color: #1c2620; }

    .hp-more-toggle { width: 100%; border: 0; background: none; padding: 0; margin: 0; }
    .hp-more .hp-chevron { transition: transform .2s; color: var(--muted); }
    .hp-more-toggle[aria-expanded="true"] .hp-chevron { transform: rotate(180deg); }

    @media (max-width: 991px) { .hp-edit { grid-template-columns: 1fr; } }
    @media (max-width: 767px) { .hp-features { grid-template-columns: 1fr; } .hp-card { padding: 18px; } }
</style>

<script>
(function () {
    // Live icon preview for feature icons
    document.querySelectorAll('.hp-icon-input').forEach(function (input) {
        input.addEventListener('input', function () {
            var name = input.value.trim().replace(/^bi\s+/, '');
            if (name && name.indexOf('bi-') !== 0) name = 'bi-' + name;
            document.getElementById(input.dataset.preview).innerHTML = '<i class="bi ' + (name || 'bi-star') + '"></i>';
        });
    });

    // Character counters
    document.querySelectorAll('.hp-edit [data-count]').forEach(function (el) {
        var max = parseInt(el.getAttribute('maxlength'), 10), out = document.createElement('div');
        out.className = 'hp-count';
        el.insertAdjacentElement('afterend', out);
        function update() { var n = el.value.length; out.textContent = n + ' / ' + max; out.classList.toggle('warn', n > max * .9); }
        el.addEventListener('input', update); update();
    });

    // Highlight the section in view
    var links = document.querySelectorAll('.hp-nav a[href^="#"]');
    if ('IntersectionObserver' in window && links.length) {
        var obs = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (!en.isIntersecting) return;
                links.forEach(function (a) { a.classList.toggle('active', a.getAttribute('href') === '#' + en.target.id); });
            });
        }, { rootMargin: '-30% 0px -60% 0px' });
        document.querySelectorAll('.hp-card[id]').forEach(function (s) { obs.observe(s); });
    }
})();
</script>
@endonce
