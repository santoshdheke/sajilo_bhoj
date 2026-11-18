@extends($theme_path . 'common.layout.master_layout')
@section('title', $pageTitle.' - '.$shop->name)

@section('contant')
<div class="container" style="padding:48px 30px 72px;max-width:900px;">
    <p style="font-size:12px;color:#999;margin-bottom:12px;"><a href="{{ route('shop.front.home') }}" style="color:#{{ $brand['primary'] }};">Home</a> &nbsp;/&nbsp; {{ $pageTitle }}</p>
    <h1 style="font-size:28px;font-weight:700;margin-bottom:24px;">{{ $pageTitle }}</h1>
    <div style="line-height:1.9;color:#444;font-size:15px;">{!! nl2br(e($pageText)) !!}</div>
</div>
@endsection
