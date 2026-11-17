{{-- From / to filter for reports. --}}
<form class="d-flex flex-wrap gap-2 align-items-end" method="get">
    <div><label class="form-label small mb-1">From</label><input class="form-control form-control-sm" type="date" name="from" value="{{ $from->toDateString() }}"></div>
    <div><label class="form-label small mb-1">To</label><input class="form-control form-control-sm" type="date" name="to" value="{{ $to->toDateString() }}"></div>
    <button class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i>Apply</button>
    <a class="btn btn-sm btn-outline-secondary" href="{{ url()->current() }}?from={{ now()->toDateString() }}&to={{ now()->toDateString() }}">Today</a>
    <a class="btn btn-sm btn-outline-secondary" href="{{ url()->current() }}">This month</a>
</form>
