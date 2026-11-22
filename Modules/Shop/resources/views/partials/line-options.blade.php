{{-- Chosen attributes + order form answers of a cart/order line. Usage: @include('shop::partials.line-options', ['attributes' => $line->attribute_description]) --}}
@php $lineOptions = \Modules\Shop\Support\ProductOptions::describe($attributes ?? []); @endphp
@if ($lineOptions)
    <div style="font-size:12px;color:#6b7280;line-height:1.5;margin-top:2px;">
        @foreach ($lineOptions as $o)<span style="white-space:normal;word-break:break-word;"><strong style="font-weight:600;color:#4b5563;">{{ $o['title'] }}:</strong> {{ $o['value'] }}</span>@if (! $loop->last)<span style="margin:0 6px;opacity:.5;">·</span>@endif @endforeach
    </div>
@endif
