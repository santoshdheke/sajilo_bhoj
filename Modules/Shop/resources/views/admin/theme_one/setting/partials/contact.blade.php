<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-soft h-100">
            <div class="card-body">
                <h5 class="mb-1">Contact</h5>
                <p class="text-secondary mb-4">Shown in the store's top bar and footer.</p>
                <div class="row g-3">
                    <div class="col-12"><label class="form-label fw-semibold">Phone</label>
                        <input class="form-control" name="phone" value="{{ old('phone', $company->phone) }}" maxlength="30"></div>
                    <div class="col-12"><label class="form-label fw-semibold">Email</label>
                        <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email', $company->email) }}"></div>
                    <div class="col-12"><label class="form-label fw-semibold">Address</label>
                        <input class="form-control" name="address" value="{{ old('address', $company->address) }}" maxlength="255"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-soft h-100">
            <div class="card-body">
                <h5 class="mb-1">Social links</h5>
                <p class="text-secondary mb-4">Shown as icons in the store footer. A WhatsApp link (e.g. https://wa.me/9779800000000) also adds a chat button.</p>
                <div class="row g-3">
                    @foreach (\Modules\Shop\Models\ShopSetting::SOCIAL as $platform => $meta)
                        <div class="col-md-6"><label class="form-label fw-semibold"><i class="bi {{ $meta['bi'] }} me-1"></i>{{ $meta['label'] }}</label>
                            <input class="form-control" type="url" name="social[{{ $platform }}]" value="{{ old('social.'.$platform, $settings->social_links[$platform] ?? '') }}" placeholder="https://"></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
