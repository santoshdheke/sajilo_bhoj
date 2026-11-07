@php
    $v = fn ($key) => old($key, $settings[$key]);
    $iconChoices = ['bi-cup-hot', 'bi-egg-fried', 'bi-basket2', 'bi-alarm', 'bi-lightning-charge', 'bi-emoji-smile', 'bi-heart', 'bi-star',
                    'bi-calendar-check', 'bi-truck', 'bi-shield-check', 'bi-award', 'bi-flower1', 'bi-gem', 'bi-people', 'bi-geo-alt', 'bi-wifi', 'bi-music-note-beamed'];
    $sections = [
        'hp-hero'     => ['bi-stars',          'Hero'],
        'hp-why'      => ['bi-hand-thumbs-up', 'Why choose us'],
        'hp-featured' => ['bi-fire',           'Best sellers'],
        'hp-hours'    => ['bi-clock',          'Opening hours'],
        'hp-more'     => ['bi-three-dots',     'More'],
    ];
@endphp

<div class="hp-edit">
    {{-- Section menu --}}
    <nav class="hp-nav d-none d-lg-block" aria-label="Homepage sections">
        <div class="hp-nav-title">Sections</div>
        @foreach ($sections as $id => [$icon, $label])
            <a href="#{{ $id }}" class="{{ $loop->first ? 'active' : '' }}"><i class="bi {{ $icon }}"></i>{{ $label }}</a>
        @endforeach
        <a href="{{ route('frontend.home.index') }}" target="_blank" rel="noopener" class="hp-nav-view"><i class="bi bi-box-arrow-up-right"></i>View home page</a>
    </nav>

    <div class="hp-forms">
        {{-- ── Hero ── --}}
        <section class="hp-card" id="hp-hero">
            <header>
                <span class="hp-ico"><i class="bi bi-stars"></i></span>
                <div><h5>Hero</h5><small>Shown at the top of the home page.</small></div>
            </header>
            <div class="mb-3">
                <label class="form-label" for="hero_title">Headline</label>
                <input type="text" id="hero_title" name="hero_title" class="form-control" maxlength="255" data-count
                       value="{{ $v('hero_title') }}" placeholder="Delicious food, smooth service, and a premium dining experience.">
                <div class="hp-help">Leave empty to use the default headline with the highlighted words.</div>
            </div>
            <div>
                <label class="form-label" for="hero_subtitle">Intro text</label>
                <textarea id="hero_subtitle" name="hero_subtitle" class="form-control" rows="3" maxlength="500" data-count
                          placeholder="Your restaurant brings you authentic flavours prepared fresh every day…">{{ $v('hero_subtitle') }}</textarea>
            </div>
        </section>

        {{-- ── Why choose us ── --}}
        <section class="hp-card" id="hp-why">
            <header>
                <span class="hp-ico"><i class="bi bi-hand-thumbs-up"></i></span>
                <div><h5>Why choose us</h5><small>The four feature cards under the hero.</small></div>
            </header>
            <div class="mb-4">
                <label class="form-label" for="home_why_title">Section title</label>
                <input type="text" id="home_why_title" name="home_why_title" class="form-control" maxlength="255" data-count
                       value="{{ $v('home_why_title') }}" placeholder="Why guests choose {{ $brandName ?? 'us' }}">
            </div>

            <datalist id="hpIcons">
                @foreach ($iconChoices as $ic)<option value="{{ $ic }}">@endforeach
            </datalist>

            <div class="hp-features">
                @for ($i = 1; $i <= 4; $i++)
                    @php $icon = $v("home_feature_{$i}_icon") ?: 'bi-star'; @endphp
                    <div class="hp-feature">
                        <div class="hp-feature-head">
                            <span class="hp-feature-ico" id="hpIco{{ $i }}"><i class="bi {{ $icon }}"></i></span>
                            <span class="hp-feature-no">Feature {{ $i }}</span>
                        </div>
                        <div class="row g-2">
                            <div class="col-sm-5">
                                <label class="form-label" for="hpIcon{{ $i }}">Icon</label>
                                <input type="text" id="hpIcon{{ $i }}" name="home_feature_{{ $i }}_icon" class="form-control hp-icon-input" maxlength="60"
                                       list="hpIcons" data-preview="hpIco{{ $i }}" value="{{ $v("home_feature_{$i}_icon") }}" placeholder="bi-star">
                            </div>
                            <div class="col-sm-7">
                                <label class="form-label" for="hpTitle{{ $i }}">Title</label>
                                <input type="text" id="hpTitle{{ $i }}" name="home_feature_{{ $i }}_title" class="form-control" maxlength="100"
                                       value="{{ $v("home_feature_{$i}_title") }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="hpDesc{{ $i }}">Description</label>
                                <textarea id="hpDesc{{ $i }}" name="home_feature_{{ $i }}_desc" class="form-control" rows="3" maxlength="500" data-count>{{ $v("home_feature_{$i}_desc") }}</textarea>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
            <div class="hp-help mt-2"><i class="bi bi-info-circle me-1"></i>Icons use <a href="https://icons.getbootstrap.com/" target="_blank" rel="noopener">Bootstrap Icons</a> names, e.g. <code>bi-cup-hot</code>. Pick from the list or type your own.</div>
        </section>

        {{-- ── Best sellers ── --}}
        <section class="hp-card" id="hp-featured">
            <header>
                <span class="hp-ico"><i class="bi bi-fire"></i></span>
                <div><h5>Best sellers</h5><small>The coloured "best sellers" card next to the featured dish.</small></div>
            </header>
            <div class="mb-3">
                <label class="form-label" for="home_featured_title">Title</label>
                <input type="text" id="home_featured_title" name="home_featured_title" class="form-control" maxlength="255" data-count
                       value="{{ $v('home_featured_title') }}" placeholder="Best sellers from our kitchen">
            </div>
            <div>
                <label class="form-label" for="home_featured_text">Description</label>
                <textarea id="home_featured_text" name="home_featured_text" class="form-control" rows="2" maxlength="500" data-count
                          placeholder="A selection of our most loved dishes.">{{ $v('home_featured_text') }}</textarea>
            </div>
        </section>

        {{-- ── Opening hours ── --}}
        <section class="hp-card" id="hp-hours">
            <header>
                <span class="hp-ico"><i class="bi bi-clock"></i></span>
                <div><h5>Opening hours</h5><small>Shown on the Contact page. One line per day range.</small></div>
            </header>
            <textarea id="opening_hours" name="opening_hours" class="form-control" rows="3" maxlength="500" data-count
                      placeholder="Sunday - Friday: 10:00 AM - 10:00 PM&#10;Saturday: 11:00 AM - 11:00 PM">{{ $v('opening_hours') }}</textarea>
        </section>

        {{-- ── More (not on the current design) ── --}}
        <section class="hp-card hp-more" id="hp-more">
            <button class="hp-more-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#hpMoreBody" aria-expanded="false" aria-controls="hpMoreBody">
                <span class="hp-ico"><i class="bi bi-three-dots"></i></span>
                <div class="text-start"><h5>More</h5><small>Extra text kept for other layouts — not shown on the current home design.</small></div>
                <i class="bi bi-chevron-down ms-auto hp-chevron"></i>
            </button>
            <div class="collapse" id="hpMoreBody">
                <div class="pt-3">
                    <div class="mb-3">
                        <label class="form-label" for="home_why_subtitle">"Why choose us" subtitle</label>
                        <textarea id="home_why_subtitle" name="home_why_subtitle" class="form-control" rows="2" maxlength="500" data-count>{{ $v('home_why_subtitle') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="home_featured_badge">Best sellers badge</label>
                        <input type="text" id="home_featured_badge" name="home_featured_badge" class="form-control" maxlength="100" value="{{ $v('home_featured_badge') }}">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="home_testimonial_badge">Testimonial badge</label>
                            <input type="text" id="home_testimonial_badge" name="home_testimonial_badge" class="form-control" maxlength="100" value="{{ $v('home_testimonial_badge') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="home_testimonial_author">Testimonial author</label>
                            <input type="text" id="home_testimonial_author" name="home_testimonial_author" class="form-control" maxlength="150" value="{{ $v('home_testimonial_author') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="home_testimonial_quote">Testimonial quote</label>
                            <textarea id="home_testimonial_quote" name="home_testimonial_quote" class="form-control" rows="2" maxlength="500" data-count>{{ $v('home_testimonial_quote') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

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
