@php $classroom = $classroom ?? null; @endphp
<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Name</label>
        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $classroom?->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="display_order" class="form-label">Display order</label>
        <input type="number" id="display_order" name="display_order" class="form-control @error('display_order') is-invalid @enderror" value="{{ old('display_order', $classroom?->display_order ?? 0) }}" min="0">
        @error('display_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="status" class="form-label">Status</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
            @foreach (\Modules\School\Enums\ClassroomStatusEnum::cases() as $s)
                <option value="{{ $s->value }}" @selected(old('status', $classroom?->status?->value ?? 'active') === $s->value)>{{ $s->label() }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
