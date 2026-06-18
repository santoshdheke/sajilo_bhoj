@extends('admin.theme_one.component.layout')

@section('content')
    <div class="page-title mb-4">
        <h2>Settings</h2>
        <p>Restaurant branding, contact details, and public site content. These power the storefront pages directly.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-lg-6">
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

                <div class="card-soft mt-4">
                    <div class="card-body">
                        <h5 class="mb-3">Theme</h5>
                        <label class="form-label">Brand / Primary Color</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="color" name="theme_color" class="form-control form-control-color @error('theme_color') is-invalid @enderror"
                                   style="width:64px;height:48px;padding:.35rem" value="{{ old('theme_color', $settings['theme_color']) }}">
                            <span class="text-secondary">Used for buttons, links, and highlights across your dashboard.</span>
                        </div>
                    </div>
                </div>

                <div class="card-soft mt-4">
                    <div class="card-body">
                        <h5 class="mb-3">Contact Details</h5>
                        <div class="mb-3"><label class="form-label">Email</label>
                            <input type="email" name="contact_email" class="form-control" maxlength="255" value="{{ old('contact_email', $settings['contact_email']) }}"></div>
                        <div class="mb-3"><label class="form-label">Phone</label>
                            <input type="text" name="contact_phone" class="form-control" maxlength="50" value="{{ old('contact_phone', $settings['contact_phone']) }}"></div>
                        <div class="mb-3"><label class="form-label">Address</label>
                            <textarea name="contact_address" class="form-control" rows="2" maxlength="500">{{ old('contact_address', $settings['contact_address']) }}</textarea></div>
                        <div class="mb-3"><label class="form-label">Opening Hours</label>
                            <textarea name="opening_hours" class="form-control" rows="2" maxlength="500" placeholder="e.g. Mon - Sun: 10:00 AM - 10:00 PM">{{ old('opening_hours', $settings['opening_hours']) }}</textarea></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card-soft">
                    <div class="card-body">
                        <h5 class="mb-3">Homepage Hero</h5>
                        <div class="mb-3"><label class="form-label">Hero Title</label>
                            <input type="text" name="hero_title" class="form-control" maxlength="255" value="{{ old('hero_title', $settings['hero_title']) }}"></div>
                        <div class="mb-3"><label class="form-label">Hero Subtitle</label>
                            <textarea name="hero_subtitle" class="form-control" rows="2" maxlength="500">{{ old('hero_subtitle', $settings['hero_subtitle']) }}</textarea></div>
                    </div>
                </div>

                <div class="card-soft mt-4">
                    <div class="card-body">
                        <h5 class="mb-3">About Page</h5>
                        <div class="mb-3"><label class="form-label">About Text</label>
                            <textarea name="about_text" class="form-control" rows="5" maxlength="5000">{{ old('about_text', $settings['about_text']) }}</textarea></div>
                    </div>
                </div>

                <div class="card-soft mt-4">
                    <div class="card-body">
                        <h5 class="mb-3">Social Links</h5>
                        <div class="mb-3"><label class="form-label"><i class="bi bi-facebook me-1"></i>Facebook URL</label>
                            <input type="text" name="facebook_url" class="form-control" maxlength="255" value="{{ old('facebook_url', $settings['facebook_url']) }}"></div>
                        <div class="mb-3"><label class="form-label"><i class="bi bi-instagram me-1"></i>Instagram URL</label>
                            <input type="text" name="instagram_url" class="form-control" maxlength="255" value="{{ old('instagram_url', $settings['instagram_url']) }}"></div>
                        <div class="mb-3"><label class="form-label"><i class="bi bi-twitter-x me-1"></i>Twitter / X URL</label>
                            <input type="text" name="twitter_url" class="form-control" maxlength="255" value="{{ old('twitter_url', $settings['twitter_url']) }}"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Settings</button>
        </div>
    </form>
@endsection
