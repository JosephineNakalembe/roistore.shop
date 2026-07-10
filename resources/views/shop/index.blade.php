@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:1200px;margin:0 auto;padding:12px;">
        <!-- Shop Header with Search -->
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
            <h1 class="shop-title" style="font-size:1.2rem;font-weight:700;margin:0;flex-shrink:0;">Shop</h1>
            <form method="GET" action="{{ route('shop.index') }}" style="flex:1;min-width:0;">
                <input class="input" type="search" name="search" value="{{ $search ?? '' }}" placeholder="Search products..." style="width:100%;padding:8px 12px;font-size:0.9rem;">
            </form>
        </div>

    @php
        $defaults = ['women-s-clothing' => 'Women', 'men-s-clothing' => 'Men', 'kids-baby' => 'Kids & Baby', 'electronics-gadgets' => 'Electronics', 'beauty-personal-care' => 'Beauty'];
        $otherCategories = $categories->filter(fn($c) => !in_array($c->slug, array_keys($defaults)));
        $hasMore = $otherCategories->isNotEmpty();
    @endphp

    <!-- Categories in one line -->
    <div style="display:flex;flex-wrap:nowrap;gap:6px;overflow-x:auto;padding-bottom:4px;margin-bottom:12px;align-items:center;-webkit-overflow-scrolling:touch;scrollbar-width:none;">
        <a href="{{ route('shop.index', array_filter(['search' => $search ?? null])) }}" class="btn btn-secondary" style="padding:4px 12px;border-radius:999px;font-size:0.8rem;flex-shrink:0;{{ !$categorySlug ? 'opacity:1;' : '' }}">All</a>
        @foreach($defaults as $slug => $label)
            <a href="{{ route('shop.index', array_merge(request()->query(), ['category' => $slug])) }}" style="padding:4px 12px;border-radius:999px;font-size:0.8rem;text-decoration:none;font-weight:500;flex-shrink:0;{{ $categorySlug === $slug ? 'background:#1a1a2e;color:#fff;' : 'background:#f1f3f5;color:#1a1a2e;' }}transition:all 0.2s;">{{ $label }}</a>
        @endforeach
        @if($hasMore)
            <button onclick="toggleCategories()" id="catToggle" style="padding:4px 12px;border-radius:999px;font-size:0.8rem;border:none;background:#f1f3f5;color:#1a1a2e;cursor:pointer;font-weight:500;flex-shrink:0;">+{{ $otherCategories->count() }} more</button>
        @endif
    </div>

    @if($hasMore)
    <div id="extraCategories" style="display:none;flex-wrap:wrap;gap:6px;margin-bottom:14px;">
        @foreach($otherCategories as $category)
            <a href="{{ route('shop.index', array_merge(request()->query(), ['category' => $category->slug])) }}" style="padding:4px 12px;border-radius:999px;font-size:0.8rem;text-decoration:none;font-weight:500;{{ $categorySlug === $category->slug ? 'background:#1a1a2e;color:#fff;' : 'background:#f1f3f5;color:#1a1a2e;' }}transition:all 0.2s;">{{ $category->name }}</a>
        @endforeach
    </div>
    @endif

    <!-- Frequently Searched / Suggested Categories -->
    @if($suggestedCategories->isNotEmpty() && !$search && !$categorySlug)
        <div style="margin-bottom:12px;padding:8px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;">
            <p style="font-size:0.75rem;font-weight:600;color:#166534;margin:0 0 4px 0;">🔥 Popular</p>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                @foreach($suggestedCategories as $sCat)
                    <a href="{{ route('shop.index', ['category' => $sCat->slug]) }}" style="padding:2px 8px;border-radius:999px;font-size:0.7rem;text-decoration:none;background:#dcfce7;color:#166534;font-weight:500;">{{ $sCat->name }}</a>
                @endforeach
            </div>
        </div>
    @endif

    <div id="productGrid" class="product-grid">
        @forelse($products as $product)
            <a href="{{ route('shop.show', ['slug' => $product->slug]) }}" class="product-card">
                <img src="{{ optional($product->primaryImage)->path ? asset('storage/' . $product->primaryImage->path) : 'https://via.placeholder.com/400x400' }}" alt="{{ $product->name }}" style="width:100%;aspect-ratio:1/1;object-fit:cover;" loading="lazy">
                <div style="padding:8px 10px 10px;">
                    <h2 style="font-size:0.85rem;font-weight:600;margin-bottom:2px;">{{ $product->name }}</h2>
                    <p style="font-size:0.75rem;color:#6c757d;margin-bottom:2px;">{{ $product->category?->name ?? 'Uncategorized' }}</p>
                    <p style="font-weight:700;font-size:0.95rem;">UGX{{ number_format($product->price, 0) }}</p>
                    @if($product->stock <= 0)
                        <span class="badge badge-red">Out of Stock</span>
                    @elseif($product->stock <= 2)
                        <span style="font-size:0.7rem;color:#c62828;">Only {{ $product->stock }} left</span>
                    @endif
                </div>
            </a>
        @empty
            <div class="card" style="grid-column:1/-1;">No products found.</div>
        @endforelse
    </div>

    <div id="loadingState" style="display:none;text-align:center;margin-top:20px;padding:20px;">
        <div style="display:inline-block;width:32px;height:32px;border:3px solid #e5e7eb;border-top-color:#1a1a2e;border-radius:50%;animation:spin 0.8s linear infinite;"></div>
        <p style="margin-top:8px;color:#6b7280;">Loading more products…</p>
    </div>
    <div id="endState" style="display:none;text-align:center;margin-top:20px;color:#6b7280;padding:20px;">No more products to show.</div>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    <script>
        function toggleCategories() {
            const extra = document.getElementById('extraCategories');
            const btn = document.getElementById('catToggle');
            if (extra.style.display === 'flex') {
                extra.style.display = 'none';
                btn.textContent = '+{{ $otherCategories->count() }} more';
            } else {
                extra.style.display = 'flex';
                btn.textContent = 'Show less';
            }
        }

        let nextPageUrl = @json($products->nextPageUrl());
        let isLoading = false;
        let hasMore = true;

        async function loadMoreProducts() {
            if (!nextPageUrl || isLoading || !hasMore) return;
            isLoading = true;
            document.getElementById('loadingState').style.display = 'block';

            try {
                const response = await fetch(nextPageUrl, { 
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    } 
                });
                
                if (!response.ok) {
                    hasMore = false;
                    document.getElementById('loadingState').style.display = 'none';
                    document.getElementById('endState').style.display = 'block';
                    isLoading = false;
                    return;
                }

                const data = await response.json();
                
                if (data.html) {
                    const temp = document.createElement('div');
                    temp.innerHTML = data.html;
                    const cards = temp.querySelectorAll('.product-card');
                    cards.forEach(card => document.getElementById('productGrid').appendChild(card));
                }
                
                nextPageUrl = data.next_page_url || null;
                
                if (!nextPageUrl) {
                    hasMore = false;
                    document.getElementById('endState').style.display = 'block';
                }
            } catch (e) {
                hasMore = false;
                document.getElementById('endState').textContent = 'Error loading products.';
                document.getElementById('endState').style.display = 'block';
            }

            document.getElementById('loadingState').style.display = 'none';
            isLoading = false;
        }

        // Infinite scroll with debounce
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            if (scrollTimeout) clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 800) {
                    loadMoreProducts();
                }
            }, 100);
        });

        // Also load more when page is near bottom on load
        document.addEventListener('DOMContentLoaded', () => {
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 800) {
                loadMoreProducts();
            }
        });
    </script>
@endsection