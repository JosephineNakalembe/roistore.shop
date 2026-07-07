@extends('layouts.app')

@section('content')
    <div class="shop-header">
        <h1 class="shop-title">Shop</h1>
        <form method="GET" action="{{ route('shop.index') }}" class="shop-filter-bar">
            <input class="input" type="search" name="search" value="{{ $search ?? '' }}" placeholder="Search products...">
            <select class="input" name="category">
                <option value="">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}"{{ $categorySlug === $category->slug ? ' selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <button class="btn" type="submit">Filter</button>
        </form>
    </div>

    @php
        $defaults = ['women-s-clothing' => 'Women\'s Clothing', 'men-s-clothing' => 'Men\'s Clothing', 'kids-baby' => 'Kids & Baby', 'electronics-gadgets' => 'Electronics', 'beauty-personal-care' => 'Beauty'];
        $otherCategories = $categories->filter(fn($c) => !in_array($c->slug, array_keys($defaults)));
        $hasMore = $otherCategories->isNotEmpty();
    @endphp

    <div class="categories-container">
        <a href="{{ route('shop.index', array_filter(['search' => $search ?? null])) }}" class="btn btn-secondary category-pill" style="padding:6px 14px;border-radius:999px;font-size:0.85rem;{{ !$categorySlug ? 'opacity:1;' : '' }}">All</a>
        @foreach($defaults as $slug => $label)
            <a href="{{ route('shop.index', array_merge(request()->query(), ['category' => $slug])) }}" class="category-pill" style="padding:6px 14px;border-radius:999px;font-size:0.85rem;text-decoration:none;font-weight:500;{{ $categorySlug === $slug ? 'background:#1a1a2e;color:#fff;' : 'background:#f1f3f5;color:#1a1a2e;' }}transition:all 0.2s;">{{ $label }}</a>
        @endforeach
        @if($hasMore)
            <button onclick="toggleCategories()" id="catToggle" class="category-pill" style="padding:6px 14px;border-radius:999px;font-size:0.85rem;border:none;background:#f1f3f5;color:#1a1a2e;cursor:pointer;font-weight:500;">+{{ $otherCategories->count() }} more</button>
        @endif
    </div>

    @if($hasMore)
    <div id="extraCategories" class="categories-section" style="display:none;">
        @foreach($otherCategories as $category)
            <a href="{{ route('shop.index', array_merge(request()->query(), ['category' => $category->slug])) }}" class="category-pill" style="padding:6px 14px;border-radius:999px;font-size:0.85rem;text-decoration:none;font-weight:500;{{ $categorySlug === $category->slug ? 'background:#1a1a2e;color:#fff;' : 'background:#f1f3f5;color:#1a1a2e;' }}transition:all 0.2s;">{{ $category->name }}</a>
        @endforeach
    </div>
    @endif

    <div id="productGrid" class="product-grid">
        @forelse($products as $product)
            <a href="{{ route('shop.show', $product->slug) }}" class="product-card">
                <img src="{{ optional($product->primaryImage)->path ? asset('storage/' . $product->primaryImage->path) : 'https://via.placeholder.com/400x400' }}" alt="{{ $product->name }}" loading="lazy">
                <div>
                    <h2>{{ $product->name }}</h2>
                    <p class="text-muted">{{ $product->category?->name ?? 'Uncategorized' }}</p>
                    <p style="font-weight:700;">UGX{{ number_format($product->price, 0) }}</p>
                    @if($product->stock <= 0)
                        <span class="badge badge-red">Out of Stock</span>
                    @elseif($product->stock <= 2)
                        <span style="font-size:0.75rem;color:#c62828;">Only {{ $product->stock }} left</span>
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

        let scrollTimeout;
        window.addEventListener('scroll', () => {
            if (scrollTimeout) clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 800) {
                    loadMoreProducts();
                }
            }, 100);
        });

        document.addEventListener('DOMContentLoaded', () => {
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 800) {
                loadMoreProducts();
            }
        });
    </script>
@endsection
