@php $v = fn ($key) => old($key, $settings[$key]); @endphp

<div class="card-soft">
    <div class="card-body">
        <h5 class="mb-3">Our Story</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Badge</label>
                <input type="text" name="about_badge" class="form-control" maxlength="60" value="{{ $v('about_badge') }}">
            </div>
            <div class="col-md-8">
                <label class="form-label">Title</label>
                <input type="text" name="about_title" class="form-control" maxlength="255" value="{{ $v('about_title') }}">
            </div>
            <div class="col-12">
                <label class="form-label">About Text</label>
                <textarea name="about_text" class="form-control" rows="6" maxlength="5000">{{ $v('about_text') }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="card-soft mt-4">
    <div class="card-body">
        <h5 class="mb-3">Highlights</h5>
        <div class="row g-3">
            @for ($i = 1; $i <= 4; $i++)
                <div class="col-md-6">
                    <label class="form-label">Point {{ $i }}</label>
                    <input type="text" name="about_point_{{ $i }}" class="form-control" maxlength="150" value="{{ $v("about_point_{$i}") }}">
                </div>
            @endfor
        </div>
    </div>
</div>

<div class="card-soft mt-4">
    <div class="card-body">
        <h5 class="mb-3">Vision, Mission &amp; Promise</h5>
        @for ($i = 1; $i <= 3; $i++)
            <div class="border rounded-3 p-3 mb-3">
                <div class="fw-semibold mb-2">Card {{ $i }}</div>
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">Icon</label>
                        <input type="text" name="about_value_{{ $i }}_icon" class="form-control" maxlength="60" placeholder="bi-heart" value="{{ $v("about_value_{$i}_icon") }}">
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Title</label>
                        <input type="text" name="about_value_{{ $i }}_title" class="form-control" maxlength="100" value="{{ $v("about_value_{$i}_title") }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="about_value_{{ $i }}_desc" class="form-control" rows="2" maxlength="500">{{ $v("about_value_{$i}_desc") }}</textarea>
                    </div>
                </div>
            </div>
        @endfor
    </div>
</div>
