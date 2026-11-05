<div class="card-soft">
    <div class="card-body">
        <h5 class="mb-3">Hero Section</h5>
        <div class="mb-3"><label class="form-label">Hero Title</label>
            <input type="text" name="hero_title" class="form-control" maxlength="255" value="{{ old('hero_title', $settings['hero_title']) }}"></div>
        <div class="mb-3"><label class="form-label">Hero Subtitle</label>
            <textarea name="hero_subtitle" class="form-control" rows="2" maxlength="500">{{ old('hero_subtitle', $settings['hero_subtitle']) }}</textarea></div>
    </div>
</div>

<div class="card-soft mt-4">
    <div class="card-body">
        <h5 class="mb-3">"Why Choose Us" Section</h5>
        <div class="mb-3"><label class="form-label">Section Title</label>
            <input type="text" name="home_why_title" class="form-control" maxlength="255" placeholder="Why guests choose us" value="{{ old('home_why_title', $settings['home_why_title']) }}"></div>
        <div class="mb-3"><label class="form-label">Section Subtitle</label>
            <textarea name="home_why_subtitle" class="form-control" rows="2" maxlength="500">{{ old('home_why_subtitle', $settings['home_why_subtitle']) }}</textarea></div>

        @for ($i = 1; $i <= 4; $i++)
            <div class="border rounded-3 p-3 mb-3">
                <div class="fw-semibold mb-2">Feature {{ $i }}</div>
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">Icon</label>
                        <input type="text" name="home_feature_{{ $i }}_icon" class="form-control" maxlength="60" placeholder="bi-cup-hot" value="{{ old('home_feature_'.$i.'_icon', $settings['home_feature_'.$i.'_icon']) }}">
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Title</label>
                        <input type="text" name="home_feature_{{ $i }}_title" class="form-control" maxlength="100" value="{{ old('home_feature_'.$i.'_title', $settings['home_feature_'.$i.'_title']) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="home_feature_{{ $i }}_desc" class="form-control" rows="2" maxlength="500">{{ old('home_feature_'.$i.'_desc', $settings['home_feature_'.$i.'_desc']) }}</textarea>
                    </div>
                </div>
            </div>
        @endfor
    </div>
</div>

<div class="card-soft mt-4">
    <div class="card-body">
        <h5 class="mb-3">"Popular Picks" Panel</h5>
        <div class="mb-3"><label class="form-label">Badge Text</label>
            <input type="text" name="home_featured_badge" class="form-control" maxlength="100" value="{{ old('home_featured_badge', $settings['home_featured_badge']) }}"></div>
        <div class="mb-3"><label class="form-label">Title</label>
            <input type="text" name="home_featured_title" class="form-control" maxlength="255" value="{{ old('home_featured_title', $settings['home_featured_title']) }}"></div>
        <div class="mb-3"><label class="form-label">Description</label>
            <textarea name="home_featured_text" class="form-control" rows="2" maxlength="500">{{ old('home_featured_text', $settings['home_featured_text']) }}</textarea></div>
    </div>
</div>

<div class="card-soft mt-4">
    <div class="card-body">
        <h5 class="mb-3">Guest Testimonial</h5>
        <div class="mb-3"><label class="form-label">Badge Text</label>
            <input type="text" name="home_testimonial_badge" class="form-control" maxlength="100" value="{{ old('home_testimonial_badge', $settings['home_testimonial_badge']) }}"></div>
        <div class="mb-3"><label class="form-label">Quote</label>
            <textarea name="home_testimonial_quote" class="form-control" rows="2" maxlength="500">{{ old('home_testimonial_quote', $settings['home_testimonial_quote']) }}</textarea></div>
        <div class="mb-3"><label class="form-label">Author</label>
            <input type="text" name="home_testimonial_author" class="form-control" maxlength="150" value="{{ old('home_testimonial_author', $settings['home_testimonial_author']) }}"></div>
    </div>
</div>

<div class="card-soft mt-4">
    <div class="card-body">
        <h5 class="mb-3">Opening Hours</h5>
        <div class="mb-3"><label class="form-label">Opening Hours</label>
            <textarea name="opening_hours" class="form-control" rows="3" maxlength="500" placeholder="e.g. Mon - Sun: 10:00 AM - 10:00 PM">{{ old('opening_hours', $settings['opening_hours']) }}</textarea></div>
    </div>
</div>
