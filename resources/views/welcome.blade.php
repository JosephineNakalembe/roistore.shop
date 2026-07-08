@extends('layouts.app')

@section('content')
    <div class="card welcome-card" style="width:min(100%, 420px);max-width:420px;margin:0 auto;padding:20px 22px 24px;text-align:center;">
        <h1 style="font-size:1.3rem;margin-bottom:8px;">Welcome to ROI Store</h1>
        <p class="text-muted" style="font-size:0.9rem;line-height:1.5;margin-bottom:14px;">
            Browse products, add items to your cart, checkout securely, and track orders from a single login.
        </p>

        <div style="display:flex;justify-content:center;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
            <a class="btn" href="{{ route('shop.index') }}" style="padding:8px 14px;font-size:0.85rem;">Shop Now</a>
            @guest
                <a class="btn btn-secondary" href="{{ route('register') }}" style="padding:8px 14px;font-size:0.85rem;">Create Account</a>
            @endguest
        </div>

        <div style="border-top:1px solid #e9ecef;padding-top:12px;text-align:left;">
            <h2 style="font-size:0.95rem;margin-bottom:6px;">Unified buyer and admin flow</h2>
            <p class="text-muted" style="font-size:0.85rem;line-height:1.45;margin:0;">
                Users sign in with email and password only. Admin access is granted by role after login, without a separate admin sign-in page.
            </p>
        </div>
    </div>
@endsection
