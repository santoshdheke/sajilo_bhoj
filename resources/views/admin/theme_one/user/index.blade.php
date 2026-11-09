@extends('admin.theme_one.component.layout')

@section('content')
@php
    $roleColor = fn ($id) => ['#198754', '#3730a3', '#9f1239', '#0369a1', '#92400e', '#7c3aed'][$id % 6];
    $staffJson = $users->map(fn ($u) => [
        'id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'phone' => $u->phone,
        'role_id' => $u->role_id, 'role_name' => $u->role?->name, 'is_active' => $u->is_active,
        'joined' => $u->created_at?->format('d M Y'),
    ])->values();
    $rolesJson = $roles->map(fn ($r) => [
        'id' => $r->id, 'name' => $r->name, 'description' => $r->description, 'permissions' => $r->permissions ?? [],
        'is_owner' => $r->isOwner(), 'users_count' => $r->users_count,
    ])->values();
@endphp
<style>
.us-tabs        { display:inline-flex;gap:6px;padding:5px;border-radius:14px;background:color-mix(in srgb, var(--primary) 8%, white);margin-bottom:18px; }
.us-tab         { border:0;background:transparent;border-radius:10px;padding:8px 18px;font-weight:700;font-size:.9rem;color:color-mix(in srgb, var(--primary) 70%, #333);display:inline-flex;align-items:center;gap:7px; }
.us-tab.active  { background:#fff;color:var(--primary);box-shadow:0 2px 10px color-mix(in srgb, var(--primary) 18%, transparent); }
.us-tab .n      { font-size:.75rem;font-weight:700;padding:1px 8px;border-radius:999px;background:color-mix(in srgb, var(--primary) 12%, white); }
.us-pane        { display:none; }
.us-pane.active { display:block; }
.form-panel     { position:sticky;top:24px; }

.user-avatar  { width:42px;height:42px;border-radius:50%;font-weight:700;font-size:.9rem;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.role-badge   { display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:8px;font-size:.75rem;font-weight:600; }
.you-tag      { font-size:.68rem;font-weight:700;padding:1px 7px;border-radius:6px;background:color-mix(in srgb, var(--primary) 12%, white);color:var(--primary);margin-left:6px; }
.tbl-action   { width:32px;height:32px;border:none;border-radius:9px;font-size:.85rem;display:inline-flex;align-items:center;justify-content:center;transition:.12s; }
.tbl-action.edit { background:#eef0ff;color:#3730a3; }
.tbl-action.edit:hover { background:#3730a3;color:#fff; }
.tbl-action.del  { background:#fff0f0;color:#dc3545; }
.tbl-action.del:hover  { background:#dc3545;color:#fff; }
.tbl-action:disabled { opacity:.35;pointer-events:none; }
.avail-toggle { width:38px;height:21px;border-radius:999px;border:none;position:relative;transition:.2s;flex-shrink:0; }
.avail-toggle::after { content:'';position:absolute;top:3px;left:3px;width:15px;height:15px;border-radius:50%;background:#fff;transition:.2s; }
.avail-toggle.on  { background:var(--primary); }
.avail-toggle.on::after { left:20px; }
.avail-toggle.off { background:#ced4da; }
.avail-toggle:disabled { opacity:.45; }
tr.is-off td { opacity:.6; }

/* Roles */
.role-grid    { display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px; }
.role-card    { background:#fff;border-radius:18px;padding:18px;box-shadow:0 6px 20px rgba(15,40,25,.05);border:1px solid rgba(24,52,34,.06);display:flex;flex-direction:column;gap:10px; }
.role-card h6 { margin:0;font-weight:800;font-size:1.05rem;display:flex;align-items:center;gap:8px; }
.role-card .dot { width:10px;height:10px;border-radius:50%;flex-shrink:0; }
.role-card .desc { font-size:.84rem;color:var(--muted);margin:0; }
.role-card .perms { display:flex;flex-wrap:wrap;gap:5px; }
.role-card .perm { font-size:.72rem;font-weight:600;padding:3px 9px;border-radius:999px;background:color-mix(in srgb, var(--primary) 8%, white);color:color-mix(in srgb, var(--primary) 70%, #222); }
.role-card .foot { margin-top:auto;display:flex;align-items:center;justify-content:space-between;gap:8px;padding-top:10px;border-top:1px dashed rgba(24,52,34,.1);font-size:.82rem;color:var(--muted); }
.role-add     { border:2px dashed color-mix(in srgb, var(--primary) 30%, white);background:color-mix(in srgb, var(--primary) 4%, white);color:var(--primary);
                font-weight:700;border-radius:18px;min-height:170px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px; }
.role-add i   { font-size:1.6rem; }
.perm-list    { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px; }
.perm-item    { display:flex;align-items:center;gap:9px;padding:9px 12px;border-radius:12px;border:1px solid rgba(24,52,34,.1);cursor:pointer;font-size:.9rem;margin:0; }
.perm-item:has(input:checked) { border-color:color-mix(in srgb, var(--primary) 45%, white);background:color-mix(in srgb, var(--primary) 6%, white); }
.perm-item input { accent-color:var(--primary);width:16px;height:16px; }
.perm-item.locked { opacity:.65;cursor:default; }
.perm-item .plan-tag { margin-left:auto;font-size:.68rem;font-weight:700;padding:2px 8px;border-radius:999px;background:#fff4dc;color:#9a6700; }
@media (max-width:575px) { .perm-list { grid-template-columns:1fr; } .us-tabs { display:flex; } .us-tab { flex:1;justify-content:center; } }
</style>

{{-- ── Edit staff ── --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-gear me-2"></i>Edit Staff</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="editError" class="alert alert-danger d-none py-2 small"></div>
                <input type="hidden" id="editId">
                <div class="mb-3"><label class="form-label fw-semibold" for="editName">Full Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="editName" maxlength="100"></div>
                <div class="mb-3"><label class="form-label fw-semibold" for="editEmail">Email <span class="text-danger">*</span></label><input type="email" class="form-control" id="editEmail"></div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6"><label class="form-label fw-semibold" for="editRole">Role <span class="text-danger">*</span></label><select class="form-select role-select" id="editRole"></select></div>
                    <div class="col-sm-6"><label class="form-label fw-semibold" for="editPhone">Phone</label><input type="text" class="form-control" id="editPhone" maxlength="20" placeholder="+977…"></div>
                </div>
                <div><label class="form-label fw-semibold" for="editPassword">New Password <small class="text-muted fw-normal">(leave empty to keep)</small></label><input type="password" class="form-control" id="editPassword" placeholder="Min 8 characters" autocomplete="new-password"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="editSaveBtn"><i class="bi bi-check-lg me-1"></i>Update</button>
            </div>
        </div>
    </div>
</div>

{{-- ── Add / edit role ── --}}
<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-shield-check me-2"></i><span id="roleModalTitle">New Role</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="roleError" class="alert alert-danger d-none py-2 small"></div>
                <input type="hidden" id="roleId">
                <div class="row g-3 mb-3">
                    <div class="col-sm-5"><label class="form-label fw-semibold" for="roleName">Role Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="roleName" maxlength="60" placeholder="e.g. Cashier"></div>
                    <div class="col-sm-7"><label class="form-label fw-semibold" for="roleDesc">Description</label><input type="text" class="form-control" id="roleDesc" maxlength="255" placeholder="What this role does"></div>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <label class="form-label fw-semibold mb-0">Can access</label>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-soft btn-sm" id="permAll">Select all</button>
                        <button type="button" class="btn btn-soft btn-sm" id="permNone">Clear</button>
                    </div>
                </div>
                <div class="perm-list" id="permList">
                    @foreach($permissionLabels as $key => $label)
                    @php $locked = in_array($key, \App\Models\Role::ALWAYS, true); @endphp
                    <label class="perm-item {{ $locked ? 'locked' : '' }}" @if($locked) title="Everyone lands on the dashboard after signing in" @endif>
                        <input type="checkbox" value="{{ $key }}" {{ $locked ? 'checked disabled' : '' }}> {{ $label }}
                        @unless(auth()->user()->planAllows($key))<span class="plan-tag" title="Not in your current plan — upgrade to use it">Upgrade</span>@endunless
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="roleSaveBtn"><i class="bi bi-check-lg me-1"></i>Save Role</button>
            </div>
        </div>
    </div>
</div>

<div class="page-title mb-3">
    <h2>Users &amp; Roles</h2>
    <p>Add your staff, give each one a role, and choose what every role can open.</p>
</div>

<div class="us-tabs" role="tablist">
    <button type="button" class="us-tab {{ $tab === 'staff' ? 'active' : '' }}" data-tab="staff"><i class="bi bi-people"></i>Staff <span class="n" id="staffCount">{{ $users->count() }}</span></button>
    <button type="button" class="us-tab {{ $tab === 'roles' ? 'active' : '' }}" data-tab="roles"><i class="bi bi-shield-lock"></i>Roles <span class="n" id="roleCount">{{ $roles->count() }}</span></button>
</div>

{{-- ════════ Staff ════════ --}}
<div class="us-pane {{ $tab === 'staff' ? 'active' : '' }}" data-pane="staff">
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-soft form-panel">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-person-plus-fill" style="color:var(--primary)"></i>
                <h5 class="mb-0">Add Staff</h5>
            </div>
            <div class="card-body">
                <div id="addError" class="alert alert-danger d-none py-2 small"></div>
                <div class="mb-3"><label class="form-label fw-semibold" for="addName">Full Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="addName" maxlength="100" placeholder="Staff full name"></div>
                <div class="mb-3"><label class="form-label fw-semibold" for="addEmail">Email <span class="text-danger">*</span></label><input type="email" class="form-control" id="addEmail" placeholder="staff@restaurant.com"></div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6 col-lg-12 col-xl-6"><label class="form-label fw-semibold" for="addRole">Role <span class="text-danger">*</span></label><select class="form-select role-select" id="addRole"></select></div>
                    <div class="col-sm-6 col-lg-12 col-xl-6"><label class="form-label fw-semibold" for="addPhone">Phone</label><input type="text" class="form-control" id="addPhone" maxlength="20" placeholder="+977…"></div>
                </div>
                <div class="mb-4"><label class="form-label fw-semibold" for="addPassword">Password <span class="text-danger">*</span></label><input type="password" class="form-control" id="addPassword" placeholder="Min 8 characters" autocomplete="new-password"></div>
                <button class="btn btn-primary w-100" id="addSaveBtn"><i class="bi bi-person-plus me-1"></i>Create Account</button>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Staff List</h5>
                <div style="position:relative;">
                    <i class="bi bi-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#6c757d;font-size:.8rem;"></i>
                    <input class="form-control form-control-sm" id="userSearch" placeholder="Search name, email, role…" style="padding-left:30px;border-radius:10px;width:220px;max-width:100%;">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Staff</th><th>Role</th><th>Phone</th><th>Joined</th><th class="text-center">Active</th><th></th></tr></thead>
                        <tbody id="userTbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{{-- ════════ Roles ════════ --}}
<div class="us-pane {{ $tab === 'roles' ? 'active' : '' }}" data-pane="roles">
    <div class="role-grid" id="roleGrid"></div>
</div>

@push('js')
<script>
const CSRF   = '{{ csrf_token() }}';
const ME     = {{ auth()->id() }};
const LABELS = @json($permissionLabels);
const ALWAYS = @json(\App\Models\Role::ALWAYS);
const COLORS = ['#198754','#3730a3','#9f1239','#0369a1','#92400e','#7c3aed'];
let staff = @json($staffJson);
let roles = @json($rolesJson);

const _editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
const _roleModal = new bootstrap.Modal(document.getElementById('roleModal'));
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const initials = n => n.trim().split(/\s+/).map(w => w[0].toUpperCase()).slice(0, 2).join('');
const roleOf = id => roles.find(r => r.id === Number(id));
const color = id => COLORS[Number(id) % COLORS.length];

async function api(url, method, body) {
    let res;
    try {
        res = await fetch(url, { method, headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF}, body: body ? JSON.stringify(body) : undefined });
    } catch (e) { return { ok: false, error: 'Network error. Please check your connection.' }; }
    const data = await res.json().catch(() => ({}));
    if (res.ok) return { ok: true, data };
    const error = res.status === 419 ? 'Your session expired. Please refresh the page.'
        : Object.values(data.errors || {})[0]?.[0] || data.message || 'Something went wrong. Please try again.';
    return { ok: false, error };
}
function showErr(id, msg) { const el = document.getElementById(id); el.textContent = msg || ''; el.classList.toggle('d-none', !msg); }
function busy(btn, on, html) { btn.disabled = on; if (on) { btn.dataset.html = btn.innerHTML; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…'; } else btn.innerHTML = html || btn.dataset.html; }

/* ── Tabs (?tab=staff | ?tab=roles) ── */
document.querySelectorAll('.us-tab').forEach(t => t.addEventListener('click', () => {
    document.querySelectorAll('.us-tab').forEach(x => x.classList.toggle('active', x === t));
    document.querySelectorAll('.us-pane').forEach(p => p.classList.toggle('active', p.dataset.pane === t.dataset.tab));
    const url = new URL(location.href); url.searchParams.set('tab', t.dataset.tab); history.replaceState(null, '', url);
}));

/* ════════ Staff ════════ */
function fillRoleSelects() {
    document.querySelectorAll('.role-select').forEach(sel => {
        const keep = sel.value;
        sel.innerHTML = roles.map(r => `<option value="${r.id}">${esc(r.name)}</option>`).join('');
        const fallback = roles.find(r => r.name === 'Waiter') || roles.find(r => !r.is_owner) || roles[0];
        sel.value = roleOf(keep) ? keep : (fallback?.id ?? '');
    });
}

function renderStaff() {
    const q = document.getElementById('userSearch').value.trim().toLowerCase();
    const list = staff.filter(u => !q || [u.name, u.email, u.role_name || ''].some(v => v.toLowerCase().includes(q)));
    document.getElementById('staffCount').textContent = staff.length;
    document.getElementById('userTbody').innerHTML = list.length ? list.map(u => {
        const c = color(u.id), r = roleOf(u.role_id), me = u.id === ME;
        return `<tr data-id="${u.id}" class="${u.is_active ? '' : 'is-off'}">
            <td><div class="d-flex align-items-center gap-3">
                <div class="user-avatar" style="background:${c}22;color:${c};">${esc(initials(u.name))}</div>
                <div style="min-width:0;"><div class="fw-semibold" style="font-size:.9rem;">${esc(u.name)}${me ? '<span class="you-tag">You</span>' : ''}</div>
                <div class="text-muted" style="font-size:.75rem;">${esc(u.email)}</div></div></div></td>
            <td><span class="role-badge" style="background:${color(u.role_id)}18;color:${color(u.role_id)};">${r?.is_owner ? '<i class="bi bi-star-fill"></i>' : ''}${esc(u.role_name || '—')}</span></td>
            <td class="text-muted" style="font-size:.83rem;">${esc(u.phone || '—')}</td>
            <td class="text-muted" style="font-size:.82rem;">${esc(u.joined || '')}</td>
            <td class="text-center"><button class="avail-toggle ${u.is_active ? 'on' : 'off'}" data-act="toggle" ${me ? 'disabled title="You can’t deactivate yourself"' : `title="${u.is_active ? 'Deactivate' : 'Activate'}"`}></button></td>
            <td><div class="d-flex gap-1 justify-content-end">
                <button class="tbl-action edit" data-act="edit" title="Edit"><i class="bi bi-pencil"></i></button>
                <button class="tbl-action del" data-act="delete" title="Delete" ${me ? 'disabled' : ''}><i class="bi bi-trash"></i></button>
            </div></td></tr>`;
    }).join('') : `<tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>${staff.length ? 'No staff match your search.' : 'No staff accounts yet.'}</td></tr>`;
}

document.getElementById('userSearch').addEventListener('input', renderStaff);

document.getElementById('addSaveBtn').addEventListener('click', async e => {
    const saveBtn = e.currentTarget; // e.currentTarget is gone after the await
    const body = {
        name: addName.value.trim(), email: addEmail.value.trim(), role_id: addRole.value,
        phone: addPhone.value.trim() || null, password: addPassword.value,
    };
    showErr('addError');
    if (!body.name || !body.email || !body.password) return showErr('addError', 'Name, email and password are required.');
    busy(saveBtn, true);
    const r = await api('/admin/user', 'POST', body);
    busy(saveBtn, false);
    if (!r.ok) return showErr('addError', r.error);
    staff.unshift(r.data); bumpRoleCount(r.data.role_id, 1);
    ['addName','addEmail','addPhone','addPassword'].forEach(id => document.getElementById(id).value = '');
    renderStaff(); renderRoles();
    notify.success(`${r.data.name} was added as ${r.data.role_name}.`);
});

document.getElementById('userTbody').addEventListener('click', async e => {
    const btn = e.target.closest('[data-act]'); if (!btn) return;
    const u = staff.find(x => x.id === Number(btn.closest('tr').dataset.id));

    if (btn.dataset.act === 'edit') {
        showErr('editError');
        editId.value = u.id; editName.value = u.name; editEmail.value = u.email;
        editPhone.value = u.phone || ''; editPassword.value = ''; fillRoleSelects(); editRole.value = u.role_id;
        editRole.disabled = u.id === ME; editRole.title = u.id === ME ? 'You can’t change your own role' : '';
        _editModal.show();
    }
    if (btn.dataset.act === 'toggle') {
        const r = await api(`/admin/user/${u.id}/toggle`, 'PATCH');
        if (!r.ok) return notify.error(r.error);
        u.is_active = r.data.is_active; renderStaff();
    }
    if (btn.dataset.act === 'delete') {
        if (!confirm(`Delete the account of "${u.name}"? This can’t be undone.`)) return;
        const r = await api(`/admin/user/${u.id}`, 'DELETE');
        if (!r.ok) return notify.error(r.error);
        staff = staff.filter(x => x.id !== u.id); bumpRoleCount(u.role_id, -1);
        renderStaff(); renderRoles();
    }
});

document.getElementById('editSaveBtn').addEventListener('click', async e => {
    const saveBtn = e.currentTarget; // e.currentTarget is gone after the await
    const id = Number(editId.value), old = staff.find(x => x.id === id);
    const body = { name: editName.value.trim(), email: editEmail.value.trim(), role_id: editRole.value,
                   phone: editPhone.value.trim() || null, password: editPassword.value || null };
    showErr('editError');
    if (!body.name || !body.email) return showErr('editError', 'Name and email are required.');
    busy(saveBtn, true);
    const r = await api(`/admin/user/${id}`, 'PUT', body);
    busy(saveBtn, false);
    if (!r.ok) return showErr('editError', r.error);
    if (old.role_id !== r.data.role_id) { bumpRoleCount(old.role_id, -1); bumpRoleCount(r.data.role_id, 1); }
    Object.assign(old, r.data);
    _editModal.hide(); renderStaff(); renderRoles();
});

/* ════════ Roles ════════ */
function bumpRoleCount(roleId, by) { const r = roleOf(roleId); if (r) r.users_count = Math.max(0, r.users_count + by); }

function renderRoles() {
    document.getElementById('roleCount').textContent = roles.length;
    document.getElementById('roleGrid').innerHTML = roles.map(r => {
        const perms = r.is_owner ? '<span class="perm">Everything</span>'
            : r.permissions.filter(p => LABELS[p]).map(p => `<span class="perm">${esc(LABELS[p])}</span>`).join('');
        return `<div class="role-card" data-id="${r.id}">
            <h6><span class="dot" style="background:${color(r.id)}"></span>${esc(r.name)}${r.is_owner ? ' <i class="bi bi-star-fill" style="color:#f5a524;font-size:.85rem" title="Full access"></i>' : ''}</h6>
            ${r.description ? `<p class="desc">${esc(r.description)}</p>` : ''}
            <div class="perms">${perms}</div>
            <div class="foot"><span><i class="bi bi-people me-1"></i>${r.users_count} staff</span>
                ${r.is_owner ? '<span class="small">Full access · locked</span>' : `<div class="d-flex gap-1">
                    <button class="tbl-action edit" data-act="edit-role" title="Edit role"><i class="bi bi-pencil"></i></button>
                    <button class="tbl-action del" data-act="delete-role" title="Delete role"><i class="bi bi-trash"></i></button></div>`}
            </div></div>`;
    }).join('') + `<button type="button" class="role-add" data-act="new-role"><i class="bi bi-plus-circle"></i>New Role</button>`;
    fillRoleSelects();
}

function openRole(r) {
    showErr('roleError');
    roleId.value = r?.id || ''; roleName.value = r?.name || ''; roleDesc.value = r?.description || '';
    document.getElementById('roleModalTitle').textContent = r ? `Edit “${r.name}”` : 'New Role';
    const has = new Set(r ? r.permissions : ALWAYS);
    document.querySelectorAll('#permList input:not(:disabled)').forEach(cb => cb.checked = has.has(cb.value));
    _roleModal.show();
}

document.getElementById('roleGrid').addEventListener('click', async e => {
    const btn = e.target.closest('[data-act]'); if (!btn) return;
    if (btn.dataset.act === 'new-role') return openRole(null);
    const r = roleOf(btn.closest('.role-card').dataset.id);
    if (btn.dataset.act === 'edit-role') return openRole(r);
    if (btn.dataset.act === 'delete-role') {
        if (!confirm(`Delete the role "${r.name}"?`)) return;
        const res = await api(`/admin/roles/${r.id}`, 'DELETE');
        if (!res.ok) return notify.error(res.error);
        roles = roles.filter(x => x.id !== r.id); renderRoles();
    }
});

document.getElementById('permAll').addEventListener('click', () => document.querySelectorAll('#permList input:not(:disabled)').forEach(cb => cb.checked = true));
document.getElementById('permNone').addEventListener('click', () => document.querySelectorAll('#permList input:not(:disabled)').forEach(cb => cb.checked = false));

document.getElementById('roleSaveBtn').addEventListener('click', async e => {
    const saveBtn = e.currentTarget; // e.currentTarget is gone after the await
    const id = roleId.value;
    const body = { name: roleName.value.trim(), description: roleDesc.value.trim() || null,
                   permissions: [...document.querySelectorAll('#permList input:checked')].map(cb => cb.value) };
    showErr('roleError');
    if (!body.name) return showErr('roleError', 'Please give the role a name.');
    busy(saveBtn, true);
    const r = await api(id ? `/admin/roles/${id}` : '/admin/roles', id ? 'PUT' : 'POST', body);
    busy(saveBtn, false);
    if (!r.ok) return showErr('roleError', r.error);
    const role = { id: r.data.id, name: r.data.name, description: r.data.description, permissions: r.data.permissions,
                   is_owner: false, users_count: r.data.users_count };
    if (id) {
        Object.assign(roleOf(id), role);
        staff.forEach(u => { if (u.role_id === role.id) u.role_name = role.name; });
    } else roles.push(role);
    _roleModal.hide(); renderRoles(); renderStaff();
    notify.success(id ? 'Role updated.' : `Role "${role.name}" created.`);
});

renderRoles();
renderStaff();
</script>
@endpush
@endsection
