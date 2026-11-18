{{--
    Shop favicon: the shop's uploaded logo, or the store mark (Bootstrap "shop" icon on a soft
    tinted square) in the shop's brand colour. Params: $color (#rrggbb or rrggbb), optional $logoUrl.
--}}
@php
    $hex = ltrim($color ?? 'f97316', '#');
    [$r, $g, $b] = [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    $tint = sprintf('%02x%02x%02x', (int) round($r + (255 - $r) * .86), (int) round($g + (255 - $g) * .86), (int) round($b + (255 - $b) * .86));
    $svg  = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='#{$tint}'/>"
          . "<g transform='translate(8 8)' fill='#{$hex}'><path d='M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.37 2.37 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0M1.5 8.5A.5.5 0 0 1 2 9v6h1v-5a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v5h6V9a.5.5 0 0 1 1 0v6h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1V9a.5.5 0 0 1 .5-.5M4 15h3v-5H4zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1zm3 0h-2v3h2z'/></g></svg>";
@endphp
@if (! empty($logoUrl))
    <link rel="icon" href="{{ $logoUrl }}">
@else
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,{{ rawurlencode($svg) }}">
@endif
