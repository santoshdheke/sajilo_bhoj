{{--
    Shared Material dashboard shell (drawer + app bar + content), used by each role
    module's own master layout — the role supplies its navigation, this supplies the chrome.

    @param string      $title        Current page name, shown in the breadcrumb and <title>.
    @param string      $section      Root breadcrumb label (e.g. "Superadmin", "Admin").
    @param string      $group        Optional middle breadcrumb crumb — the nav group a page
                                      belongs to (e.g. "Users"), so the breadcrumb reads the
                                      same path as the sidebar: Section > Group > Title.
    @param string      $brandTitle   Overrides the sidebar header's bold line (defaults to the
                                      app name) — e.g. a school's name for the Admin module.
    @param string      $brandSubtitle Overrides the small line under it (defaults to the host).
    @param array       $navigation   Each entry is either a leaf:
                                        ['label', 'icon', 'route', 'active'?]
                                      or a collapsible group:
                                        ['label', 'icon', 'children' => [
                                            ['label', 'route'?, 'active'?, 'roleFilter'?],
                                            ...
                                        ]]
                                      A child with no 'route' renders as a disabled "Soon" row —
                                      present in the menu, but nothing is built behind it yet.
                                      'roleFilter' (optional) is merged into the route as
                                      ?role=... and also used to tell otherwise-identical
                                      children (e.g. "All Users" vs "Teachers") apart when
                                      deciding which one is active.
--}}
@props(['title' => null, 'section' => null, 'group' => null, 'navigation' => [], 'brandTitle' => null, 'brandSubtitle' => null])

@php
    $user = auth()->user();
    $appName = config('app.name');

    // A leaf item — or a group child — is active when its route matches AND (if it declares
    // a roleFilter) the current ?role= query matches too. That's what tells "All Users" apart
    // from "Teachers": same route, different expected query.
    $isItemActive = function (array $item): bool {
        if (empty($item['route'])) {
            return false;
        }

        return request()->routeIs($item['active'] ?? $item['route'])
            && request()->query('role') === ($item['roleFilter'] ?? null);
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? "$title - " : '' }}{{ $appName }}</title>

        <!-- MDBootstrap + Material theme (see project CLAUDE.md — swap for the npm/Vite build once the asset pipeline is wired up) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/mdb-ui-kit@8/css/mdb.min.css" rel="stylesheet">
        <link href="{{ asset('school/css/sms-material.css') }}?v={{ @filemtime(public_path('school/css/sms-material.css')) }}" rel="stylesheet">
    </head>

    <body>
        <div class="sms-shell" id="sms-shell">
            <aside class="sms-drawer" id="sms-drawer">
                <a class="sms-drawer__brand" href="{{ url('/') }}">
                    <span class="sms-drawer__logo">{{ strtoupper(mb_substr($brandTitle ?? $appName, 0, 1)) }}</span>
                    <span class="sms-drawer__name">
                        {{ $brandTitle ?? $appName }}
                        <span class="sms-drawer__host">{{ $brandSubtitle ?? request()->getHost() }}</span>
                    </span>
                    <span class="material-icons-outlined sms-drawer__brand-caret" aria-hidden="true">unfold_more</span>
                </a>

                <ul class="sms-nav">
                    @if ($section)
                        <li class="sms-nav__label">{{ $section }}</li>
                    @endif

                    @foreach ($navigation as $item)
                        @if (isset($item['children']))
                            @php
                                $groupHasActiveChild = collect($item['children'])->contains($isItemActive);
                            @endphp
                            <li class="sms-nav__group {{ $groupHasActiveChild ? 'is-expanded' : '' }}">
                                <button type="button"
                                    class="sms-nav__link sms-nav__group-toggle {{ $groupHasActiveChild ? 'is-active' : '' }}"
                                    aria-expanded="{{ $groupHasActiveChild ? 'true' : 'false' }}">
                                    <span class="material-icons-outlined">{{ $item['icon'] }}</span>
                                    <span class="sms-nav__group-label">{{ $item['label'] }}</span>
                                    <span class="material-icons-outlined sms-nav__chevron">expand_more</span>
                                </button>
                                <ul class="sms-nav__submenu">
                                    @foreach ($item['children'] as $child)
                                        <li>
                                            @if (! empty($child['route']))
                                                <a href="{{ route($child['route'], array_filter(['role' => $child['roleFilter'] ?? null])) }}"
                                                    class="sms-nav__sublink {{ $isItemActive($child) ? 'is-active' : '' }}">
                                                    {{ $child['label'] }}
                                                </a>
                                            @else
                                                {{-- In the menu, nothing behind it yet — shown, not linked, no dead route. --}}
                                                <span class="sms-nav__sublink sms-nav__sublink--soon">
                                                    {{ $child['label'] }}
                                                    <span class="sms-badge-soon">Soon</span>
                                                </span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li>
                                <a href="{{ route($item['route']) }}"
                                    class="sms-nav__link {{ $isItemActive($item) ? 'is-active' : '' }}">
                                    <span class="material-icons-outlined">{{ $item['icon'] }}</span>
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>

                @if ($user)
                    <div class="sms-drawer__user-wrap">
                        <div class="sms-drawer__user">
                            <span class="sms-avatar">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                            <div class="sms-drawer__who">
                                <strong>{{ $user->name }}</strong>
                                <span>{{ $user->email }}</span>
                            </div>
                            <button type="button" class="sms-icon-btn sms-drawer__user-menu-btn" id="sms-user-menu-btn"
                                aria-haspopup="true" aria-expanded="false" aria-label="Account menu">
                                <span class="material-icons-outlined">more_vert</span>
                            </button>
                            <div class="sms-drawer__user-menu" id="sms-user-menu" role="menu" hidden>
                                {{-- No pages behind these yet — shown, not linked (like the "Soon" nav items). --}}
                                <span class="sms-drawer__user-menu-item is-disabled" role="menuitem" aria-disabled="true">Profile</span>
                                <span class="sms-drawer__user-menu-item is-disabled" role="menuitem" aria-disabled="true">My account</span>
                                <hr class="sms-drawer__user-menu-divider">
                                <span class="sms-drawer__user-menu-item is-disabled" role="menuitem" aria-disabled="true">Add another account</span>
                                <span class="sms-drawer__user-menu-item is-disabled" role="menuitem" aria-disabled="true">Settings</span>
                                <hr class="sms-drawer__user-menu-divider">
                                <form action="{{ route('school.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="sms-drawer__user-menu-item sms-drawer__user-menu-item--logout" role="menuitem">
                                        Logout
                                        <span class="material-icons-outlined">logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </aside>

            <div class="sms-scrim" id="sms-scrim"></div>

            <div class="sms-main">
                <header class="sms-appbar">
                    <button type="button" class="sms-icon-btn sms-appbar__toggle" id="sms-toggle" aria-label="Open navigation">
                        <span class="material-icons-outlined">menu</span>
                    </button>

                    <ol class="sms-breadcrumb">
                        @if ($section)
                            <li>{{ $section }}</li>
                            <li aria-hidden="true"><span class="material-icons-outlined">chevron_right</span></li>
                        @endif
                        @if ($group)
                            <li>{{ $group }}</li>
                            <li aria-hidden="true"><span class="material-icons-outlined">chevron_right</span></li>
                        @endif
                        <li>{{ $title ?? 'Dashboard' }}</li>
                    </ol>
                </header>

                <main class="sms-content">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/mdb-ui-kit@8/js/mdb.umd.min.js"></script>
        @stack('scripts')
        <script>
            (function () {
                var shell = document.getElementById('sms-shell');
                var toggleShell = function () { shell.classList.toggle('is-open'); };
                document.getElementById('sms-toggle').addEventListener('click', toggleShell);
                document.getElementById('sms-scrim').addEventListener('click', toggleShell);

                // Account kebab menu in the drawer footer.
                var menuBtn = document.getElementById('sms-user-menu-btn');
                var menu = document.getElementById('sms-user-menu');
                if (menuBtn && menu) {
                    var setMenu = function (open) {
                        menu.hidden = ! open;
                        menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                    };
                    menuBtn.addEventListener('click', function (e) { e.stopPropagation(); setMenu(menu.hidden); });
                    document.addEventListener('click', function (e) { if (! menu.contains(e.target)) setMenu(false); });
                    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setMenu(false); });
                }

                // Collapsible submenus (Users, Classes, ...). Independent of each other —
                // opening one doesn't close the rest, so several can stay open at once.
                document.querySelectorAll('.sms-nav__group-toggle').forEach(function (button) {
                    button.addEventListener('click', function () {
                        var expanded = button.getAttribute('aria-expanded') === 'true';
                        button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                        button.closest('.sms-nav__group').classList.toggle('is-expanded', ! expanded);
                    });
                });
            })();
        </script>
    </body>
</html>
