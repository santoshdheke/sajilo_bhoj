<div class="card-soft">
    <div class="card-body">
        <h5 class="mb-1">Store policies</h5>
        <p class="text-secondary mb-4">Linked in the store footer. Leave a box empty to show a standard text with your store name.</p>
        <div class="row g-3">
            @foreach (\Modules\Shop\Models\ShopSetting::POLICIES as $field => [$slug, $label, $default])
                <div class="col-12">
                    <label class="form-label fw-semibold d-flex justify-content-between">{{ $label }}
                        <a class="small fw-normal" href="{{ route('shop.front.page.'.($slug === 'refund-policy' ? 'refund' : $slug)) }}" target="_blank">View page <i class="bi bi-box-arrow-up-right"></i></a></label>
                    <textarea class="form-control" name="{{ $field }}" rows="5" maxlength="20000" placeholder="{{ str_replace(':shop', $company->name, $default) }}">{{ old($field, $settings->$field) }}</textarea>
                </div>
            @endforeach
        </div>
    </div>
</div>
