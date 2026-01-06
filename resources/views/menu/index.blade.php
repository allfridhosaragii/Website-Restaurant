@extends('layouts.guest')
@section('title', __('messages.our_menu'))
@section('content')
<section class="bg-gradient-primary text-white pb-5" style="margin-top: -80px; padding-top: 120px;">
    <div class="container-fluid px-4 px-lg-5">
        <div class="row align-items-center">
            <div class="col-12">
                <h1 class="display-5 fw-bold mb-3" data-i18n="our_menu">{{ __('messages.our_menu') }}</h1>
                <p class="lead opacity-75 mb-0" data-i18n="menu_desc">
                    {{ __('messages.menu_desc') }}
                </p>
            </div>
        </div>
    </div>
</section>
<section class="section bg-cream">
    <div class="container-fluid px-4 px-lg-5">
        <div class="row mb-5">
            <div class="col-lg-6 mb-3 mb-lg-0">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 ps-0" 
                           placeholder="{{ __('messages.search_placeholder') }}" id="searchMenu" data-i18n="search_placeholder">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-flex gap-2 flex-wrap justify-content-lg-end">
                    <button class="btn btn-primary active filter-btn" data-category="all" data-i18n="cat_all">{{ __('messages.cat_all') }}</button>
                    <button class="btn btn-outline-primary filter-btn" data-category="nasi & mie" data-i18n="cat_rice_noodle">{{ __('messages.cat_rice_noodle') }}</button>
                    <button class="btn btn-outline-primary filter-btn" data-category="lauk pauk" data-i18n="cat_dishes">{{ __('messages.cat_dishes') }}</button>
                    <button class="btn btn-outline-primary filter-btn" data-category="minuman" data-i18n="cat_drinks">{{ __('messages.cat_drinks') }}</button>
                    <button class="btn btn-outline-primary filter-btn" data-category="dessert" data-i18n="cat_dessert">{{ __('messages.cat_dessert') }}</button>
                </div>
            </div>
        </div>
        <div class="row g-4" id="menuGrid">
            @forelse($menus as $index => $menu)
            <div class="col-6 col-md-4 col-lg-3 menu-item" data-category="{{ strtolower($menu->category) }}">
                <div class="card menu-card h-100">
                    <div class="position-relative">
                        @if($menu->image_url)
                            <img src="{{ $menu->image_url }}" 
                                 class="card-img-top" alt="{{ $menu->name }}" style="height: 200px; object-fit: cover;"
                                 loading="lazy" decoding="async"
                                 onerror="this.onerror=null; this.src='https://res.cloudinary.com/dh9ysyfit/image/fetch/w_400,h_300,c_fill,f_auto,q_auto/https://images.unsplash.com/photo-1546069901-ba9599a7e63c';">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-image fs-1 text-muted"></i>
                            </div>
                        @endif
                        @if($index == 0)
                        <span class="trending-badge">
                            <i class="bi bi-fire"></i> <span data-i18n="trending">{{ __('messages.trending') }}</span>
                        </span>
                        @endif
                        <span class="price-tag">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title">{{ $menu->name }}</h6>
                        <div class="d-flex align-items-center mb-2">
                            <div class="rating-stars small">
                                <i class="bi bi-star-fill filled"></i>
                                <i class="bi bi-star-fill filled"></i>
                                <i class="bi bi-star-fill filled"></i>
                                <i class="bi bi-star-fill filled"></i>
                                <i class="bi bi-star-fill filled"></i>
                            </div>
                            <small class="text-muted ms-2">({{ rand(50, 300) }})</small>
                        </div>
                        <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($menu->description, 60) }}</p>
                        <button class="btn btn-primary btn-sm w-100 mt-auto" style="isolation: isolate; position: relative; z-index: 2;" onclick="addToCart(this, {{ $menu->id }})">
                            <i class="bi bi-cart-plus me-1"></i> <span data-i18n="add">{{ __('messages.add') }}</span>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted" data-i18n="no_menu">{{ __('messages.no_menu') }}</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
<section class="section bg-gradient-primary text-white text-center">
    <div class="container-fluid px-4 px-lg-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h3 class="text-white mb-3" data-i18n="want_order">{{ __('messages.want_order') }}</h3>
                <p class="opacity-75 mb-4" data-i18n="order_desc">
                    {{ __('messages.order_desc') }}
                </p>
                @guest
                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ url('/login') }}" class="btn btn-secondary btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i><span data-i18n="login">{{ __('messages.login') }}</span>
                    </a>
                    <a href="{{ url('/register') }}" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-person-plus me-2"></i><span data-i18n="register">{{ __('messages.register') }}</span>
                    </a>
                </div>
                @else
                <a href="{{ url('/customer/orders/create') }}" class="btn btn-secondary btn-lg">
                    <i class="bi bi-cart-plus me-2"></i><span data-i18n="create_order">{{ __('messages.create_order') }}</span>
                </a>
                @endguest
            </div>
        </div>
    </div>
    </div>
</section>

<!-- Floating Cart Button -->
<div id="floatingCartContainer" class="cart-float-container">
    <button type="button" class="cart-float-btn" onclick="toggleCartPanel()">
        <i class="bi bi-bag-fill"></i>
        <span class="cart-float-badge" id="cartCountBadge">0</span>
    </button>
</div>

<!-- Slide-out Cart Panel -->
<div class="cart-overlay" id="cartOverlay" onclick="closeCartPanel()"></div>
<div class="cart-panel" id="cartPanel">
    <div class="cart-panel-header">
        <span class="cart-panel-title">Keranjang <span id="cartHeaderCount"></span></span>
        <button class="cart-panel-close" onclick="closeCartPanel()">
            <i class="bi bi-x"></i>
        </button>
    </div>
    <div class="cart-panel-body" id="cartModalBody"></div>
    <div class="cart-panel-footer">
        <div class="cart-footer-total">
            <span>Total</span>
            <span id="cartModalTotal">Rp 0</span>
        </div>
        <a href="{{ url('/customer/orders/create') }}" class="cart-footer-btn" id="btnCheckout">
            Checkout <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</div>
@endsection
@push('styles')
<style>
    .pagination .page-link {
        border: none;
        color: var(--gray-600);
        padding: 0.75rem 1rem;
        margin: 0 0.25rem;
        border-radius: var(--radius-md);
    }
    .pagination .page-link:hover {
        background: var(--gray-200);
        color: var(--primary);
    }
    .pagination .page-item.active .page-link {
        background: var(--primary);
        color: var(--white);
    }
    
    /* Mobile Responsive - Compact E-commerce Style */
    @media (max-width: 576px) {
        #menuGrid {
            gap: 0.5rem !important;
            display: flex !important;
            flex-wrap: wrap !important;
        }
        #menuGrid .menu-item {
            flex: 0 0 calc(50% - 0.25rem) !important;
            max-width: calc(50% - 0.25rem) !important;
            width: calc(50% - 0.25rem) !important;
            padding: 0.25rem !important;
        }
        .menu-card {
            border-radius: 8px !important;
            border-width: 1px !important;
        }
        .menu-card .card-img-top {
            height: 120px !important;
            border-radius: 8px 8px 0 0 !important;
        }
        .menu-card .card-body {
            padding: 0.5rem !important;
        }
        .menu-card .card-title {
            font-size: 0.85rem !important;
            margin-bottom: 0.25rem !important;
            line-height: 1.2 !important;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .menu-card .rating-stars {
            font-size: 0.65rem !important;
        }
        .menu-card .rating-stars + small {
            font-size: 0.65rem !important;
        }
        .menu-card .text-muted.small {
            font-size: 0.7rem !important;
            margin-bottom: 0.5rem !important;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .menu-card .price-tag {
            font-size: 0.7rem !important;
            padding: 0.15rem 0.35rem !important;
        }
        .menu-card .trending-badge {
            font-size: 0.6rem !important;
            padding: 0.15rem 0.35rem !important;
        }
        .menu-card .btn-sm {
            font-size: 0.75rem !important;
            padding: 0.35rem 0.5rem !important;
        }
        .menu-card .badge.bg-light {
            font-size: 0.6rem !important;
            padding: 0.2rem 0.4rem !important;
        }
        .menu-card .btn-favorite {
            width: 28px !important;
            height: 28px !important;
        }
        .menu-card .btn-favorite i {
            font-size: 0.85rem !important;
        }
    }
</style>
@endpush
@push('styles')
<style>
    .navbar-culinaire:not(.scrolled) .nav-link,
    .navbar-culinaire:not(.scrolled) .navbar-brand {
        color: #ffffff !important;
    }
    .navbar-culinaire:not(.scrolled) .navbar-brand span {
        color: #D4AF37 !important;
    }
    .navbar-culinaire:not(.scrolled) .btn-outline-primary {
        color: #fff !important;
        border-color: #fff !important;
    }
    .navbar-culinaire:not(.scrolled) .btn-outline-primary:hover {
        background-color: #fff !important;
        color: #333 !important;
    }
</style>
@endpush
@push('scripts')
<script>
    // Use .filter-btn selector to target only filter buttons, not menu-item containers
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.dataset.category;
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active', 'btn-primary'));
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.add('btn-outline-primary'));
            this.classList.remove('btn-outline-primary');
            this.classList.add('active', 'btn-primary');
            document.querySelectorAll('.menu-item').forEach(item => {
                if (category === 'all' || item.dataset.category.includes(category)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    document.getElementById('searchMenu').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.menu-item').forEach(item => {
            const name = item.querySelector('.card-title').textContent.toLowerCase();
            if (name.includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    function toggleFavorite(btn, menuId) {
        // CSRF Token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Optimistic UI Update
        const icon = btn.querySelector('i');
        const isFav = icon.classList.contains('bi-heart-fill');
        
        // Toggle Icon immediately
        if (isFav) {
            icon.classList.remove('bi-heart-fill', 'text-danger');
            icon.classList.add('bi-heart');
            icon.style.color = ''; // Reset color
        } else {
            icon.classList.remove('bi-heart');
            icon.classList.add('bi-heart-fill', 'text-danger');
            icon.style.color = '#dc3545';
        }
        
        // Add animation class
        btn.classList.add('animate-pulse');
        setTimeout(() => btn.classList.remove('animate-pulse'), 300);

        fetch(`/customer/favorite/${menuId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.status === 401 || response.status === 419) {
                // Unauthorized - Redirect to login
                window.location.href = "{{ route('login') }}";
                return;
            }
            if (!response.ok) {
                // Revert on failure
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data && data.message) {
                // Optional: Show toast
                console.log(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert UI on error
            if (isFav) {
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill', 'text-danger');
                icon.style.color = '#dc3545';
            } else {
                icon.classList.remove('bi-heart-fill', 'text-danger');
                icon.classList.add('bi-heart');
                icon.style.color = '';
            }
            alert('Failed to update favorite. Please try again.');
        });
    }

    // Cart Functions
    document.addEventListener('DOMContentLoaded', function() {
        updateCartCount();
        
        // Polling for real-time updates (every 1 second)
        setInterval(updateCartCount, 1000);
        
        // Immediate update when tab becomes visible
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                updateCartCount();
            }
        });
    });

    function updateCartCount() {
        fetch('/customer/cart/count')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('floatingCartContainer');
                const badge = document.getElementById('cartCountBadge');
                
                if (data.count > 0) {
                    container.classList.add('show');
                    if (badge) {
                        badge.textContent = data.count;
                        badge.style.display = 'flex';
                    }
                } else {
                    container.classList.remove('show');
                    if (badge) badge.style.display = 'none';
                }
            })
            .catch(err => console.error(err));
    }

    function toggleCartPanel() {
        const panel = document.getElementById('cartPanel');
        const overlay = document.getElementById('cartOverlay');
        
        if (panel.classList.contains('open')) {
            closeCartPanel();
        } else {
            panel.classList.add('open');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
            loadCartItems();
        }
    }

    function closeCartPanel() {
        const panel = document.getElementById('cartPanel');
        const overlay = document.getElementById('cartOverlay');
        panel.classList.remove('open');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    function addToCart(btn, menuId) {
        @guest
            window.location.href = "{{ route('login') }}";
            return;
        @endguest

        const originalContent = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        btn.disabled = true;

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/customer/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ menu_id: menuId, quantity: 1 })
        })
        .then(async response => {
            if (response.status === 401 || response.status === 419) {
                window.location.href = "{{ route('login') }}";
                return;
            }
            const data = await response.json().catch(() => null);
            if (!data) throw new Error('Invalid response');
            if (!response.ok) throw new Error(data.message || 'Error');
            return data;
        })
        .then(data => {
            if (!data) return;
            updateCartCount();
            btn.innerHTML = '<i class="bi bi-check-lg"></i>';
            btn.classList.replace('btn-primary', 'btn-success');
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.classList.replace('btn-success', 'btn-primary');
                btn.disabled = false;
            }, 1000);
        })
        .catch(error => {
            console.error(error);
            btn.innerHTML = originalContent;
            btn.disabled = false;
            alert('Gagal: ' + error.message);
        });
    }

    function loadCartItems() {
        const modalBody = document.getElementById('cartModalBody');
        modalBody.innerHTML = '<div class="cart-loading"><div class="cart-spinner"></div></div>';
        
        fetch('/customer/cart')
            .then(res => res.json())
            .then(data => {
                renderCartModal(data);
                const badge = document.getElementById('cartCountBadge');
                if(badge) badge.textContent = data.count || 0;
            })
            .catch(err => {
                modalBody.innerHTML = '<div class="cart-error">Gagal memuat keranjang</div>';
                console.error(err);
            });
    }

    function openCartModal() {
        toggleCartPanel();
    }

    function renderCartModal(data) {
        const modalBody = document.getElementById('cartModalBody');
        const modalTotal = document.getElementById('cartModalTotal');
        const btnCheckout = document.getElementById('btnCheckout');
        const headerCount = document.getElementById('cartHeaderCount');
        
        // Update header count
        if (headerCount) {
            headerCount.textContent = data.items ? `(${data.items.length})` : '';
        }
        
        if (!data.items || data.items.length === 0) {
            modalBody.innerHTML = `
                <div class="cart-empty">
                    <div class="cart-empty-icon">
                        <i class="bi bi-bag"></i>
                    </div>
                    <p class="cart-empty-text">Keranjang masih kosong</p>
                    <button class="cart-start-btn" data-bs-dismiss="modal">
                        Mulai Belanja
                    </button>
                </div>
            `;
            modalTotal.innerText = 'Rp 0';
            btnCheckout.classList.add('disabled');
            return;
        }

        btnCheckout.classList.remove('disabled');
        modalTotal.innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(data.total);

        let html = '<div class="cart-items-list">';
        data.items.forEach(item => {
            if (!item.menu) return;

            const subtotal = item.menu.price * item.quantity;
            html += `
                <div class="cart-item">
                    <img src="${item.menu.image_url}" alt="${item.menu.name}" class="cart-item-img">
                    <div class="cart-item-info">
                        <h6 class="cart-item-name">${item.menu.name}</h6>
                        <span class="cart-item-price">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(subtotal)}</span>
                    </div>
                    <div class="cart-item-actions">
                        <div class="cart-qty-control">
                            <button class="cart-qty-btn" onclick="updateCartItem(${item.id}, ${item.quantity}, -1)" ${item.quantity <= 1 ? 'disabled' : ''}>
                                <i class="bi bi-dash"></i>
                            </button>
                            <span class="cart-qty-value">${item.quantity}</span>
                            <button class="cart-qty-btn" onclick="updateCartItem(${item.id}, ${item.quantity}, 1)">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                        <button class="cart-remove-btn" onclick="removeCartItem(${item.id})">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        modalBody.innerHTML = html;
    }

    function updateCartItem(id, change) {
        // Optimistic UI could be added here, but for safety we'll wait for server
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Use existing function to get current quantity? 
        // We'll just try to "GET" current state? No, "update" endpoint takes absolute quantity.
        // Wait, my controller update takes "quantity" (absolute). 
        // I need to know CURRENT quantity to add "change".
        // Solution: Polling maintains state, or I finding item in DOM?
        // Better: Backend "add" endpoint handles incremental?
        // No, 'update' replaces.
        
        // I must allow logic to just call "add" for +1.
        // But for -1?
        // Let's first Find the item in local data? 
        // Simplest: Pass Current Qty in render logic? 
        // Refactor renderCartModal to include current qty in logic?
        // No, I can cheat: fetch individual logic or just use "Add" logic?
        // Actually, CartController::add handles "increment" if item exists.
        // But I don't have "decrement" logic in "add".
        
        // Let's use PUT to /customer/cart/{id}. But I need "newQuantity".
        // I can grab it from DOM?
        // Yes, let's grab it from the span sibling.
        // OR better: reload the whole modal every click? (Slightly slow but reliable).
        
        // Wait, Render has: onclick="updateCartItem(item.id, +/-1)".
        // I'll grab the current qty from the span next to the button.
        // This is a bit hacky but works.
        // Better: Pass current qty to function? `updateCartItem(id, currentQty, change)`
        // Rewriting render to pass `item.quantity`.
        
        // Let's pause and rewrite render to use: `updateCartItem(id, ${item.quantity} + ${change})`?
        // No, `updateCartItem(${item.id}, ${item.quantity + change})`.
        // Wait, JS template string evaluates immediately.
        // So `onclick="updateItem(5, 2)"`.
        // If I click +, I want it to become 3.
        // But if I click again without re-render, it sends 3 again (no change).
        // Solution: Re-render Modal on each update.
        
        // So:
        // 1. Call API with (Current + Change).
        // 2. On Success -> openCartModal() (re-fetch & re-render).
        // BUT I need correct Current Qty.
        // If I rely on re-render, I can just hardcode "1" and "-1" and handle logic in backend?
        // No, backend update expects absolute.
        
        // Re-render approach:
        // HTML: onclick="tempUpdate(${item.id}, ${item.quantity}, 1)"
        // Function: calculates new qty, calls API, then re-fetches.
    }
    
    function updateCartItem(id, currentQty, change) {
        const newQty = currentQty + change;
        if (newQty < 1) return; 
        
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/customer/cart/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: newQty })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                openCartModal(); // Refresh modal
                updateCartCount(); // Refresh badge
            }
        })
        .catch(err => console.error(err));
    }

    function removeCartItem(id) {
        if(!confirm('Hapus menu ini dari keranjang?')) return;

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/customer/cart/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                openCartModal(); // Refresh modal
                updateCartCount(); // Refresh badge
            }
        })
        .catch(err => console.error(err));
    }


    // Wait, updateCartItem definition in Render needs to change.

</script>
<style>
    .animate-pulse {
        animation: pulse 0.3s ease-in-out;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    .btn-favorite {
        transition: all 0.2s ease;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(4px);
    }
    .btn-favorite:hover {
        transform: scale(1.1);
        background: white;
    }

    /* ===== ULTRA-MINIMAL CART PANEL ===== */
    
    /* Floating Button */
    .cart-float-container {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1000;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
        pointer-events: none;
    }
    
    .cart-float-container.show {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }
    
    .cart-float-btn {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        border: none;
        background: #C89B3A;
        color: #fff;
        font-size: 1.25rem;
        cursor: pointer;
        position: relative;
        box-shadow: 0 4px 20px rgba(200, 155, 58, 0.4);
        transition: all 0.25s ease;
    }
    
    .cart-float-btn:hover {
        transform: scale(1.08);
        box-shadow: 0 6px 28px rgba(200, 155, 58, 0.5);
    }
    
    .cart-float-btn:active {
        transform: scale(0.95);
    }
    
    .cart-float-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 22px;
        height: 22px;
        background: #dc3545;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 50px;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
    }
    
    /* Overlay */
    .cart-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 1050;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .cart-overlay.show {
        opacity: 1;
        visibility: visible;
    }
    
    /* Slide Panel */
    .cart-panel {
        position: fixed;
        top: 0;
        right: 0;
        width: 100%;
        max-width: 360px;
        height: 100%;
        background: #0d1f2d;
        z-index: 1051;
        transform: translateX(100%);
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }
    
    .cart-panel.open {
        transform: translateX(0);
    }
    
    .cart-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    
    .cart-panel-title {
        color: #fff;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    .cart-panel-title span {
        color: rgba(255, 255, 255, 0.4);
        font-weight: 400;
    }
    
    .cart-panel-close {
        width: 36px;
        height: 36px;
        border: none;
        background: rgba(255, 255, 255, 0.05);
        color: rgba(255, 255, 255, 0.6);
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    
    .cart-panel-close:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }
    
    .cart-panel-body {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
    }
    
    .cart-panel-body::-webkit-scrollbar {
        width: 4px;
    }
    
    .cart-panel-body::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }
    
    .cart-panel-footer {
        padding: 16px 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(0, 0, 0, 0.2);
    }
    
    .cart-footer-total {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        color: #fff;
        font-size: 1rem;
    }
    
    .cart-footer-total span:last-child {
        color: #C89B3A;
        font-weight: 700;
        font-size: 1.15rem;
    }
    
    .cart-footer-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 14px;
        background: #C89B3A;
        color: #fff;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.25s ease;
    }
    
    .cart-footer-btn:hover {
        background: #d4a84a;
        color: #fff;
    }
    
    .cart-footer-btn.disabled {
        opacity: 0.5;
        pointer-events: none;
    }
    
    /* Cart Items */
    .cart-items-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .cart-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
    }
    
    .cart-item-img {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        object-fit: cover;
    }
    
    .cart-item-info {
        flex: 1;
        min-width: 0;
    }
    
    .cart-item-name {
        margin: 0 0 2px 0;
        font-size: 0.9rem;
        font-weight: 500;
        color: #fff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .cart-item-price {
        font-size: 0.8rem;
        color: #C89B3A;
        font-weight: 600;
    }
    
    .cart-item-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .cart-qty-control {
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 8px;
        padding: 2px;
    }
    
    .cart-qty-btn {
        width: 26px;
        height: 26px;
        border: none;
        background: transparent;
        color: rgba(255, 255, 255, 0.6);
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }
    
    .cart-qty-btn:hover:not(:disabled) {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }
    
    .cart-qty-btn:disabled {
        opacity: 0.3;
    }
    
    .cart-qty-value {
        min-width: 24px;
        text-align: center;
        font-size: 0.85rem;
        font-weight: 600;
        color: #fff;
    }
    
    .cart-remove-btn {
        width: 28px;
        height: 28px;
        border: none;
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }
    
    .cart-remove-btn:hover {
        background: rgba(220, 53, 69, 0.2);
    }
    
    /* Loading & Empty States */
    .cart-loading {
        display: flex;
        justify-content: center;
        padding: 60px 0;
    }
    
    .cart-spinner {
        width: 28px;
        height: 28px;
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-top-color: #C89B3A;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }
    
    @keyframes spin { to { transform: rotate(360deg); } }
    
    .cart-empty {
        text-align: center;
        padding: 60px 20px;
    }
    
    .cart-empty-icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 16px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: rgba(255, 255, 255, 0.2);
    }
    
    .cart-empty-text {
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.9rem;
        margin-bottom: 16px;
    }
    
    .cart-start-btn {
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .cart-start-btn:hover {
        background: rgba(255, 255, 255, 0.05);
    }
    
    .cart-error {
        text-align: center;
        padding: 40px;
        color: #dc3545;
    }
    
    /* Light Mode */
    [data-theme="light"] .cart-panel {
        background: #fff;
        box-shadow: -4px 0 20px rgba(0, 0, 0, 0.1);
    }
    
    [data-theme="light"] .cart-panel-title,
    [data-theme="light"] .cart-item-name,
    [data-theme="light"] .cart-qty-value,
    [data-theme="light"] .cart-footer-total {
        color: #0d1f2d;
    }
    
    [data-theme="light"] .cart-item {
        background: rgba(0, 0, 0, 0.02);
    }
    
    [data-theme="light"] .cart-qty-control {
        background: rgba(0, 0, 0, 0.05);
    }
    
    [data-theme="light"] .cart-qty-btn {
        color: rgba(0, 0, 0, 0.5);
    }
    
    [data-theme="light"] .cart-panel-close {
        background: rgba(0, 0, 0, 0.05);
        color: rgba(0, 0, 0, 0.5);
    }
    
    /* Mobile */
    @media (max-width: 400px) {
        .cart-panel {
            max-width: 100%;
        }
    }
    /* 
     * IMPORTANT: Disable ALL CSS :hover and :focus pseudo-classes for menu-card
     * We use JavaScript to manage hover state via .is-hovered class
     */
    .menu-card,
    .menu-card:hover,
    .menu-card:focus,
    .menu-card:active,
    .menu-card:focus-within,
    .menu-card:visited {
        border-color: var(--cream) !important;
        box-shadow: var(--shadow-md) !important;
        outline: none !important;
    }
    /* Only show golden border when .is-hovered class is present (managed by JS) */
    .menu-card.is-hovered {
        border-color: var(--secondary) !important;
        box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3) !important;
        transform: translateY(-4px);
    }
</style>
<script>
    // JavaScript-managed hover state - completely replaces CSS :hover
    document.addEventListener('DOMContentLoaded', function() {
        const menuCards = document.querySelectorAll('.menu-card');
        
        menuCards.forEach(function(card) {
            // Add .is-hovered on mouse enter
            card.addEventListener('mouseenter', function() {
                this.classList.add('is-hovered');
            });
            
            // Remove .is-hovered on mouse leave
            card.addEventListener('mouseleave', function() {
                this.classList.remove('is-hovered');
            });
            
            // Also handle touch devices
            card.addEventListener('touchstart', function() {
                // Remove from all other cards first
                menuCards.forEach(function(c) {
                    c.classList.remove('is-hovered');
                });
                this.classList.add('is-hovered');
            }, { passive: true });
        });
        
        // Remove hover from all cards when clicking outside menu area
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.menu-card')) {
                menuCards.forEach(function(card) {
                    card.classList.remove('is-hovered');
                });
            }
        });
        
        // Blur all buttons inside menu cards after any click to prevent focus persistence
        document.addEventListener('mouseup', function() {
            document.querySelectorAll('.menu-card button, .menu-card .btn').forEach(function(btn) {
                btn.blur();
            });
        });
    });
</script>
@endpush