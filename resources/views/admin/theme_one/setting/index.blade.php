@extends('admin.theme_one.component.layout')

@section('content')
    <div class="page-title mb-4">
        <h2>{{ $sections[$section]['label'] }} Settings</h2>
        <p>Website settings that power the storefront pages directly.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update', $section) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.theme_one.setting.partials.' . $section)

        <div class="mt-4">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Settings</button>
        </div>
    </form>
@endsection
