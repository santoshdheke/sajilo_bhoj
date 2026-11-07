@extends('admin.theme_one.component.layout')

@section('content')
    <div class="page-title mb-4">
        <h2>{{ $sections[$section]['label'] }} Settings</h2>
        <p>Website settings that power the storefront pages directly.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update', $section) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.theme_one.setting.partials.' . $section)

        <div class="settings-actions mt-4 d-flex flex-wrap align-items-center gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Settings</button>
            @if ($section === 'theme')
                @php $defaultColor = \App\Http\Controllers\Admin\SettingController::SECTIONS['theme']['fields']['theme_color']; @endphp
                <button type="submit" name="reset_theme" value="1" class="btn theme-reset-btn" formnovalidate
                        onclick="return confirm('Reset the brand colour to the default ({{ $defaultColor }})? This saves immediately.')">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset to default
                </button>
            @endif
        </div>
    </form>
@endsection
