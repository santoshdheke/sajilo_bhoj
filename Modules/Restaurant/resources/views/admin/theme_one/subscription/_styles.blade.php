{{-- Shared look of the Balance & Subscription pages (balance, top-up, Refer & Earn). Colours follow the theme (--primary). --}}
<style>
.tp-grid      { display:grid; grid-template-columns:minmax(0,1.15fr) minmax(0,1fr); gap:22px; align-items:start; }
.tp-balance   { position:relative; overflow:hidden; padding:26px 28px; border-radius:22px; color:#fff; min-height:150px;
                background:linear-gradient(135deg, color-mix(in srgb, var(--primary) 88%, white), color-mix(in srgb, var(--primary) 62%, black));
                box-shadow:0 14px 30px color-mix(in srgb, var(--primary) 25%, transparent); }
.tp-balance.expired { background:linear-gradient(135deg, #e5484d, #8f1d2a); box-shadow:0 14px 30px rgba(220,53,69,.25); }
.tp-balance .lbl { opacity:.88; font-size:.95rem; }
.tp-balance .amt { font-size:2.5rem; font-weight:800; letter-spacing:-.02em; margin:2px 0 10px; }
.tp-balance .sub { opacity:.92; font-size:.88rem; display:flex; align-items:center; gap:8px; max-width:78%; }
.tp-balance .wallet { position:absolute; right:26px; top:50%; transform:translateY(-50%) rotate(-8deg); font-size:5.4rem; line-height:1;
                      color:rgba(255,255,255,.22); filter:drop-shadow(0 6px 10px rgba(0,0,0,.12)); }
.tp-facts     { display:grid; grid-template-columns:minmax(0,1.35fr) minmax(0,1fr) minmax(0,1fr); gap:14px; margin-top:16px; }
.tp-fact > div { min-width:0; }
.tp-fact      { display:flex; align-items:center; gap:12px; background:#fff; border-radius:16px; padding:14px; box-shadow:0 6px 18px rgba(15,40,25,.05); }
.tp-ic        { width:46px; height:46px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:1.2rem;
                color:var(--primary); background:color-mix(in srgb, var(--primary) 12%, white); }
.tp-fact small { display:block; color:var(--muted); font-size:.78rem; }
.tp-fact b    { color:#14201a; font-size:.98rem; }
.tp-fact a    { color:var(--primary); font-weight:600; font-size:.85rem; text-decoration:none; margin-left:6px; white-space:nowrap; display:inline-block; }
.tp-card      { background:#fff; border-radius:20px; box-shadow:0 8px 24px rgba(15,40,25,.06); }
.tp-card-head { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:18px 22px; border-bottom:1px solid rgba(24,52,34,.07); }
.tp-card-head h5 { margin:0; font-weight:800; display:flex; align-items:center; gap:10px; }
.tp-card-head h5 i { color:var(--primary); font-size:1.3rem; }
.tp-card-head a { color:var(--primary); font-weight:700; font-size:.9rem; text-decoration:none; }
.tp-card-body { padding:6px 22px 18px; }
.tp-tx        { display:flex; align-items:center; gap:14px; padding:14px 0; border-bottom:1px solid rgba(24,52,34,.07); }
.tp-tx:last-child { border-bottom:0; }
.tp-tx .ic    { width:42px; height:42px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:1.1rem; }
.tp-tx .ic.topup  { background:color-mix(in srgb, var(--primary) 12%, white); color:var(--primary); }
.tp-tx .ic.charge { background:#f2f4f3; color:#6b7280; }
.tp-tx .ic.bonus  { background:#fff4dc; color:#b7791f; }
.tp-tx .t     { font-weight:700; color:#14201a; font-size:.95rem; }
.tp-tx .m     { color:var(--muted); font-size:.8rem; }
.tp-tx .amt   { margin-left:auto; font-weight:800; white-space:nowrap; }
.tp-tx .amt.topup  { color:var(--primary); }
.tp-tx .amt.charge { color:#475569; }
.tp-tx .amt.bonus  { color:#b7791f; }
.tp-presets   { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; margin:16px 0; }
.tp-preset    { border:1.5px solid rgba(24,52,34,.1); background:#fff; border-radius:14px; padding:12px 16px; text-align:left; transition:.15s; }
.tp-preset:hover, .tp-preset.on { border-color:var(--primary); background:color-mix(in srgb, var(--primary) 5%, white); }
.tp-preset b  { display:block; font-size:1.08rem; color:#14201a; }
.tp-preset small { color:var(--muted); font-size:.88rem; }
.tp-gw        { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; margin-bottom:16px; }
.tp-gw label  { display:flex; align-items:center; gap:10px; padding:12px 14px; border-radius:14px; border:1.5px solid rgba(24,52,34,.1); cursor:pointer; margin:0; transition:.15s; }
.tp-gw input  { accent-color:var(--primary); width:17px; height:17px; }
.tp-gw label:has(input:checked) { border-color:var(--primary); background:color-mix(in srgb, var(--primary) 5%, white); }
.tp-gw .logo  { font-weight:800; font-size:1.05rem; letter-spacing:-.01em; }
.tp-gw .logo.khalti { color:#5c2d91; }
.tp-gw .logo.esewa  { color:#41a124; }
.tp-gw .logo.esewa span { color:#1d1d1d; }
.tp-note      { display:flex; align-items:flex-start; gap:8px; color:var(--muted); font-size:.84rem; margin-top:8px; }
.tp-btn       { width:100%; border:0; border-radius:16px; padding:15px; font-weight:700; font-size:1.02rem; color:#fff; margin-top:18px;
                background:linear-gradient(135deg, color-mix(in srgb, var(--primary) 90%, white), color-mix(in srgb, var(--primary) 75%, black));
                box-shadow:0 10px 22px color-mix(in srgb, var(--primary) 28%, transparent); display:flex; align-items:center; justify-content:center; gap:10px; }
.tp-btn:hover { filter:brightness(1.05); }
.tp-btn:disabled { opacity:.6; }
.tp-promo     { display:flex; align-items:center; gap:14px; padding:18px 20px; border-radius:20px; text-decoration:none; color:inherit;
                background:linear-gradient(135deg, color-mix(in srgb, var(--primary) 9%, white), #fff);
                border:1.5px dashed color-mix(in srgb, var(--primary) 35%, white); transition:.15s; }
.tp-promo:hover { border-style:solid; box-shadow:0 10px 22px color-mix(in srgb, var(--primary) 14%, transparent); color:inherit; }
.tp-promo-ic  { width:48px; height:48px; border-radius:14px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:1.35rem;
                color:#fff; background:linear-gradient(135deg, color-mix(in srgb, var(--primary) 90%, white), color-mix(in srgb, var(--primary) 70%, black)); }
.tp-promo-text { min-width:0; }
.tp-promo-text b { display:block; color:#14201a; font-size:1rem; }
.tp-promo-text small { color:var(--muted); font-size:.84rem; }
.tp-promo-go  { margin-left:auto; color:var(--primary); font-weight:700; font-size:.88rem; white-space:nowrap; }
.tp-status    { display:inline-flex; align-items:center; gap:5px; font-size:.72rem; font-weight:700; padding:3px 9px; border-radius:999px; white-space:nowrap; }
.tp-status.trial  { background:#e0f2fe; color:#0369a1; }
.tp-status.active { background:color-mix(in srgb, var(--primary) 12%, white); color:var(--primary); }
.tp-status.paused { background:#fff0f0; color:#dc3545; }
.tp-status.new    { background:#f2f4f3; color:#6b7280; }
@media (max-width:991px) { .tp-grid { grid-template-columns:1fr; } }
@media (max-width:575px) {
    .tp-facts { grid-template-columns:1fr; }
    .tp-balance .wallet { font-size:4rem; right:16px; }
    .tp-balance .sub { max-width:100%; }
    .tp-balance .amt { font-size:2rem; }
    .tp-promo { flex-wrap:wrap; }
    .tp-promo-go { margin-left:62px; }
}
</style>
