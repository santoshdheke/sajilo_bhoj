@php
    $v = fn ($key) => old($key, $settings[$key]);
    $iconChoices = ['bi-eye', 'bi-bullseye', 'bi-heart', 'bi-star', 'bi-award', 'bi-gem', 'bi-flower1', 'bi-shield-check', 'bi-people',
                    'bi-emoji-smile', 'bi-cup-hot', 'bi-egg-fried', 'bi-lightning-charge', 'bi-globe2', 'bi-hand-thumbs-up', 'bi-trophy'];
    $sections = [
        'ab-banner' => ['bi-window',          'Page banner'],
        'ab-story'  => ['bi-book',            'Our story'],
        'ab-stats'  => ['bi-graph-up-arrow',  'Numbers'],
        'ab-values' => ['bi-stars',           'Values'],
        'ab-cta'    => ['bi-megaphone',       'Call to action'],
    ];
@endphp

<div class="hp-edit">
    <nav class="hp-nav d-none d-lg-block" aria-label="About page sections">
        <div class="hp-nav-title">Sections</div>
        @foreach ($sections as $id => [$icon, $label])
            <a href="#{{ $id }}" class="{{ $loop->first ? 'active' : '' }}"><i class="bi {{ $icon }}"></i>{{ $label }}</a>
        @endforeach
        <a href="{{ route('frontend.about.index') }}" target="_blank" rel="noopener" class="hp-nav-view"><i class="bi bi-box-arrow-up-right"></i>View about page</a>
    </nav>

    <div class="hp-forms">
        {{-- ── Banner ── --}}
        <section class="hp-card" id="ab-banner">
            <header>
                <span class="hp-ico"><i class="bi bi-window"></i></span>
                <div><h5>Page banner</h5><small>The title area at the top of the About page.</small></div>
            </header>
            <label class="form-label" for="about_banner_subtitle">Subtitle</label>
            <input type="text" id="about_banner_subtitle" name="about_banner_subtitle" class="form-control" maxlength="255" data-count
                   value="{{ $v('about_banner_subtitle') }}" placeholder="{{ $settings['tagline'] ?? '' ?: 'A restaurant with a fresh, modern and welcoming identity.' }}">
            <div class="hp-help">The banner title is “About {{ $brandName ?? 'your restaurant' }}”. Leave the subtitle empty to use your tagline.</div>
        </section>

        {{-- ── Story ── --}}
        <section class="hp-card" id="ab-story">
            <header>
                <span class="hp-ico"><i class="bi bi-book"></i></span>
                <div><h5>Our story</h5><small>Shown next to the cooking chef illustration.</small></div>
            </header>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="about_badge">Badge</label>
                    <input type="text" id="about_badge" name="about_badge" class="form-control" maxlength="60" value="{{ $v('about_badge') }}" placeholder="Our Story">
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="about_title">Title</label>
                    <input type="text" id="about_title" name="about_title" class="form-control" maxlength="255" data-count value="{{ $v('about_title') }}">
                </div>
                <div class="col-12">
                    <label class="form-label" for="about_text">Story</label>
                    <textarea id="about_text" name="about_text" class="form-control" rows="6" maxlength="5000" data-count
                              placeholder="Tell guests how your restaurant started, what you cook and what makes you different…">{{ $v('about_text') }}</textarea>
                    <div class="hp-help">Line breaks are kept. Leave empty to show a short default introduction.</div>
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label mb-2">Highlights <span class="text-secondary fw-normal">(shown with ✓ ticks — leave one empty to hide it)</span></label>
                <div class="row g-2">
                    @for ($i = 1; $i <= 4; $i++)
                        <div class="col-md-6">
                            <div class="ab-point"><i class="bi bi-check-circle-fill"></i>
                                <input type="text" name="about_point_{{ $i }}" class="form-control" maxlength="150" value="{{ $v("about_point_{$i}") }}" aria-label="Highlight {{ $i }}">
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </section>

        {{-- ── Numbers ── --}}
        <section class="hp-card" id="ab-stats">
            <header>
                <span class="hp-ico"><i class="bi bi-graph-up-arrow"></i></span>
                <div><h5>Numbers</h5><small>A strip of three highlights, e.g. “50+ Dishes on the menu”.</small></div>
            </header>
            <div class="ab-stats">
                @for ($i = 1; $i <= 3; $i++)
                    <div class="ab-stat">
                        <input type="text" name="about_stat_{{ $i }}_value" class="form-control ab-stat-value" maxlength="20" value="{{ $v("about_stat_{$i}_value") }}" aria-label="Number {{ $i }}">
                        <input type="text" name="about_stat_{{ $i }}_label" class="form-control" maxlength="60" value="{{ $v("about_stat_{$i}_label") }}" aria-label="Label {{ $i }}">
                    </div>
                @endfor
            </div>
        </section>

        {{-- ── Values ── --}}
        <section class="hp-card" id="ab-values">
            <header>
                <span class="hp-ico"><i class="bi bi-stars"></i></span>
                <div><h5>Values</h5><small>Three cards — e.g. vision, mission and promise.</small></div>
            </header>
            <datalist id="abIcons">@foreach ($iconChoices as $ic)<option value="{{ $ic }}">@endforeach</datalist>
            <div class="hp-features ab-values">
                @for ($i = 1; $i <= 3; $i++)
                    @php $icon = $v("about_value_{$i}_icon") ?: 'bi-star'; @endphp
                    <div class="hp-feature">
                        <div class="hp-feature-head">
                            <span class="hp-feature-ico" id="abIco{{ $i }}"><i class="bi {{ $icon }}"></i></span>
                            <span class="hp-feature-no">Card {{ $i }}</span>
                        </div>
                        <label class="form-label" for="abIcon{{ $i }}">Icon</label>
                        <input type="text" id="abIcon{{ $i }}" name="about_value_{{ $i }}_icon" class="form-control hp-icon-input mb-2" maxlength="60"
                               list="abIcons" data-preview="abIco{{ $i }}" value="{{ $v("about_value_{$i}_icon") }}">
                        <label class="form-label" for="abTitle{{ $i }}">Title</label>
                        <input type="text" id="abTitle{{ $i }}" name="about_value_{{ $i }}_title" class="form-control mb-2" maxlength="100" value="{{ $v("about_value_{$i}_title") }}">
                        <label class="form-label" for="abDesc{{ $i }}">Description</label>
                        <textarea id="abDesc{{ $i }}" name="about_value_{{ $i }}_desc" class="form-control" rows="3" maxlength="500" data-count>{{ $v("about_value_{$i}_desc") }}</textarea>
                    </div>
                @endfor
            </div>
        </section>

        {{-- ── Call to action ── --}}
        <section class="hp-card" id="ab-cta">
            <header>
                <span class="hp-ico"><i class="bi bi-megaphone"></i></span>
                <div><h5>Call to action</h5><small>The coloured band at the bottom, with “View menu” and “Book a table” buttons.</small></div>
            </header>
            <div class="mb-3">
                <label class="form-label" for="about_cta_title">Title</label>
                <input type="text" id="about_cta_title" name="about_cta_title" class="form-control" maxlength="255" data-count value="{{ $v('about_cta_title') }}">
            </div>
            <label class="form-label" for="about_cta_text">Text</label>
            <textarea id="about_cta_text" name="about_cta_text" class="form-control" rows="2" maxlength="500" data-count>{{ $v('about_cta_text') }}</textarea>
        </section>
    </div>
</div>

<style>
    .ab-point { position: relative; }
    .ab-point .bi { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--primary); }
    .ab-point .form-control { padding-left: 40px; }
    .ab-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .ab-stat { border: 1px solid var(--line); border-radius: 14px; padding: 14px; background: color-mix(in srgb, var(--primary) 3%, white); display: flex; flex-direction: column; gap: 8px; }
    .ab-stat-value { font-size: 1.4rem; font-weight: 800; color: var(--primary); text-align: center; }
    .ab-stat .form-control:not(.ab-stat-value) { text-align: center; }
    .hp-features.ab-values { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    @media (max-width: 991px) { .hp-features.ab-values { grid-template-columns: 1fr; } }
    @media (max-width: 575px) { .ab-stats { grid-template-columns: 1fr; } }
</style>

@include('admin.theme_one.setting.partials._editor-assets')
