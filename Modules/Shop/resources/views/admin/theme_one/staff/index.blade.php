@extends('shop::admin.theme_one.component.layout')
@section('title', 'Staff')

@section('content')
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit staff member</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body row g-3">
                <div class="col-12"><label class="form-label fw-semibold">Name</label><input class="form-control" name="name" id="eName" required maxlength="255"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Email</label><input class="form-control" type="email" name="email" id="eEmail" required></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Phone</label><input class="form-control" name="phone_number" id="ePhone" maxlength="20"></div>
                <div class="col-12"><label class="form-label fw-semibold">Address</label><input class="form-control" name="address" id="eAddress" maxlength="255"></div>
                <div class="col-12" id="eStatusBox"><div class="form-check form-switch"><input type="hidden" name="status" value="0">
                    <input class="form-check-input" type="checkbox" name="status" value="1" id="eStatus"><label class="form-check-label" for="eStatus">Active (can sign in)</label></div></div>
                <div class="col-md-6"><label class="form-label fw-semibold">New password</label><input class="form-control" type="password" name="password" minlength="8" autocomplete="new-password" placeholder="Leave empty to keep"></div>
                <div class="col-md-6"><label class="form-label fw-semibold">Confirm</label><input class="form-control" type="password" name="password_confirmation" autocomplete="new-password"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
            </div>
        </form>
    </div>
</div>

<div class="page-title mb-4"><h2>Staff</h2><p>People who can use the shop back office. New staff set their own password from the invite email.</p></div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-soft form-panel">
            <div class="card-header d-flex align-items-center gap-2"><i class="bi bi-person-plus text-primary"></i><h5 class="mb-0">Add staff member</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('shop.admin.staff.store') }}">
                    @csrf
                    <div class="mb-3"><label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input class="form-control" name="name" value="{{ old('name') }}" required maxlength="255"></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input class="form-control" type="email" name="email" value="{{ old('email') }}" required></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Phone</label>
                        <input class="form-control" name="phone_number" value="{{ old('phone_number') }}" maxlength="20"></div>
                    <div class="mb-4"><label class="form-label fw-semibold">Address</label>
                        <input class="form-control" name="address" value="{{ old('address') }}" maxlength="255"></div>
                    <button class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Add staff member</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header"><h5 class="mb-0">Team <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $staff->count() }})</span></h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Name</th><th>Contact</th><th>Role</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            @foreach ($staff as $member)
                                @php($pending = ! $member->email_verified_at)
                                <tr>
                                    <td class="fw-semibold">{{ $member->name }}@if ($member->id === auth('shop')->id()) <span class="text-muted small">(you)</span>@endif</td>
                                    <td class="small">{{ $member->email }}<div class="text-muted">{{ $member->phone_number }}</div></td>
                                    <td><span class="pill {{ $member->isOwner() ? 'pill-purple' : 'pill-blue' }}">{{ $member->role_label }}</span></td>
                                    <td>
                                        @if ($pending)<span class="pill pill-amber">Invite pending</span>
                                        @elseif ($member->status)<span class="pill pill-green">Active</span>
                                        @else<span class="pill pill-gray">Inactive</span>@endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if ($pending && ! $member->isOwner())
                                                <form method="POST" action="{{ route('shop.admin.staff.send_invite', $member) }}">@csrf
                                                    <button class="tbl-action view" title="Send invite email"><i class="bi bi-envelope-paper"></i></button></form>
                                            @endif
                                            <button type="button" class="tbl-action edit" title="Edit" data-url="{{ route('shop.admin.staff.update', $member) }}"
                                                data-name="{{ $member->name }}" data-email="{{ $member->email }}" data-phone="{{ $member->phone_number }}" data-address="{{ $member->address }}"
                                                data-status="{{ (int) $member->status }}" data-locked="{{ (int) ($member->isOwner() || $member->id === auth('shop')->id()) }}"><i class="bi bi-pencil"></i></button>
                                            @unless ($member->isOwner() || $member->id === auth('shop')->id())
                                                <form method="POST" action="{{ route('shop.admin.staff.destroy', $member) }}" data-confirm="Remove {{ $member->name }} from the staff?">@csrf @method('DELETE')
                                                    <button class="tbl-action del" title="Delete"><i class="bi bi-trash"></i></button></form>
                                            @endunless
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.querySelectorAll('.tbl-action.edit').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var d = btn.dataset;
        document.getElementById('editForm').action = d.url;
        document.getElementById('eName').value = d.name;
        document.getElementById('eEmail').value = d.email;
        document.getElementById('ePhone').value = d.phone || '';
        document.getElementById('eAddress').value = d.address || '';
        document.getElementById('eStatus').checked = d.status === '1';
        document.getElementById('eStatusBox').classList.toggle('d-none', d.locked === '1');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal')).show();
    });
});
</script>
@endpush
