<x-school::layouts.student title="Dashboard">
    <div class="sms-page-header">
        <div>
            <h1 class="sms-page-title">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="sms-page-subtitle">{{ $school?->name }}</p>
        </div>
    </div>

    <div class="sms-paper sms-paper__body">
        <div class="sms-profile-header">
            <span class="sms-avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <div>
                <h2 class="h6 mb-0">{{ auth()->user()->name }}</h2>
                <span class="sms-chip">Student</span>
            </div>
        </div>

        <div class="sms-detail-row">
            <div class="sms-detail-row__label">Email</div>
            <div class="sms-detail-row__value">{{ auth()->user()->email }}</div>
        </div>
        <div class="sms-detail-row">
            <div class="sms-detail-row__label">School</div>
            <div class="sms-detail-row__value">{{ $school?->name }}</div>
        </div>

        <p class="text-muted mt-3 mb-0" style="font-size: 0.875rem;">
            Your classes, timetable and results are on the way.
        </p>
    </div>
</x-school::layouts.student>
