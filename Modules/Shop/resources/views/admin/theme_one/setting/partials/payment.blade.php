<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-soft h-100">
            <div class="card-body">
                <h5 class="mb-1"><i class="bi bi-qr-code me-2 text-primary"></i>QR payments</h5>
                <p class="text-secondary mb-4">Customers scan a QR made from your ID, pay in their app, then upload a screenshot. Verify it on the order page.</p>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-semibold">eSewa ID</label>
                        <input class="form-control" name="esewa_id" value="{{ old('esewa_id', $settings->esewa_id) }}" maxlength="100" placeholder="98XXXXXXXX"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">eSewa account name</label>
                        <input class="form-control" name="esewa_name" value="{{ old('esewa_name', $settings->esewa_name) }}" maxlength="100"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Khalti ID</label>
                        <input class="form-control" name="khalti_id" value="{{ old('khalti_id', $settings->khalti_id) }}" maxlength="100" placeholder="98XXXXXXXX"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Khalti account name</label>
                        <input class="form-control" name="khalti_name" value="{{ old('khalti_name', $settings->khalti_name) }}" maxlength="100"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-soft h-100">
            <div class="card-body">
                <h5 class="mb-1"><i class="bi bi-lightning-charge me-2 text-primary"></i>Khalti online payment</h5>
                <p class="text-secondary mb-4">Customers pay on Khalti and the order is marked paid automatically. Use the secret key from your Khalti merchant dashboard.</p>
                <div class="row g-3">
                    <div class="col-12"><label class="form-label fw-semibold">Secret key</label>
                        <input class="form-control" type="password" name="khalti_secret_key" autocomplete="new-password" placeholder="{{ $settings->khalti_secret_key ? '•••••••• saved — type to replace' : 'live_secret_key_… or test key' }}">
                        @if ($settings->khalti_secret_key)
                            <div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="remove_khalti_key" value="1" id="removeKey"><label class="form-check-label small" for="removeKey">Remove the saved key (turns off Khalti online payment)</label></div>
                        @endif
                    </div>
                    <div class="col-12"><label class="form-label fw-semibold">Mode</label>
                        <select class="form-select" name="khalti_env">
                            <option value="test" @selected(old('khalti_env', $settings->khalti_env) === 'test')>Test (sandbox, no real money)</option>
                            <option value="live" @selected(old('khalti_env', $settings->khalti_env) === 'live')>Live (real payments)</option>
                        </select></div>
                    <div class="col-12"><span class="pill {{ $settings->khalti_secret_key ? 'pill-green' : 'pill-gray' }}">{{ $settings->khalti_secret_key ? 'Khalti online payment is on' : 'Khalti online payment is off' }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
