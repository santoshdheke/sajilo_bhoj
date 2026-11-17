{{-- Shared styles for the split auth pages (workspace sign-up / staff login). Expects --primary set on :root. --}}
<style>
        :root { --ink: #14201a; --muted: #4b5a51; --orange: #f5a524; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; color: var(--ink);
               background: color-mix(in srgb, var(--primary) 6%, #f7fbf8); display: flex; align-items: center; justify-content: center; padding: 32px 16px; overflow-x: hidden; }

        /* page decoration */
        .bg-blob { position: fixed; border-radius: 50%; background: color-mix(in srgb, var(--primary) 12%, white); z-index: 0; pointer-events: none; }
        .bg-blob.b1 { width: 520px; height: 520px; left: -260px; top: -240px; }
        .bg-blob.b2 { width: 460px; height: 460px; right: -230px; bottom: -200px; }
        .sparks { position: fixed; top: 54px; right: 9%; width: 70px; z-index: 0; color: var(--primary); animation: spark 2.4s ease-in-out infinite; }
        @keyframes spark { 0%, 100% { opacity: .35; transform: scale(.92) } 50% { opacity: 1; transform: scale(1) } }

        /* card */
        .ws-card { position: relative; z-index: 1; width: 100%; max-width: 1180px; display: grid; grid-template-columns: 1.05fr 1fr; background: #fff; border-radius: 28px;
                   box-shadow: 0 30px 80px color-mix(in srgb, var(--primary) 14%, transparent); overflow: hidden; animation: rise .6s ease-out both; }
        @keyframes rise { from { opacity: 0; transform: translateY(18px) } to { opacity: 1; transform: none } }

        /* left */
        .ws-side { position: relative; overflow: hidden; padding: 38px 40px 26px; min-height: 640px; display: flex; flex-direction: column;
                   background: linear-gradient(160deg, color-mix(in srgb, var(--primary) 10%, white) 0%, color-mix(in srgb, var(--primary) 6%, white) 55%, color-mix(in srgb, var(--primary) 16%, white) 100%); }
        .ws-logo { display: inline-flex; align-items: center; gap: 10px; font-weight: 800; font-size: 1.45rem; letter-spacing: -.02em; color: var(--ink); text-decoration: none; position: relative; z-index: 2; }
        .ws-logo svg { width: 44px; height: 44px; color: var(--primary); }
        .ws-logo span span { color: var(--orange); }
        .ws-side h1 { position: relative; z-index: 2; font-size: clamp(2rem, 3.4vw, 2.9rem); font-weight: 800; line-height: 1.08; letter-spacing: -.03em; margin: 34px 0 18px; max-width: 420px;
                      color: color-mix(in srgb, var(--primary) 30%, #0f1d16); animation: rise .7s .1s ease-out both; }
        .ws-side h1 em { font-style: normal; color: var(--primary); position: relative; display: inline-block; }
        .ws-side h1 em svg { position: absolute; left: 6%; bottom: -12px; width: 92%; height: 12px; }
        .ws-side h1 em svg path { stroke-dasharray: 220; stroke-dashoffset: 220; animation: draw 1s .7s ease-out forwards; }
        @keyframes draw { to { stroke-dashoffset: 0 } }
        .ws-side .lead { position: relative; z-index: 2; color: #2f3b34; font-size: 1.05rem; line-height: 1.55; max-width: 300px; animation: rise .7s .2s ease-out both; }
        .ws-note { position: relative; z-index: 2; display: inline-flex; align-items: center; gap: 6px; margin-top: 8px; font-size: .8rem; color: var(--muted); background: rgba(255,255,255,.7); border-radius: 999px; padding: 4px 12px; align-self: flex-start; }

        .ws-scene { position: absolute; inset: auto 0 0 0; height: 66%; z-index: 1; pointer-events: none; }
        .ws-hills { position: absolute; left: 0; right: 0; bottom: 0; width: 100%; height: 46%; }
        .ws-chef { position: absolute; right: 1%; bottom: 70px; width: 64%; max-width: 420px; }
        .ws-table { position: absolute; left: 2%; bottom: 74px; width: 46%; max-width: 280px; }
        .ws-table .sway { transform-origin: 70px 112px; animation: sway 3.2s ease-in-out infinite; }
        @keyframes sway { 0%, 100% { transform: rotate(-3deg) } 50% { transform: rotate(3deg) } }

        .ws-features { position: relative; z-index: 2; margin-top: auto; align-self: flex-start; display: flex; align-items: center; background: #fff; border-radius: 999px; padding: 12px 22px;
                       box-shadow: 0 12px 30px color-mix(in srgb, var(--primary) 16%, transparent); animation: rise .7s .35s ease-out both; flex-wrap: wrap; row-gap: 6px; }
        .ws-features span { display: inline-flex; align-items: center; gap: 8px; font-weight: 600; font-size: .9rem; padding: 0 14px; }
        .ws-features span + span { border-left: 1px solid #e3e9e6; }
        .ws-features i { color: var(--primary); font-size: 1.15rem; }

        /* right */
        .ws-form { padding: 46px 44px 40px; }
        .ws-form .eyebrow { color: var(--primary); font-weight: 700; margin-bottom: 6px; }
        .ws-form h2 { font-weight: 800; font-size: clamp(1.6rem, 2.6vw, 2.1rem); letter-spacing: -.02em; margin: 0 0 8px; color: color-mix(in srgb, var(--primary) 30%, #0f1d16); }
        .ws-form .signin { color: #2f3b34; margin-bottom: 26px; }
        .ws-form .signin a { color: var(--primary); font-weight: 700; text-decoration: none; }
        .ws-form .signin a:hover i { transform: translateX(3px); }
        .ws-form .signin a i { display: inline-block; transition: transform .2s; }
        .ws-form .form-label { font-weight: 500; color: var(--ink); margin-bottom: 6px; }
        .ws-field { position: relative; }
        .ws-field > i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #7a867f; font-size: 1.05rem; pointer-events: none; }
        .ws-field .form-control { height: 52px; padding-left: 44px; border-radius: 12px; border: 1px solid #dfe5e2; font-size: .95rem; }
        .ws-field .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 4px color-mix(in srgb, var(--primary) 14%, transparent); }
        .ws-field .form-control::placeholder { color: #8a958f; }

        .ws-colors { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; }
        .ws-color { width: 38px; height: 38px; border-radius: 50%; border: 0; padding: 0; position: relative; cursor: pointer; transition: transform .15s; box-shadow: 0 4px 10px rgba(0,0,0,.12); }
        .ws-color:hover { transform: scale(1.1); }
        .ws-color.active { box-shadow: 0 0 0 3px #fff, 0 0 0 5px var(--primary); }
        .ws-color.active::after { content: '\F26E'; font-family: bootstrap-icons; color: #fff; font-size: 1.15rem; position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; }
        .ws-color-custom { background: conic-gradient(#ef4444, #f59e0b, #22c55e, #06b6d4, #3b82f6, #8b5cf6, #ec4899, #ef4444); overflow: hidden; }
        .ws-color-custom input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }

        .ws-submit { width: 100%; height: 58px; margin-top: 30px; border: 0; border-radius: 999px; color: #fff; font-weight: 700; font-size: 1.1rem;
                     background: linear-gradient(135deg, var(--primary), color-mix(in srgb, var(--primary) 70%, black)); box-shadow: 0 14px 30px color-mix(in srgb, var(--primary) 30%, transparent); transition: transform .15s, box-shadow .15s; }
        .ws-submit:hover { transform: translateY(-2px); box-shadow: 0 18px 36px color-mix(in srgb, var(--primary) 36%, transparent); }
        .ws-submit i { display: inline-block; transition: transform .2s; margin-left: 6px; }
        .ws-submit:hover i { transform: translateX(4px); }

        @media (max-width: 991px) {
            .ws-card { grid-template-columns: 1fr; }
            .ws-side { min-height: 560px; }
            .ws-chef { width: 56%; }
        }
        @media (max-width: 575px) {
            body { padding: 14px 10px; }
            .ws-side { padding: 26px 22px 20px; min-height: 520px; }
            .ws-form { padding: 28px 22px; }
            .ws-chef { width: 70%; right: 0; }
            .ws-table { width: 52%; }
            .ws-features { padding: 9px 8px; flex-wrap: nowrap; align-self: stretch; justify-content: center; }
            .ws-features span { padding: 0 7px; font-size: .72rem; gap: 5px; white-space: nowrap; }
            .ws-features i { font-size: .9rem; }
            .ws-side { min-height: 560px; }
            .sparks { display: none; }
        }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; transition: none !important; } .ws-side h1 em svg path { stroke-dashoffset: 0; } }
</style>
