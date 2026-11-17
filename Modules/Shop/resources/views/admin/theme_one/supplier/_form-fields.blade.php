{{-- Supplier fields; $supplier may be null (add form). --}}
<div class="col-12"><label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
    <input class="form-control" name="name" value="{{ old('name', $supplier?->name) }}" required maxlength="255" placeholder="Contact person or business"></div>
<div class="col-md-6"><label class="form-label fw-semibold">Phone</label>
    <input class="form-control" name="phone" value="{{ old('phone', $supplier?->phone) }}" maxlength="20"></div>
<div class="col-md-6"><label class="form-label fw-semibold">Email</label>
    <input class="form-control" type="email" name="email" value="{{ old('email', $supplier?->email) }}" maxlength="255"></div>
<div class="col-md-6"><label class="form-label fw-semibold">Company</label>
    <input class="form-control" name="company_name" value="{{ old('company_name', $supplier?->company_name) }}" maxlength="255"></div>
<div class="col-md-6"><label class="form-label fw-semibold">PAN / VAT</label>
    <input class="form-control" name="pan_vat" value="{{ old('pan_vat', $supplier?->pan_vat) }}" maxlength="50"></div>
<div class="col-12"><label class="form-label fw-semibold">Address</label>
    <input class="form-control" name="address" value="{{ old('address', $supplier?->address) }}" maxlength="255"></div>
<div class="col-12"><label class="form-label fw-semibold">Notes</label>
    <textarea class="form-control" name="notes" rows="2" maxlength="1000">{{ old('notes', $supplier?->notes) }}</textarea></div>
