<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>ROI Store</title>
    @if (class_exists(\Illuminate\Support\Facades\Vite::class))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;1,400&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;line-height:1.6;background:#f8f9fa;color:#1a1a2e;}
        .container{max-width:1200px;margin:0 auto;padding:16px 24px;}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:6px 14px;border-radius:8px;text-decoration:none;color:#fff;background:#1a1a2e;font-size:0.85rem;font-weight:500;border:none;cursor:pointer;transition:all 0.2s;white-space:nowrap;min-height:36px;}
        .btn:hover{background:#2d2d44;transform:translateY(-1px);box-shadow:0 4px 12px rgba(26,26,46,0.15);}
        .btn-secondary{background:#6c757d;}
        .btn-secondary:hover{background:#5a6268;}
        .card{background:#fff;border:1px solid #e9ecef;border-radius:12px;padding:16px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,0.04);}
        .input{width:100%;padding:10px 12px;border:1.5px solid #dee2e6;border-radius:8px;font-size:0.9rem;font-family:inherit;transition:border-color 0.2s;}
        .input:focus{outline:none;border-color:#1a1a2e;box-shadow:0 0 0 3px rgba(26,26,46,0.08);}
        .text-muted{color:#6c757d;}
        .grid-3{display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));}
        .nav-link{display:inline-flex;align-items:center;justify-content:center;padding:6px 12px;border-radius:8px;background:#f1f3f5;color:#1a1a2e;text-decoration:none;font-size:0.85rem;font-weight:500;transition:all 0.2s;white-space:nowrap;}
        .nav-link:hover{background:#e9ecef;}
        .product-image{width:100%;aspect-ratio:1/1;object-fit:cover;border-radius:14px;}
        .badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;font-size:0.8rem;font-weight:500;}
        .badge-green{background:#e8f5e9;color:#2e7d32;}
        .badge-blue{background:#e3f2fd;color:#1565c0;}
        .badge-amber{background:#fff8e1;color:#f57f17;}
        .badge-gray{background:#f1f3f5;color:#495057;}
        .badge-red{background:#fce4ec;color:#c62828;}
        table{width:100%;border-collapse:collapse;font-size:0.9rem;}
        th{padding:12px 16px;border-bottom:2px solid #e9ecef;text-align:left;font-weight:600;color:#495057;background:#f8f9fa;}
        td{padding:12px 16px;border-bottom:1px solid #f1f3f5;color:#1a1a2e;}
        tr:last-child td{border-bottom:none;}
        h1{font-size:1.5rem;font-weight:700;margin-bottom:4px;color:#1a1a2e;}
        h2{font-size:1.2rem;font-weight:600;margin-bottom:12px;color:#1a1a2e;}
        h3{font-size:1.05rem;font-weight:600;margin-bottom:8px;color:#1a1a2e;}
        .stat-card{padding:16px;border-radius:12px;border:1px solid #e9ecef;background:#fff;transition:all 0.2s;}
        .stat-card:hover{box-shadow:0 4px 12px rgba(0,0,0,0.06);}
        .stat-value{font-size:1.3rem;font-weight:700;color:#1a1a2e;}
        .stat-label{font-size:0.8rem;color:#6c757d;margin-top:2px;}
        .nav-badge{position:relative;display:inline-flex;align-items:center;}
        .nav-badge sup{position:absolute;top:-6px;right:-8px;min-width:18px;height:18px;border-radius:9px;font-size:0.65rem;font-weight:700;display:flex;align-items:center;justify-content:center;padding:0 4px;box-shadow:0 2px 4px rgba(0,0,0,0.15);}
        .nav-badge sup.badge-red{background:#dc2626;color:#fff;}
        .nav-badge sup.badge-orange{background:#f97316;color:#fff;}
        .unread-help-badge{position:fixed;bottom:20px;right:20px;z-index:999;animation:pulse 2s infinite;}
        .cart-float{position:fixed;bottom:80px;right:20px;z-index:999;width:56px;height:56px;border-radius:50%;background:#1a1a2e;color:#fff;display:flex;align-items:center;justify-content:center;text-decoration:none;box-shadow:0 4px 16px rgba(26,26,46,0.5);transition:all 0.2s;font-size:1.4rem;}
        .cart-float:hover{transform:scale(1.1);box-shadow:0 6px 24px rgba(26,26,46,0.6);}
        .cart-float sup{position:absolute;top:-6px;right:-6px;min-width:22px;height:22px;border-radius:11px;background:#dc2626;color:#fff;font-size:0.7rem;font-weight:700;display:flex;align-items:center;justify-content:center;padding:0 5px;border:2px solid #1a1a2e;}
        @keyframes pulse{0%{box-shadow:0 0 0 0 rgba(220,38,38,0.4);}70%{box-shadow:0 0 0 15px rgba(220,38,38,0);}100%{box-shadow:0 0 0 0 rgba(220,38,38,0);}}
        
        /* Navbar styles */
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            flex-wrap: nowrap;
        }
        
        .logo {
            font-size: 1.2rem;
            font-weight: 700;
            color: #111;
            text-decoration: none;
            flex-shrink: 0;
        }
        
        .navbar {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: nowrap;
            overflow-x: auto;
            flex-shrink: 0;
        }
        
        .navbar::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>
<body>
    <header class="container" style="margin-bottom:12px;">
        <div class="header-top">
            <a href="{{ url('/') }}" class="logo">ROI Store</a>
            <nav class="navbar">
                @auth
                    @unless(auth()->user()->isAdmin())
                        @php
                            $cartCount = auth()->user()->cartItems()->count();
                            $activeOrdersCount = auth()->user()->orders()->whereNotIn('status', ['delivered', 'cancelled'])->count();
                            $unreadMessagesCount = auth()->user()->customerMessages()->where('seen_by_user', false)
                                ->whereIn('status', ['open', 'answered'])
                                ->count();
                        @endphp
                        <a class="nav-link" href="{{ route('shop.index') }}">Shop</a>
                        <a class="nav-link" href="{{ route('cart.index') }}">
                            Cart
                            @if($cartCount > 0)
                                <sup class="badge-red" style="margin-left:3px;">{{ $cartCount }}</sup>
                            @endif
                        </a>
                        <a class="nav-link" href="{{ route('orders.index') }}">
                            Orders
                            @if($activeOrdersCount > 0)
                                <sup class="badge-orange" style="margin-left:3px;">{{ $activeOrdersCount }}</sup>
                            @endif
                        </a>
                        <a class="nav-link" href="{{ route('wishlist.index') }}">Wishlist</a>
                        <a class="nav-link" href="{{ route('dashboard') }}">Account</a>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="nav-link" style="background:none;border:none;color:#1a1a2e;cursor:pointer;padding:4px 10px;">Logout</button>
                        </form>
                    @else
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="nav-link" style="background:none;border:none;color:#1a1a2e;cursor:pointer;padding:4px 10px;">Logout</button>
                        </form>
                    @endunless
                @else
                    <a class="nav-link" href="{{ route('shop.index') }}">Shop</a>
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="container">
        @include('partials.alerts')
        @yield('content')
    </main>

    @auth
        @unless(auth()->user()->isAdmin())
            @php
                $floatCartCount = auth()->user()->cartItems()->count();
            @endphp
            <a href="{{ route('cart.index') }}" class="cart-float" title="View Cart">
                🛒
                @if($floatCartCount > 0)
                    <sup>{{ $floatCartCount }}</sup>
                @endif
            </a>
        @endunless
    @endauth
</body>
</html>