{{-- Customer fields; $customer may be null (add form). --}}
<div class="col-md-6"><label class="form-label fw-semibold">First name <span class="text-danger">*</span></label>
    <input class="form-control" name="first_name" value="{{ old('first_name', $customer?->first_name) }}" required maxlength="100"></div>
<div class="col-md-6"><label class="form-label fw-semibold">Last name</label>
    <input class="form-control" name="last_name" value="{{ old('last_name', $customer?->last_name) }}" maxlength="100"></div>
<div class="col-md-6"><label class="form-label fw-semibold">Phone</label>
    <input class="form-control" name="phone_number" value="{{ old('phone_number', $customer?->phone_number) }}" maxlength="20"></div>
<div class="col-md-6"><label class="form-label fw-semibold">Email</label>
    <input class="form-control" type="email" name="email" value="{{ old('email', $customer?->email) }}" maxlength="255"></div>
<div class="col-md-6"><label class="form-label fw-semibold">Address</label>
    <input class="form-control" name="address_one" value="{{ old('address_one', $customer?->address_one) }}" maxlength="255"></div>
<div class="col-md-6"><label class="form-label fw-semibold">Address 2</label>
    <input class="form-control" name="address_two" value="{{ old('address_two', $customer?->address_two) }}" maxlength="255"></div>
<div class="col-12"><label class="form-label fw-semibold">Notes</label>
    <textarea class="form-control" name="notes" rows="2" maxlength="1000">{{ old('notes', $customer?->notes) }}</textarea></div>
