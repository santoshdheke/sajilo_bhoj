@extends('shop::admin.theme_one.component.layout')
@section('title', $sections[$section]['label'].' Settings')

@section('content')
    <div class="page-title mb-4">
        <h2>{{ $sections[$section]['label'] }} Settings</h2>
        <p>Website settings that power the storefront pages directly.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('shop.admin.setting.update', $section) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('shop::admin.theme_one.setting.partials.' . $section)

        <div class="settings-actions mt-4 d-flex flex-wrap align-items-center gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Settings</button>
            @if ($section === 'theme')
                <button type="submit" name="reset_theme" value="1" class="btn theme-reset-btn" formnovalidate
                        onclick="return confirm('Reset the colours to the default (orange #f97316)? This saves immediately.')">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset to default
                </button>
            @endif
        </div>
    </form>
@endsection
