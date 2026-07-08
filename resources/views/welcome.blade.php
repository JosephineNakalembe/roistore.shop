@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:520px;margin:0 auto;padding:24px 24px 28px;text-align:center;">
        <h1 style="font-size:1.4rem;margin-bottom:8px;">Welcome to ROI Store</h1>
        <p class="text-muted" style="font-size:0.95rem;line-height:1.6;margin-bottom:16px;">
            Browse products, add items to your cart, checkout securely, and track orders from a single login.
        </p>

        <div style="display:flex;justify-content:center;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
            <a class="btn" href="{{ route('shop.index') }}" style="padding:9px 16px;font-size:0.9rem;">Shop Now</a>
            @guest
                <a class="btn btn-secondary" href="{{ route('register') }}" style="padding:9px 16px;font-size:0.9rem;">Create Account</a>
            @endguest
        </div>

        <div style="border-top:1px solid #e9ecef;padding-top:14px;text-align:left;">
            <h2 style="font-size:1rem;margin-bottom:6px;">Unified buyer and admin flow</h2>
            <p class="text-muted" style="font-size:0.9rem;line-height:1.5;margin:0;">
                Users sign in with email and password only. Admin access is granted by role after login, without a separate admin sign-in page.
            </p>
        </div>
    </div>
@endsection
