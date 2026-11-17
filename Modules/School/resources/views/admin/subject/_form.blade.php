@php $subject = $subject ?? null; $selected = collect(old('classroom_ids', $subject?->classrooms->pluck('id')->all() ?? [])); @endphp
<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Name</label>
        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $subject?->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="code" class="form-label">Code</label>
        <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $subject?->code) }}" >
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="type" class="form-label">Type</label>
        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror">
            @foreach (\Modules\School\Enums\SubjectTypeEnum::cases() as $t)
                <option value="{{ $t->value }}" @selected(old('type', $subject?->type?->value ?? 'compulsory') === $t->value)>{{ $t->label() }}</option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <span class="form-label d-block">Taught in classes</span>
        <input type="hidden" name="classroom_ids_submitted" value="1">
        @forelse ($classrooms as $c)
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="classroom_ids[]" id="classroom_{{ $c->id }}" value="{{ $c->id }}" @checked($selected->contains($c->id))>
                <label class="form-check-label" for="classroom_{{ $c->id }}">{{ $c->name }}</label>
            </div>
        @empty
            <div class="form-text">Add classes first to map subjects to them.</div>
        @endforelse
        @error('classroom_ids.*')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
</div>
