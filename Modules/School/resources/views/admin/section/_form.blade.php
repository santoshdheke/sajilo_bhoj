@php $section = $section ?? null; @endphp
<div class="row g-3">
    <div class="col-md-6">
        <label for="classroom_id" class="form-label">Class</label>
        @if ($section)
            <input type="text" class="form-control" value="{{ $section->classroom->name }}" disabled>
        @else
            <select id="classroom_id" name="classroom_id" class="form-select @error('classroom_id') is-invalid @enderror" required>
                <option value="">Select class…</option>
                @foreach ($classrooms as $c)
                    <option value="{{ $c->id }}" @selected((int) old('classroom_id', $presetClassroom ?? 0) === $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            @error('classroom_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @endif
    </div>
    <div class="col-md-6">
        <label for="name" class="form-label">Name</label>
        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $section?->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="capacity" class="form-label">Capacity</label>
        <input type="number" id="capacity" name="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', $section?->capacity) }}" min="1">
        @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="class_teacher_id" class="form-label">Class teacher</label>
        <select id="class_teacher_id" name="class_teacher_id" class="form-select @error('class_teacher_id') is-invalid @enderror">
            <option value="">None</option>
            @foreach ($teachers as $t)
                <option value="{{ $t->id }}" @selected((int) old('class_teacher_id', $section?->class_teacher_id ?? 0) === $t->id)>{{ $t->name }}</option>
            @endforeach
        </select>
        @error('class_teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
