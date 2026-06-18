@extends('admin.theme_one.component.layout')

@section('content')
<style>
.user-avatar { width:42px;height:42px;border-radius:50%;font-weight:700;font-size:.9rem;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.role-badge  { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:8px;font-size:.75rem;font-weight:600; }
.role-admin  { background:#fde7ea;color:#9f1239; }
.role-manager{ background:#eef0ff;color:#3730a3; }
.role-waiter { background:#edfaf3;color:#065f46; }
.role-kitchen{ background:#fff8e8;color:#92400e; }
.role-cashier{ background:#e0f2fe;color:#0369a1; }
.role-default{ background:#f1f5f9;color:#475569; }
.tbl-action  { width:30px;height:30px;border:none;border-radius:9px;font-size:.82rem;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:.12s; }
.tbl-action.edit { background:#eef0ff;color:#3730a3; }
.tbl-action.edit:hover { background:#3730a3;color:#fff; }
.tbl-action.del  { background:#fff0f0;color:#dc3545; }
.tbl-action.del:hover  { background:#dc3545;color:#fff; }
.avail-toggle { width:38px;height:21px;border-radius:999px;border:none;cursor:pointer;position:relative;transition:.2s;flex-shrink:0; }
.avail-toggle::after { content:'';position:absolute;top:3px;left:3px;width:15px;height:15px;border-radius:50%;background:#fff;transition:.2s; }
.avail-toggle.on { background:var(--primary); }
.avail-toggle.on::after { left:20px; }
.avail-toggle.off { background:#ced4da; }
.form-panel  { position:sticky;top:24px; }
</style>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-gear me-2"></i>Edit Staff</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="mb-3"><label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="editName"></div>
                <div class="mb-3"><label class="form-label fw-semibold">Email <span class="text-danger">*</span></label><input type="email" class="form-control" id="editEmail"></div>
                <div class="row g-3">
                    <div class="col-sm-6"><label class="form-label fw-semibold">Role</label><select class="form-select" id="editRole">@foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select></div>
                    <div class="col-sm-6"><label class="form-label fw-semibold">Phone</label><input type="text" class="form-control" id="editPhone" placeholder="+977…"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="editSaveBtn"><i class="bi bi-check-lg me-1"></i>Update</button>
            </div>
        </div>
    </div>
</div>

<div class="page-title mb-4">
    <h2>Users &amp; Staff</h2>
    <p>Manage admin, manager, waiter, and kitchen staff accounts.</p>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-soft form-panel">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-person-plus-fill text-success"></i>
                <h5 class="mb-0">Add Staff</h5>
            </div>
            <div class="card-body">
                <div id="addError" class="alert alert-danger d-none py-2" style="font-size:.85rem;"></div>
                <div class="mb-3"><label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="addName" placeholder="Staff full name"></div>
                <div class="mb-3"><label class="form-label fw-semibold">Email <span class="text-danger">*</span></label><input type="email" class="form-control" id="addEmail" placeholder="staff@restopro.com"></div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6"><label class="form-label fw-semibold">Role</label><select class="form-select" id="addRole">@foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select></div>
                    <div class="col-sm-6"><label class="form-label fw-semibold">Phone</label><input type="text" class="form-control" id="addPhone" placeholder="+977…"></div>
                </div>
                <div class="mb-4"><label class="form-label fw-semibold">Password <span class="text-danger">*</span></label><input type="password" class="form-control" id="addPassword" placeholder="Min 8 characters"></div>
                <button class="btn btn-primary w-100" id="addSaveBtn"><i class="bi bi-person-plus me-1"></i>Create Account</button>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-soft table-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Staff List <span class="text-muted fw-normal" style="font-size:.85rem;">({{ $users->count() }})</span></h5>
                <div style="position:relative;">
                    <i class="bi bi-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#6c757d;font-size:.8rem;"></i>
                    <input class="form-control form-control-sm" id="userSearch" placeholder="Search…" style="padding-left:30px;border-radius:10px;width:180px;">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Staff</th><th>Role</th><th>Contact</th><th>Joined</th><th class="text-center">Active</th><th></th></tr></thead>
                        <tbody id="userTbody">
                            @forelse($users as $u)
                            @php
                                $ini    = collect(explode(' ',$u->name))->map(fn($w)=>strtoupper(substr($w,0,1)))->take(2)->implode('');
                                $clrs   = ['#198754','#3730a3','#9f1239','#0369a1','#92400e'];
                                $clr    = $clrs[$u->id % count($clrs)];
                                $roleName = $u->role->name ?? '—';
                                $rcls   = match(true){str_contains($roleName,'Manager')=>'role-manager',str_contains($roleName,'Waiter')=>'role-waiter',str_contains($roleName,'Kitchen')=>'role-kitchen',str_contains($roleName,'Cashier')=>'role-cashier',str_contains(strtolower($roleName),'admin')=>'role-admin',default=>'role-default'};
                            @endphp
                            <tr data-id="{{ $u->id }}" data-name="{{ $u->name }}" data-email="{{ $u->email }}" data-role-id="{{ $u->role_id }}" data-role-name="{{ $roleName }}" data-phone="{{ $u->phone ?? '' }}">
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="user-avatar" style="background:{{ $clr }}22;color:{{ $clr }};">{{ $ini }}</div>
                                        <div>
                                            <div class="fw-semibold" style="font-size:.9rem;">{{ $u->name }}</div>
                                            <div class="text-muted" style="font-size:.75rem;">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="role-badge {{ $rcls }}">{{ $roleName }}</span></td>
                                <td class="text-muted" style="font-size:.83rem;">{{ $u->phone ?? '—' }}</td>
                                <td class="text-muted" style="font-size:.82rem;">{{ $u->created_at->format('d M Y') }}</td>
                                <td class="text-center"><button class="avail-toggle {{ $u->is_active ? 'on' : 'off' }}" onclick="toggleUser({{ $u->id }},this)"></button></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="tbl-action edit" onclick="openEdit(this.closest('tr'))"><i class="bi bi-pencil"></i></button>
                                        <button class="tbl-action del"  onclick="deleteUser({{ $u->id }},'{{ addslashes($u->name) }}')"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRow"><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>No staff accounts yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
const CSRF='{{ csrf_token() }}';
const _editModal=new bootstrap.Modal(document.getElementById('editUserModal'));
const COLORS=['#198754','#3730a3','#9f1239','#0369a1','#92400e'];
function initials(n){return n.trim().split(/\s+/).map(w=>w[0].toUpperCase()).slice(0,2).join('');}
function rcls(role){const r=role||'';if(r.includes('Manager'))return 'role-manager';if(r.includes('Waiter'))return 'role-waiter';if(r.includes('Kitchen'))return 'role-kitchen';if(r.includes('Cashier'))return 'role-cashier';if(r.toLowerCase().includes('admin'))return 'role-admin';return 'role-default';}

document.getElementById('addSaveBtn').addEventListener('click',async()=>{
    const name=document.getElementById('addName').value.trim(),email=document.getElementById('addEmail').value.trim();
    const roleId=document.getElementById('addRole').value,phone=document.getElementById('addPhone').value.trim();
    const pass=document.getElementById('addPassword').value;
    const err=document.getElementById('addError');err.classList.add('d-none');
    if(!name||!email||!pass){err.textContent='Name, email and password are required.';err.classList.remove('d-none');return;}
    const btn=document.getElementById('addSaveBtn');btn.disabled=true;btn.innerHTML='<span class="spinner-border spinner-border-sm me-1"></span>Saving…';
    const res=await fetch('/admin/user',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({name,email,role_id:roleId,phone:phone||null,password:pass})});
    const data=await res.json();btn.disabled=false;btn.innerHTML='<i class="bi bi-person-plus me-1"></i>Create Account';
    if(!res.ok){err.textContent=Object.values(data.errors||{})[0]?.[0]||'Error.';err.classList.remove('d-none');return;}
    document.getElementById('emptyRow')?.remove();
    const color=COLORS[data.id%COLORS.length];
    const roleName=data.role?.name||'—';
    const tr=document.createElement('tr');tr.dataset.id=data.id;tr.dataset.name=data.name;tr.dataset.email=data.email;tr.dataset.roleId=data.role_id;tr.dataset.roleName=roleName;tr.dataset.phone=data.phone||'';
    tr.innerHTML=`<td><div class="d-flex align-items-center gap-3"><div class="user-avatar" style="background:${color}22;color:${color};">${initials(data.name)}</div><div><div class="fw-semibold" style="font-size:.9rem;">${data.name}</div><div class="text-muted" style="font-size:.75rem;">${data.email}</div></div></div></td><td><span class="role-badge ${rcls(roleName)}">${roleName}</span></td><td class="text-muted" style="font-size:.83rem;">${data.phone||'—'}</td><td class="text-muted" style="font-size:.82rem;">Just now</td><td class="text-center"><button class="avail-toggle on" onclick="toggleUser(${data.id},this)"></button></td><td><div class="d-flex gap-1"><button class="tbl-action edit" onclick="openEdit(this.closest('tr'))"><i class="bi bi-pencil"></i></button><button class="tbl-action del" onclick="deleteUser(${data.id},'${data.name.replace(/'/g,"\\'")}')"><i class="bi bi-trash"></i></button></div></td>`;
    document.getElementById('userTbody').insertBefore(tr,document.getElementById('userTbody').firstChild);
    ['addName','addEmail','addPhone','addPassword'].forEach(id=>document.getElementById(id).value='');
});

function openEdit(row){document.getElementById('editId').value=row.dataset.id;document.getElementById('editName').value=row.dataset.name;document.getElementById('editEmail').value=row.dataset.email;document.getElementById('editRole').value=row.dataset.roleId;document.getElementById('editPhone').value=row.dataset.phone||'';_editModal.show();}
document.getElementById('editSaveBtn').addEventListener('click',async()=>{
    const id=document.getElementById('editId').value,name=document.getElementById('editName').value.trim();
    const email=document.getElementById('editEmail').value.trim(),roleId=document.getElementById('editRole').value;
    const phone=document.getElementById('editPhone').value.trim();if(!name||!email)return;
    const btn=document.getElementById('editSaveBtn');btn.disabled=true;btn.innerHTML='<span class="spinner-border spinner-border-sm me-1"></span>';
    const res=await fetch(`/admin/user/${id}`,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({name,email,role_id:roleId,phone:phone||null})});
    const data=await res.json();btn.disabled=false;btn.innerHTML='<i class="bi bi-check-lg me-1"></i>Update';
    if(res.ok){_editModal.hide();const row=document.querySelector(`tr[data-id="${id}"]`);if(row){const roleName=data.role?.name||'—';row.dataset.name=name;row.dataset.email=email;row.dataset.roleId=data.role_id;row.dataset.roleName=roleName;row.dataset.phone=phone;const c=COLORS[parseInt(id)%COLORS.length];row.cells[0].innerHTML=`<div class="d-flex align-items-center gap-3"><div class="user-avatar" style="background:${c}22;color:${c};">${initials(name)}</div><div><div class="fw-semibold" style="font-size:.9rem;">${name}</div><div class="text-muted" style="font-size:.75rem;">${email}</div></div></div>`;row.cells[1].innerHTML=`<span class="role-badge ${rcls(roleName)}">${roleName}</span>`;row.cells[2].textContent=phone||'—';}}
});
function toggleUser(id,btn){fetch(`/admin/user/${id}/toggle`,{method:'PATCH',headers:{'X-CSRF-TOKEN':CSRF}}).then(r=>r.json()).then(d=>{btn.classList.toggle('on',d.is_active);btn.classList.toggle('off',!d.is_active);});}
function deleteUser(id,name){if(!confirm(`Delete account for "${name}"?`))return;fetch(`/admin/user/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF}}).then(()=>{document.querySelector(`tr[data-id="${id}"]`)?.remove();if(!document.querySelector('#userTbody tr[data-id]')){document.getElementById('userTbody').innerHTML='<tr id="emptyRow"><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.35;"></i>No staff accounts yet.</td></tr>';}});}
document.getElementById('userSearch').addEventListener('input',function(){const q=this.value.toLowerCase();document.querySelectorAll('#userTbody tr[data-id]').forEach(r=>{r.style.display=(r.dataset.name.toLowerCase().includes(q)||r.dataset.email.toLowerCase().includes(q)||r.dataset.roleName.toLowerCase().includes(q))?'':'none';});});
</script>
@endpush
@endsection
