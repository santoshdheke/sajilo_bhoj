@php
    $roleMeta = [
        'admin' => ['label' => 'Admins', 'icon' => 'shield', 'color' => '#0b6bcb'],
        'teacher' => ['label' => 'Teachers', 'icon' => 'menu_book', 'color' => '#2e7d32'],
        'student' => ['label' => 'Students', 'icon' => 'school', 'color' => '#c77700'],
    ];

    // Everything charted below is embedded once here and read by the script at the
    // bottom — real counts from DashboardService, nothing fabricated.
    $chartData = [
        'labels' => $trendDates,
        'trendTotal' => $trendTotal,
        'trendByRole' => $trendByRole,
        'roleColors' => array_map(fn ($role) => $roleMeta[$role]['color'], array_keys($roleMeta)),
        'roleLabels' => array_map(fn ($role) => $roleMeta[$role]['label'], array_keys($roleMeta)),
        // Explicitly keyed by $roleMeta's order (admin, teacher, student) — not $counts's own
        // order (UserRoleEnum::values(): student, teacher, admin) — so this lines up with
        // roleColors/roleLabels above for the bar chart.
        'roleTotals' => array_map(fn ($role) => $counts[$role] ?? 0, array_keys($roleMeta)),
    ];
@endphp
<x-school::layouts.admin title="Dashboard">
    <div class="sms-page-header">
        <div>
            <h1 class="sms-page-title">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="sms-page-subtitle">Overview of {{ $school->name }}.</p>
        </div>
        <a href="{{ route('school.admin.users.create') }}" class="btn btn-primary">
            <span class="material-icons-outlined">add</span>Add user
        </a>
    </div>

    <div class="sms-stat-grid">
        <div class="sms-paper sms-stat-card">
            <div class="sms-stat-card__top">
                <span class="sms-stat-card__label">Total users</span>
                @if (array_sum($newThisWeek) > 0)
                    <span class="sms-stat-card__badge">+{{ array_sum($newThisWeek) }} this wk</span>
                @endif
            </div>
            <div class="sms-stat-card__value">{{ array_sum($counts) }}</div>
            <div class="sms-stat-card__caption">New signups, last 30 days</div>
            <div class="sms-stat-card__sparkline-wrap"><canvas class="sms-stat-card__sparkline" id="sparkline-total" aria-hidden="true"></canvas></div>
        </div>

        @foreach ($roleMeta as $role => $meta)
            <div class="sms-paper sms-stat-card">
                <div class="sms-stat-card__top">
                    <span class="sms-stat-card__label">{{ $meta['label'] }}</span>
                    @if (($newThisWeek[$role] ?? 0) > 0)
                        <span class="sms-stat-card__badge">+{{ $newThisWeek[$role] }} this wk</span>
                    @endif
                </div>
                <div class="sms-stat-card__value">{{ $counts[$role] ?? 0 }}</div>
                <div class="sms-stat-card__caption">New signups, last 30 days</div>
                <div class="sms-stat-card__sparkline-wrap"><canvas class="sms-stat-card__sparkline" id="sparkline-{{ $role }}" aria-hidden="true"></canvas></div>
            </div>
        @endforeach
    </div>

    <div class="sms-chart-grid">
        <div class="sms-paper sms-paper__body">
            <div class="sms-chart-card__title">New users</div>
            <div class="sms-chart-card__caption">Signups per day, last 30 days, by role</div>
            <div class="sms-chart-card__canvas-wrap">
                <canvas id="chart-trend"></canvas>
            </div>
        </div>

        <div class="sms-paper sms-paper__body">
            <div class="sms-chart-card__title">Users by role</div>
            <div class="sms-chart-card__caption">Current headcount at {{ $school->name }}</div>
            <div class="sms-chart-card__canvas-wrap">
                <canvas id="chart-roles"></canvas>
            </div>
        </div>
    </div>

    <div class="sms-details-grid">
        <div class="sms-paper">
            <div class="sms-paper__body pb-0">
                <div class="sms-chart-card__title">Recently added</div>
                <div class="sms-chart-card__caption mb-0">The last {{ $recentUsers->count() }} people added to this school</div>
            </div>
            <div class="sms-table-wrap">
                <table class="sms-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentUsers as $user)
                            <tr>
                                <td class="fw-medium">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="sms-chip {{ $user->role->value === 'admin' ? 'sms-chip--success' : '' }}">{{ $user->role->label() }}</span></td>
                                <td class="text-nowrap">{{ $user->created_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="sms-empty">No users yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="sms-paper sms-paper__body">
            <div class="sms-chart-card__title mb-2">Quick actions</div>
            <ul class="sms-quick-links">
                <li>
                    <a href="{{ route('school.admin.users.create', ['role' => 'teacher']) }}" class="sms-quick-link">
                        <span class="sms-quick-link__icon material-icons-outlined">menu_book</span>
                        <span class="sms-quick-link__label">Add a teacher</span>
                        <span class="material-icons-outlined">chevron_right</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.admin.users.create', ['role' => 'student']) }}" class="sms-quick-link">
                        <span class="sms-quick-link__icon material-icons-outlined">school</span>
                        <span class="sms-quick-link__label">Add a student</span>
                        <span class="material-icons-outlined">chevron_right</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.admin.users.create', ['role' => 'admin']) }}" class="sms-quick-link">
                        <span class="sms-quick-link__icon material-icons-outlined">shield</span>
                        <span class="sms-quick-link__label">Add an admin</span>
                        <span class="material-icons-outlined">chevron_right</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('school.admin.users.index') }}" class="sms-quick-link">
                        <span class="sms-quick-link__icon material-icons-outlined">group</span>
                        <span class="sms-quick-link__label">View all users</span>
                        <span class="material-icons-outlined">chevron_right</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    @push('scripts')
        <script type="application/json" id="admin-dashboard-data">{!! json_encode($chartData, JSON_UNESCAPED_SLASHES) !!}</script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
        <script>
            (function () {
                var data = JSON.parse(document.getElementById('admin-dashboard-data').textContent);
                var roles = ['admin', 'teacher', 'student'];

                Chart.defaults.font.family = "'Inter', 'Roboto', 'Helvetica Neue', Arial, sans-serif";
                Chart.defaults.color = '#5b6878';

                function sparkline(id, values, color) {
                    var el = document.getElementById(id);
                    if (! el) return;
                    new Chart(el, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: values,
                                borderColor: color,
                                backgroundColor: color + '22',
                                fill: true,
                                tension: 0.35,
                                pointRadius: 0,
                                borderWidth: 2,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: false,
                            scales: { x: { display: false }, y: { display: false } },
                            plugins: { legend: { display: false }, tooltip: { enabled: false } },
                        },
                    });
                }

                sparkline('sparkline-total', data.trendTotal, '#0b6bcb');
                roles.forEach(function (role, i) {
                    sparkline('sparkline-' + role, data.trendByRole[role], data.roleColors[i]);
                });

                var trendEl = document.getElementById('chart-trend');
                if (trendEl) {
                    new Chart(trendEl, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: roles.map(function (role, i) {
                                return {
                                    label: data.roleLabels[i],
                                    data: data.trendByRole[role],
                                    borderColor: data.roleColors[i],
                                    backgroundColor: data.roleColors[i] + '33',
                                    fill: true,
                                    stack: 'signups',
                                    tension: 0.35,
                                    pointRadius: 0,
                                    borderWidth: 2,
                                };
                            }),
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            scales: {
                                x: { grid: { display: false }, ticks: { maxTicksLimit: 8 } },
                                y: { stacked: true, beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#e3e7ec' } },
                            },
                            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } } },
                        },
                    });
                }

                var rolesEl = document.getElementById('chart-roles');
                if (rolesEl) {
                    new Chart(rolesEl, {
                        type: 'bar',
                        data: {
                            labels: data.roleLabels,
                            datasets: [{
                                data: data.roleTotals,
                                backgroundColor: data.roleColors,
                                borderRadius: 6,
                                maxBarThickness: 56,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: { grid: { display: false } },
                                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#e3e7ec' } },
                            },
                            plugins: { legend: { display: false } },
                        },
                    });
                }
            })();
        </script>
    @endpush
</x-school::layouts.admin>
