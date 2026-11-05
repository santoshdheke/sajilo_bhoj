<div class="card-soft">
    <div class="card-body">
        <h5 class="mb-3">Contact Details</h5>
        <div class="mb-3"><label class="form-label">Email</label>
            <input type="email" name="contact_email" class="form-control" maxlength="255" value="{{ old('contact_email', $settings['contact_email']) }}"></div>
        <div class="mb-3"><label class="form-label">Phone</label>
            <input type="text" name="contact_phone" class="form-control" maxlength="50" value="{{ old('contact_phone', $settings['contact_phone']) }}"></div>
        <div class="mb-3"><label class="form-label">Address</label>
            <textarea name="contact_address" class="form-control" rows="2" maxlength="500">{{ old('contact_address', $settings['contact_address']) }}</textarea></div>
    </div>
</div>
