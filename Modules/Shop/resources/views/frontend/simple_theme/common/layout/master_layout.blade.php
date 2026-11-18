<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.css" rel="stylesheet" />
    @php
        $siteFontParam = $brand['font'];                          // e.g. "Open+Sans"
        $siteFontCss   = str_replace('+', ' ', $siteFontParam); // e.g. "Open Sans"
    @endphp
    <link href="https://fonts.googleapis.com/css2?family={{ $siteFontParam }}:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $shop->name)</title>
    @include('shop::partials.favicon', ['color' => $brand['primary'], 'logoUrl' => $shopLogoUrl])
    @stack('meta')
</head>

<?php
    $clrPrimary   = $brand['primary'];
    $clrSecondary = $brand['secondary'];
    $clrText      = $brand['text'];
    $sameColor    = ($clrPrimary === $clrSecondary);
    $tr = hexdec(substr($clrText,0,2));
    $tg = hexdec(substr($clrText,2,2));
    $tb = hexdec(substr($clrText,4,2));
?>

<style>
    :root {
        --primary:   #{{ $clrPrimary }};
        --secondary: #{{ $clrSecondary }};
        --clr-text:  #{{ $clrText }};
        --primary15: #{{ $clrPrimary }}26;
        --primary30: #{{ $clrPrimary }}4D;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { margin: 0; padding: 0; }

    body {
        margin: 0; padding: 0;
        font-family: '{{ $siteFontCss }}', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 14px;
        background-color: #ffffff;
        color: #222222;
    }

    a { text-decoration: none; }
    a:hover { text-decoration: none; }
    .container { max-width: 1280px; margin: 0 auto; padding: 0 30px; }

    /* ══ ANNOUNCEMENT BAR ══ */
    .announcement-bar {
        background: #{{ $clrPrimary }};
        color: #{{ $clrText }};
        text-align: center;
        padding: 9px 16px;
        font-size: 11.5px;
        font-weight: 500;
        letter-spacing: 0.6px;
    }
    .announcement-bar p { margin: 0; }
    .announcement-bar a { color: #{{ $clrText }}; text-decoration: underline; }

    /* ══ HEADER ══ */
    .site-header {
        background: #ffffff;
        border-bottom: 1px solid #e8e8e8;
        position: sticky;
        top: 0; left: 0; right: 0; width: 100%;
        z-index: 1000;
        border-radius: 0 !important;
        margin: 0 !important; padding: 0 !important;
        transition: box-shadow 0.3s ease;
    }
    .site-header.scrolled { box-shadow: 0 2px 20px rgba(0,0,0,0.09); }

    .header-row {
        display: flex; align-items: center;
        height: 68px; gap: 0;
    }

    .h-logo { flex-shrink: 0; margin-right: 40px; }
    .h-logo a {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 22px; font-weight: 700;
        color: #{{ $clrPrimary }};
        text-transform: uppercase;
        letter-spacing: 2px; line-height: 1;
        font-style: italic;
    }
    .h-logo a:hover { opacity: 0.8; color: #{{ $clrPrimary }}; }

    .h-nav { display: flex; align-items: center; gap: 0; flex: 1; }
    .h-nav > a {
        color: #444;
        font-size: 12px; font-weight: 500;
        padding: 0 13px; height: 68px;
        display: flex; align-items: center; gap: 4px;
        letter-spacing: 0.7px; text-transform: uppercase;
        border-bottom: 2px solid transparent;
        transition: color 0.2s, border-bottom-color 0.2s;
    }
    .h-nav > a:hover { color: #{{ $clrPrimary }}; border-bottom-color: #{{ $clrPrimary }}; }
    .h-nav > a .caret { font-size: 9px; opacity: 0.55; margin-top: 1px; }

    /* Dropdown wrapper — div, not <a>, so hover area includes the menu */
    .h-nav-item {
        position: relative;
        display: flex; align-items: center;
    }
    .h-nav-item > a {
        color: #444;
        font-size: 12px; font-weight: 500;
        padding: 0 13px; height: 68px;
        display: flex; align-items: center; gap: 4px;
        letter-spacing: 0.7px; text-transform: uppercase;
        border-bottom: 2px solid transparent;
        transition: color 0.2s, border-bottom-color 0.2s;
    }
    .h-nav-item > a:hover { color: #{{ $clrPrimary }}; border-bottom-color: #{{ $clrPrimary }}; }

    .h-nav > .has-drop { position: relative; }
    .h-nav .drop-menu {
        display: none; position: absolute;
        top: 100%; left: 0;
        background: #fff; min-width: 185px;
        border-radius: 0 0 6px 6px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        border-top: 2px solid #{{ $clrPrimary }};
        z-index: 100;
    }
    .h-nav .drop-menu a {
        display: block; padding: 11px 18px;
        color: #555; font-size: 13px; font-weight: 400;
        text-transform: none; letter-spacing: 0;
        border-bottom: 1px solid #f5f5f5;
        transition: background 0.15s, color 0.15s;
        height: auto;
    }
    .h-nav .drop-menu a:hover { background: #faf9f7; color: #{{ $clrPrimary }}; border-bottom-color: #f5f5f5; }

    .h-actions { display: flex; align-items: center; gap: 4px; flex-shrink: 0; margin-left: 16px; }

    .h-search form {
        display: flex; align-items: center;
        background: #f7f6f4; border: 1px solid #e8e8e8;
        border-radius: 50px; overflow: hidden;
        height: 36px; width: 185px;
        transition: width 0.3s, border-color 0.2s;
    }
    .h-search form:focus-within { width: 225px; border-color: #{{ $clrPrimary }}; }
    .h-search input {
        background: transparent; border: none; outline: none;
        color: #333; font-size: 12px;
        font-family: '{{ $siteFontCss }}', sans-serif;
        padding: 0 14px; flex: 1; width: 0;
    }
    .h-search input::placeholder { color: #bbb; }
    .h-search button {
        background: transparent; border: none;
        color: #999; padding: 0 12px; cursor: pointer;
        font-size: 13px; height: 36px; display: flex; align-items: center;
    }
    .h-search button:hover { color: #{{ $clrPrimary }}; }

    .h-icon-btn {
        width: 40px; height: 40px;
        display: flex; align-items: center; justify-content: center;
        color: #555; font-size: 17px; border-radius: 50%;
        transition: color 0.2s, background 0.2s;
        position: relative; cursor: pointer;
    }
    .h-icon-btn:hover { color: #{{ $clrPrimary }}; background: #f5f3f0; }
    .h-icon-btn .badge-dot {
        position: absolute; top: 4px; right: 4px;
        background: #{{ $clrPrimary }}; color: #{{ $clrText }};
        font-size: 9px; font-weight: 700;
        min-width: 16px; height: 16px; border-radius: 50px;
        display: flex; align-items: center; justify-content: center;
        padding: 0 3px; border: 2px solid #ffffff;
    }

    .h-btn-login {
        padding: 7px 18px; border-radius: 50px;
        font-size: 12px; font-weight: 600;
        color: #{{ $clrPrimary }};
        border: 1.5px solid #{{ $clrPrimary }};
        background: transparent; transition: all 0.2s; white-space: nowrap;
    }
    .h-btn-login:hover { background: #{{ $clrPrimary }}; color: #{{ $clrText }}; }

    .cart-wrapper { position: relative; }
    .cart-dropdown {
        display: none; position: absolute;
        top: calc(100% + 10px); right: 0;
        width: 300px; background: #fff;
        border-radius: 8px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.14);
        z-index: 200; padding: 14px;
        border: 1px solid #f0f0f0;
    }
    .cart-wrapper:hover .cart-dropdown { display: block; }
    .cart-item {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 0; border-bottom: 1px solid #f5f5f5;
    }
    .cart-item img { width: 54px; height: 54px; object-fit: cover; border-radius: 4px; }
    .cart-item-name { font-size: 12px; font-weight: 600; color: #222; }
    .cart-item-price { font-size: 11px; color: #999; margin-top: 2px; }
    .cart-remove { margin-left: auto; color: #ccc; font-size: 13px; flex-shrink: 0; transition: color 0.2s; }
    .cart-remove:hover { color: #e53e3e; }
    .cart-total { display: flex; justify-content: space-between; padding: 10px 0 6px; font-size: 13px; font-weight: 700; color: #222; }
    .btn-checkout {
        display: block; text-align: center; padding: 11px;
        background: #{{ $clrPrimary }}; color: #{{ $clrText }};
        border-radius: 4px; font-size: 13px; font-weight: 600;
        transition: opacity 0.2s; letter-spacing: 0.5px;
    }
    .btn-checkout:hover { opacity: 0.85; color: #{{ $clrText }}; }

    .mobile-search-bar {
        background: #f9f8f6; border-top: 1px solid #ebebeb;
        padding: 10px 16px 12px;
    }
    .mobile-search-bar form {
        display: flex; align-items: center;
        background: #fff; border: 1px solid #e0e0e0;
        border-radius: 50px; height: 36px; overflow: hidden;
    }
    .mobile-search-bar input {
        background: transparent; border: none; outline: none;
        color: #333; font-size: 12px; padding: 0 14px; flex: 1;
    }
    .mobile-search-bar input::placeholder { color: #bbb; }
    .mobile-search-bar button {
        background: transparent; border: none; color: #999;
        padding: 0 12px; cursor: pointer; font-size: 13px; height: 36px;
    }

    /* ══ MOBILE MENU ══ */
    .mobile-header { display: none; }
    .mobile-menu-overlay {
        display: none; position: fixed; inset: 0;
        z-index: 9999; background: rgba(0,0,0,0.5);
    }
    .mobile-menu-drawer {
        background: #fff; width: 280px; height: 100vh;
        padding: 24px 20px; overflow-y: auto;
    }
    .mobile-menu-close {
        background: none; border: none; color: #444;
        font-size: 20px; cursor: pointer; float: right; margin-bottom: 24px;
    }
    .mobile-menu-drawer .m-logo {
        font-family: 'Playfair Display', serif;
        font-size: 20px; font-weight: 700;
        color: #{{ $clrPrimary }};
        text-transform: uppercase; letter-spacing: 2px;
        font-style: italic; display: block; margin-bottom: 24px;
    }
    .mobile-menu-drawer a {
        display: block; color: #444;
        padding: 12px 0; font-size: 13.5px; font-weight: 500;
        border-bottom: 1px solid #f0f0f0;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .mobile-menu-drawer a:hover { color: #{{ $clrPrimary }}; }

    /* ══ PRODUCT CARD ══ */
    .pc-wrap { padding: 6px; }
    .pc-link { display: block; text-decoration: none; color: inherit; }
    .pc-link:hover { text-decoration: none; color: inherit; }
    .pc {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        border: 1px solid #f0f0f0;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .pc:hover {
        box-shadow: 0 8px 32px rgba(0,0,0,0.10);
        transform: translateY(-2px);
    }

    /* Image — portrait 3:4 ratio */
    .pc-img-wrap {
        position: relative; overflow: hidden;
        background: #f4f3f1;
        aspect-ratio: 3 / 4;
        border-radius: 10px 10px 0 0;
    }
    .pc-img {
        width: 100%; height: 100%; object-fit: cover; display: block;
        transition: transform 0.55s ease;
    }
    .pc:hover .pc-img { transform: scale(1.06); }

    /* Badge — top left */
    .pc-badge {
        position: absolute; top: 10px; left: 10px;
        background: #{{ $clrPrimary }}; color: #{{ $clrText }};
        font-size: 9px; font-weight: 700; padding: 4px 9px;
        letter-spacing: 1px; text-transform: uppercase;
        z-index: 4; border-radius: 20px;
    }
    .pc-badge.badge-sale { background: #ef4444; color: #fff; }
    .pc-badge.badge-new  { background: #{{ $clrPrimary }}; color: #{{ $clrText }}; }

    /* Wishlist — top right, appears on hover */
    .pc-wish-btn {
        position: absolute; top: 10px; right: 10px;
        width: 32px; height: 32px;
        background: rgba(255,255,255,0.95); color: #888;
        border: none; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; cursor: pointer; z-index: 4;
        opacity: 0; transform: scale(0.8);
        transition: opacity 0.2s, transform 0.2s, color 0.15s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.10);
    }
    .pc:hover .pc-wish-btn { opacity: 1; transform: scale(1); }
    .pc-wish-btn:hover { color: #ef4444; }

    /* Hover CTA overlay — slides up from bottom */
    .pc-hover-overlay {
        position: absolute; bottom: 0; left: 0; right: 0;
        padding: 12px 10px;
        background: linear-gradient(to top, rgba(0,0,0,0.55) 0%, transparent 100%);
        opacity: 0; transform: translateY(6px);
        transition: opacity 0.22s, transform 0.22s;
        z-index: 3;
    }
    .pc:hover .pc-hover-overlay { opacity: 1; transform: translateY(0); }

    .pc-pill-btn {
        display: flex; align-items: center; justify-content: center; gap: 7px;
        width: 100%; padding: 9px 14px;
        background: #fff;
        color: #111;
        border: none; border-radius: 6px;
        font-size: 12px; font-weight: 700;
        letter-spacing: .3px;
        cursor: pointer; font-family: '{{ $siteFontCss }}', sans-serif;
        transition: background 0.15s, color 0.15s;
    }
    .pc-pill-btn:hover { background: #{{ $clrPrimary }}; color: #{{ $clrText }}; }

    /* ── Below-image info panel (always visible) ── */
    .pc-below {
        padding: 12px 14px 14px;
        background: #fff;
    }
    .pc-below-cat {
        font-size: 10px; font-weight: 600;
        color: #{{ $clrPrimary }};
        text-transform: uppercase; letter-spacing: 1px;
        margin-bottom: 4px;
    }
    .pc-below-name {
        font-size: 13.5px; font-weight: 600; color: #111;
        margin: 0 0 8px; line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .pc-below-price {
        font-size: 15px; font-weight: 800; color: #{{ $clrPrimary }};
    }
    .pc-below-currency { font-size: 12px; font-weight: 600; }

    /* Legacy helpers */
    .product-card-img { width:100%; height:220px; object-fit:cover; display:block; }
    .pc-info { display: none; }
    /* Legacy overlay classes — kept for compatibility */
    .pc-overlay { display: none; }
    .pc-overlay-btn { display: none; }

    /* ══ SECTIONS ══ */
    .section-header {
        display: flex; align-items: center;
        justify-content: space-between;
        margin-bottom: 28px; padding-bottom: 16px;
        border-bottom: 2px solid #{{ $clrPrimary }};
    }
    .section-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 22px; font-weight: 700;
        color: #222; margin: 0;
        font-style: italic; letter-spacing: 0;
    }
    .section-link {
        font-size: 11px; font-weight: 600;
        color: #{{ $clrPrimary }};
        padding: 7px 18px;
        border: 1.5px solid #{{ $clrPrimary }};
        border-radius: 50px; transition: all 0.2s;
        text-transform: uppercase; letter-spacing: 1px;
    }
    .section-link:hover { background: #{{ $clrPrimary }}; color: #{{ $clrText }}; }
    .product-section { padding: 50px 0 12px; }

    /* ══ BUTTONS ══ */
    .btn-primary-own {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 13px 32px;
        background: #{{ $clrPrimary }}; color: #{{ $clrText }};
        border-radius: 2px; font-weight: 700; font-size: 12px;
        border: 2px solid #{{ $clrPrimary }};
        transition: all 0.2s; cursor: pointer;
        text-transform: uppercase; letter-spacing: 1.5px;
    }
    .btn-primary-own:hover { background: transparent; color: #{{ $clrPrimary }}; }

    /* Filter sidebar */
    .filter-sidebar { background: #fff; border-radius: 8px; border: 1px solid #ebebeb; overflow: hidden; margin-bottom: 20px; }
    .filter-sidebar-title {
        font-size: 12px; font-weight: 700; color: #{{ $clrText }};
        background: #{{ $clrPrimary }};
        padding: 12px 18px; text-transform: uppercase; letter-spacing: 1px;
        margin: 0; display: flex; justify-content: space-between;
        align-items: center; cursor: pointer;
    }
    .filter-sidebar ul { list-style: none; padding: 8px 0; margin: 0; max-height: 260px; overflow-y: auto; }
    .filter-sidebar ul::-webkit-scrollbar { width: 3px; }
    .filter-sidebar ul::-webkit-scrollbar-thumb { background: #{{ $clrPrimary }}66; border-radius: 4px; }
    .filter-sidebar ul li a {
        display: block; padding: 8px 18px; font-size: 13px; color: #555;
        border-left: 3px solid transparent; transition: all 0.15s;
    }
    .filter-sidebar ul li a:hover,
    .filter-sidebar ul li.active a { color: #{{ $clrPrimary }}; background: #{{ $clrPrimary }}11; border-left-color: #{{ $clrPrimary }}; }

    /* ══ NEWSLETTER ══ */
    .newsletter-section {
        background: #faf9f7; padding: 60px 0;
        text-align: center; border-top: 1px solid #ebebeb;
    }
    .newsletter-section h2 {
        font-family: 'Playfair Display', serif;
        font-size: 28px; font-weight: 700; font-style: italic;
        color: #222; margin-bottom: 8px;
    }
    .newsletter-section p { color: #999; font-size: 14px; margin-bottom: 26px; }
    .nl-form {
        display: flex; max-width: 420px; margin: 0 auto;
        border-radius: 2px; overflow: hidden;
        border: 1.5px solid #ddd;
    }
    .nl-form input {
        flex: 1; border: none; background: #fff; color: #333;
        padding: 13px 18px; font-size: 13px; outline: none;
        font-family: '{{ $siteFontCss }}', sans-serif;
    }
    .nl-form input::placeholder { color: #ccc; }
    .nl-form button {
        padding: 13px 22px;
        background: #{{ $clrPrimary }}; color: #{{ $clrText }};
        border: none; font-size: 10.5px; font-weight: 700;
        letter-spacing: 1.5px; text-transform: uppercase;
        cursor: pointer; font-family: '{{ $siteFontCss }}', sans-serif;
        transition: opacity 0.2s; white-space: nowrap;
    }
    .nl-form button:hover { opacity: 0.85; }

    /* ══ FOOTER ══ */
    .site-footer { background: #{{ $clrPrimary }}; margin-top: 0; }
    .footer-top { padding: 52px 0 32px; }
    .footer-brand {
        font-family: 'Playfair Display', serif;
        font-size: 20px; font-weight: 700; font-style: italic;
        color: #{{ $clrText }};
        text-transform: uppercase; letter-spacing: 2px; margin-bottom: 12px;
    }
    .footer-about {
        color: rgba({{ $tr }},{{ $tg }},{{ $tb }},0.6);
        font-size: 13px; line-height: 1.75;
        max-width: 260px; margin-bottom: 20px;
    }
    .footer-social { display: flex; gap: 10px; margin-top: 4px; }
    .footer-social a {
        width: 36px; height: 36px; border-radius: 50%;
        background: rgba({{ $tr }},{{ $tg }},{{ $tb }},0.12);
        color: #{{ $clrText }};
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; transition: background 0.2s, transform 0.2s;
    }
    .footer-social a:hover {
        background: rgba({{ $tr }},{{ $tg }},{{ $tb }},0.28);
        transform: translateY(-2px); color: #{{ $clrText }};
    }
    .footer-heading {
        font-size: 11px; font-weight: 700; color: #{{ $clrText }};
        text-transform: uppercase; letter-spacing: 2px;
        margin-bottom: 16px; padding-bottom: 10px;
        border-bottom: 1px solid rgba({{ $tr }},{{ $tg }},{{ $tb }},0.15);
    }
    .footer-links { list-style: none; padding: 0; margin: 0; }
    .footer-links li { margin-bottom: 9px; }
    .footer-links a {
        color: rgba({{ $tr }},{{ $tg }},{{ $tb }},0.6);
        font-size: 13px; transition: color 0.2s, padding-left 0.15s;
    }
    .footer-links a:hover { color: #{{ $clrText }}; padding-left: 4px; }
    .footer-divider { border-color: rgba({{ $tr }},{{ $tg }},{{ $tb }},0.12); margin: 0; }
    .footer-bottom {
        padding: 18px 0;
        display: flex; justify-content: space-between;
        align-items: center; flex-wrap: wrap; gap: 8px;
    }
    .footer-copy { color: rgba({{ $tr }},{{ $tg }},{{ $tb }},0.45); font-size: 12px; margin: 0; }
    .footer-copy a { color: rgba({{ $tr }},{{ $tg }},{{ $tb }},0.65); }
    .footer-copy a:hover { color: #{{ $clrText }}; }

    /* ══ WHATSAPP FLOAT ══ */
    .whatsapp-float {
        position: fixed; bottom: 28px; right: 28px;
        width: 54px; height: 54px; background: #25D366;
        color: #fff; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 26px; z-index: 9998;
        box-shadow: 0 4px 18px rgba(37,211,102,0.45);
        transition: transform 0.22s, box-shadow 0.22s;
    }
    .whatsapp-float:hover {
        transform: scale(1.12);
        box-shadow: 0 6px 28px rgba(37,211,102,0.6);
        color: #fff;
    }

    /* ══ UTILS ══ */
    .site-content { min-height: calc(100vh - 380px); }
    .owl-carousel .item { margin: 0 5px; }

    /* ══ RESPONSIVE ══ */
    @media (max-width: 991px) {
        .header-row { display: none; }          /* hide full desktop row */
        .h-nav, .h-search { display: none; }
        .desktop-only { display: none; }
        .mobile-header { display: flex; align-items: center; justify-content: space-between; height: 58px; }
        .footer-bottom { justify-content: center; text-align: center; }
        .cart-dropdown { display: none !important; }
    }
    @media (min-width: 992px) { .mobile-header { display: none; } }

    @media (max-width: 767px) {
        /* Container */
        .container { padding: 0 14px; }

        /* Announcement */
        .announcement-bar { font-size: 10px; padding: 7px 10px; letter-spacing: 0; }

        /* Sections */
        .product-section { padding: 28px 0 6px; }
        .section-header { margin-bottom: 16px; padding-bottom: 10px; flex-wrap: wrap; gap: 8px; }
        .section-title { font-size: 17px; }
        .section-link { font-size: 10px; padding: 5px 12px; }

        /* Product card overlay — smaller text on mobile */
        .pc-wrap { padding: 4px; }
        .pc-overlay { padding: 28px 10px 10px; }
        .pc-overlay-cat { font-size: 8.5px; letter-spacing: 1px; }
        .pc-overlay-name { font-size: 11px; }
        .pc-overlay-price { font-size: 12px; }
        .pc-overlay-btn { font-size: 10px; padding: 8px 6px; margin-top: 6px; letter-spacing: 0.6px; }
        .pc-wish-btn { opacity: 1; transform: scale(1); width: 28px; height: 28px; font-size: 11px; }

        /* Newsletter */
        .newsletter-section { padding: 32px 0; }
        .newsletter-section h2 { font-size: 20px; }
        .newsletter-section p { font-size: 12.5px; margin-bottom: 18px; }
        .nl-form { max-width: 100%; border-radius: 4px; }
        .nl-form input { padding: 11px 14px; font-size: 12.5px; }
        .nl-form button { padding: 11px 16px; font-size: 10px; }

        /* Footer */
        .footer-top { padding: 28px 0 16px; }
        .footer-brand { font-size: 17px; }
        .footer-about { font-size: 12.5px; max-width: 100%; margin-bottom: 14px; }
        .footer-heading { font-size: 10px; margin-bottom: 12px; }
        .footer-links a { font-size: 12.5px; }
        .footer-copy { font-size: 11.5px; }

        /* Mobile search bar */
        .mobile-search-bar { padding: 8px 14px 10px; }

        /* WhatsApp */
        .whatsapp-float { width: 46px; height: 46px; font-size: 22px; bottom: 18px; right: 14px; }

        /* Mobile drawer */
        .mobile-menu-drawer { max-width: 82vw; padding: 20px 16px; }
        .mobile-menu-drawer a { font-size: 13px; padding: 11px 0; }
    }

    @media (max-width: 480px) {
        /* Announcement: show only one item */
        .announcement-bar .ann-extra { display: none; }
        /* Remove overlay Add-to-Cart on tiny screens (tap card to go to product) */
        .pc-overlay-btn { display: none; }
        .pc-overlay { padding: 20px 10px 10px; }
    }
    .sf-flash { display:flex; align-items:center; gap:10px; margin:16px 0 0; padding:12px 16px; border-radius:8px; font-size:13px; font-weight:500; }
    .sf-flash button { margin-left:auto; background:none; border:none; font-size:20px; line-height:1; color:inherit; opacity:.6; cursor:pointer; }
    .sf-flash-success { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
    .sf-flash-error { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
</style>

@stack('css')

<body>


    <!-- ANNOUNCEMENT BAR -->
    <div class="announcement-bar">
        <p><i class="fas fa-bullhorn" style="margin-right:6px;font-size:11px;"></i>{{ $settings->announcement ?: 'Free delivery across Nepal • Easy 7-day returns • 100% secure checkout' }}</p>
    </div>

    <!-- Mobile menu -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay">
        <div class="mobile-menu-drawer" id="mobileMenuDrawer">
            <button class="mobile-menu-close" id="mobileMenuClose"><i class="fas fa-times"></i></button>
            <div style="clear:both;">
                <span class="m-logo">{{ $shop->name }}</span>
            </div>
            <a href="{{ route('shop.front.home') }}">Home</a>
            @foreach (Modules\Shop\Models\Category::where('parent_id', 0)->take(8)->get() as $cat)
                <a href="{{ route('shop.front.category', $cat->slug) }}">{{ $cat->title }}</a>
            @endforeach
            <a href="{{ route('shop.front.store') }}">All Products</a>
            @if (auth('shop_customer')->check())
                    <a href="{{ route('shop.front.account.profile') }}">My Profile</a>
                    <a href="{{ route('shop.front.account.orders', ['order-status' => 'pending']) }}">Orders</a>
                <form action="{{ route('shop.front.logout') }}" method="post" style="margin-top:8px;">
                    @csrf
                    <button style="background:none;border:none;color:#{{ $clrPrimary }};font-size:13.5px;font-family:Poppins,sans-serif;padding:12px 0;cursor:pointer;text-transform:uppercase;letter-spacing:0.5px;width:100%;text-align:left;">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('shop.front.login') }}">Login</a>
                <a href="{{ route('shop.front.register') }}">Register</a>
            @endif
        </div>
    </div>

    <!-- ════ HEADER ════ -->
    <header class="site-header">
        <div class="container">

            <!-- Desktop -->
            <div class="header-row">
                <div class="h-logo">
                    <a href="{{ route('shop.front.home') }}">{{ $shop->name }}</a>
                </div>

                <nav class="h-nav">
                    <a href="{{ route('shop.front.home') }}">Home</a>
                    <div class="h-nav-item has-drop">
                        <a href="{{ route('shop.front.store') }}">
                            Shop <i class="fas fa-chevron-down caret"></i>
                        </a>
                        <div class="drop-menu">
                            @foreach (Modules\Shop\Models\Category::where('parent_id', 0)->take(6)->get() as $cat)
                                <a href="{{ route('shop.front.category', $cat->slug) }}">{{ $cat->title }}</a>
                            @endforeach
                            <a href="{{ route('shop.front.store') }}">All Products</a>
                        </div>
                    </div>
                    @foreach (Modules\Shop\Models\Category::where('parent_id', 0)->take(4)->get() as $cat)
                        <a href="{{ route('shop.front.category', $cat->slug) }}">{{ $cat->title }}</a>
                    @endforeach
                </nav>

                <div class="h-actions">
                    <div class="h-search">
                        <form action="{{ route('shop.front.store') }}">
                            <button type="submit"><i class="fas fa-search"></i></button>
                            <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
                        </form>
                    </div>

                    @if (auth('shop')->check())
                        <a href="{{ route('shop.admin.dashboard.index') }}" class="h-btn-login" title="Shop back office">Dashboard</a>
                    @endif
                    @if (auth('shop_customer')->check())
                            <a href="{{ route('shop.front.account.profile') }}" class="h-icon-btn" title="Account">
                                <i class="far fa-user"></i>
                            </a>
                        <form action="{{ route('shop.front.logout') }}" method="post" style="margin:0;">
                            @csrf
                            <button class="h-btn-login" type="submit">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('shop.front.login') }}" class="h-icon-btn" title="Login">
                            <i class="far fa-user"></i>
                        </a>
                    @endif

                    <?php
                    $carts = \Modules\Shop\Support\Cart::raw();
                    $carts = json_decode($carts, true);
                    $cartCount = is_array($carts) ? count($carts) : 0;
                    ?>

                    <div class="cart-wrapper">
                        <a href="{{ route('shop.front.checkout') }}" class="h-icon-btn" title="Cart">
                            <i class="fas fa-shopping-bag"></i>
                            <span class="badge-dot totalItem" @if($cartCount == 0) style="display:none;" @endif>{{ $cartCount }}</span>
                        </a>
                        <div class="cart-dropdown">
                            <?php $totalPrice = 0; ?>
                            @if (isset($carts) && count($carts) > 0)
                                @foreach ($carts as $id => $cart)
                                    <div class="cart-item">
                                        <img src="{{ $cart['product']['image'] }}" alt="{{ $cart['product']['name'] }}">
                                        <div>
                                            <div class="cart-item-name">{{ $cart['product']['name'] }}</div>
                                            <div class="cart-item-price">Rs. {{ $cart['product']['price'] }}</div>
                                        </div>
                                        <a href="{{ route('shop.front.cart.remove', $id) }}" class="cart-remove">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                    <?php $totalPrice += $cart['product']['price']; ?>
                                @endforeach
                                <div class="cart-total">
                                    <span>Total</span>
                                    <span>Rs. {{ number_format($totalPrice) }}</span>
                                </div>
                                <a href="{{ route('shop.front.checkout') }}" class="btn-checkout">Checkout</a>
                            @else
                                <p style="text-align:center;color:#bbb;font-size:12px;padding:20px 0;">Your cart is empty</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile -->
            <div class="mobile-header">
                <button id="mobileMenuToggle" style="background:none;border:none;color:#{{ $clrPrimary }};font-size:20px;cursor:pointer;padding:0;">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="{{ route('shop.front.home') }}" style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:#{{ $clrPrimary }};text-transform:uppercase;letter-spacing:2px;font-style:italic;">
                    {{ $shop->name }}
                </a>
                <a href="{{ route('shop.front.checkout') }}" style="color:#{{ $clrPrimary }};font-size:18px;position:relative;">
                    <i class="fas fa-shopping-bag"></i>
                    @if($cartCount > 0)
                        <span style="position:absolute;top:-6px;right:-8px;background:#{{ $clrPrimary }};color:#{{ $clrText }};font-size:9px;font-weight:700;min-width:16px;height:16px;border-radius:50px;display:flex;align-items:center;justify-content:center;padding:0 3px;border:2px solid #fff;" class="totalItem">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- Mobile search -->
        <div class="d-block d-lg-none mobile-search-bar">
            <form action="{{ route('shop.front.store') }}">
                <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </header>

    {{-- Flash messages (pages that show their own set @section('hide_flash', 1)) --}}
    @hasSection('hide_flash')
    @else
        @php $flashMessages = array_filter(['success' => session('success'), 'error' => session('error') ?: ($errors->any() ? $errors->first() : null)]); @endphp
        @if ($flashMessages)
            <div class="container" style="position:relative;z-index:5;">
                @foreach ($flashMessages as $type => $message)
                    <div class="sf-flash sf-flash-{{ $type }}" role="alert">
                        <i class="fas {{ $type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
                        <span>{{ $message }}</span>
                        <button type="button" onclick="this.parentElement.remove()" aria-label="Close">&times;</button>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    <div class="site-content">
        @yield('contant')
    </div>

    <!-- ════ NEWSLETTER ════ -->
    <section class="newsletter-section">
        <div class="container">
            <h2>Stay in the Loop</h2>
            <p>Subscribe for new arrivals, exclusive offers and style inspiration.</p>
            <form class="nl-form" onsubmit="return false;">
                <input type="email" placeholder="Your email address">
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </section>

    <!-- ════ FOOTER ════ -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-top">
                <div class="row">
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="footer-brand">{{ $shop->name }}</div>
                        <p class="footer-about">{{ $settings->about_us }}</p>
                        <div class="footer-social">
                            @foreach ($socialLinks as $platform => $link)
                                <a href="{{ $link['url'] }}" title="{{ $link['label'] }}" target="_blank" rel="noopener"><i class="{{ $link['icon'] }}"></i></a>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-4 mb-md-0">
                        <h6 class="footer-heading">Quick Links</h6>
                        <ul class="footer-links">
                            <li><a href="{{ route('shop.front.home') }}">Home</a></li>
                            <li><a href="{{ route('shop.front.store') }}">Shop</a></li>
                            <li><a href="{{ route('shop.front.checkout') }}">Cart</a></li>
                            @if (!auth('shop_customer')->check())
                                <li><a href="{{ route('shop.front.login') }}">Login</a></li>
                                <li><a href="{{ route('shop.front.register') }}">Register</a></li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-md-3 col-6 mb-4 mb-md-0">
                        <h6 class="footer-heading">Categories</h6>
                        <ul class="footer-links">
                            @foreach (Modules\Shop\Models\Category::where('parent_id', 0)->take(5)->get() as $cat)
                                <li><a href="{{ route('shop.front.category', $cat->slug) }}">{{ $cat->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h6 class="footer-heading">Contact</h6>
                        <ul class="footer-links">
                            @if ($shop->phone)<li><a href="tel:{{ $shop->phone }}"><i class="fas fa-phone-alt mr-2"></i>{{ $shop->phone }}</a></li>@endif
                            @if ($shop->email)<li><a href="mailto:{{ $shop->email }}"><i class="far fa-envelope mr-2"></i>{{ $shop->email }}</a></li>@endif
                            @if ($shop->address)<li><span style="color:inherit;opacity:.8;"><i class="fas fa-map-marker-alt mr-2"></i>{{ $shop->address }}</span></li>@endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <hr class="footer-divider">
        <div class="container">
            <div class="footer-bottom">
                <p class="footer-copy">&copy; {{ date('Y') }} {{ $shop->name }}. All Rights Reserved.</p>
                <p class="footer-copy">Powered by <strong>SajiloBhoj</strong></p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Float -->
    @isset($socialLinks['whatsapp'])
    <a href="{{ $socialLinks['whatsapp']['url'] }}" class="whatsapp-float" title="Chat on WhatsApp" target="_blank" rel="noopener">
        <i class="fab fa-whatsapp"></i>
    </a>
    @endisset

</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script>

@stack('js')

<script>
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 10) {
            $('.site-header').addClass('scrolled');
        } else {
            $('.site-header').removeClass('scrolled');
        }
    });

    $('#mobileMenuToggle').on('click', function () { $('#mobileMenuOverlay').fadeIn(200); });
    $('#mobileMenuClose').on('click', function () { $('#mobileMenuOverlay').fadeOut(200); });
    $('#mobileMenuOverlay').on('click', function (e) {
        if (!$(e.target).closest('#mobileMenuDrawer').length) $(this).fadeOut(200);
    });

    // Desktop nav dropdown — .has-drop is a div wrapper so mouseleave fires correctly
    $('.h-nav .has-drop').on('mouseenter', function () {
        $(this).find('.drop-menu').stop(true, true).fadeIn(160);
    }).on('mouseleave', function () {
        $(this).find('.drop-menu').stop(true, true).delay(60).fadeOut(130);
    });
</script>

</html>
