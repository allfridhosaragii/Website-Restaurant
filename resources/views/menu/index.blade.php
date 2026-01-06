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

<!-- Luxury Floating Cart Button -->
<div id="floatingCartContainer" class="lux-cart-float">
    <button type="button" class="lux-cart-btn" onclick="toggleCartPanel()">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4z"/>
        </svg>
        <span class="lux-cart-count" id="cartCountBadge">0</span>
    </button>
</div>

<!-- Luxury Cart Panel -->
<div class="lux-overlay" id="cartOverlay" onclick="closeCartPanel()"></div>
<div class="lux-cart-panel" id="cartPanel">
    <!-- Premium Header -->
    <div class="lux-cart-head">
        <div class="lux-cart-head-inner">
            <div class="lux-cart-brand">
                <span class="lux-cart-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4z"/>
                    </svg>
                </span>
                <div class="lux-cart-titles">
                    <span class="lux-cart-label">Shopping Bag</span>
                    <span class="lux-cart-count-text" id="cartHeaderCount"></span>
                </div>
            </div>
            <button class="lux-close-btn" onclick="closeCartPanel()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Cart Content -->
    <div class="lux-cart-content" id="cartModalBody"></div>
    
    <!-- Premium Footer -->
    <div class="lux-cart-foot">
        <div class="lux-shipping-notice">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
            </svg>
            <span>Gratis ongkir untuk pesanan di atas Rp 200.000</span>
        </div>
        <div class="lux-total-row">
            <span class="lux-total-label">Subtotal</span>
            <span class="lux-total-price" id="cartModalTotal">Rp 0</span>
        </div>
        <a href="{{ url('/customer/orders/create') }}" class="lux-checkout-btn" id="btnCheckout">
            <span>Lanjut ke Pembayaran</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8"/>
            </svg>
        </a>
        <button class="lux-continue-btn" onclick="closeCartPanel()">
            Lanjut Belanja
        </button>
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
            const count = data.items ? data.items.length : 0;
            headerCount.textContent = count > 0 ? `${count} item${count > 1 ? 's' : ''}` : '';
        }
        
        if (!data.items || data.items.length === 0) {
            modalBody.innerHTML = `
                <div class="lux-empty">
                    <div class="lux-empty-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4z"/>
                        </svg>
                    </div>
                    <h4 class="lux-empty-title">Keranjang Kosong</h4>
                    <p class="lux-empty-text">Sepertinya kamu belum menambahkan menu apapun</p>
                    <button class="lux-empty-btn" onclick="closeCartPanel()">
                        Jelajahi Menu
                    </button>
                </div>
            `;
            modalTotal.innerText = 'Rp 0';
            btnCheckout.classList.add('disabled');
            return;
        }

        btnCheckout.classList.remove('disabled');
        modalTotal.innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(data.total);

        let html = '<div class="lux-items">';
        data.items.forEach((item, index) => {
            if (!item.menu) return;

            const subtotal = item.menu.price * item.quantity;
            const unitPrice = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.menu.price);
            const totalPrice = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(subtotal);
            
            html += `
                <div class="lux-item" style="animation-delay: ${index * 0.05}s">
                    <div class="lux-item-img-wrap">
                        <img src="${item.menu.image_url}" alt="${item.menu.name}" class="lux-item-img">
                    </div>
                    <div class="lux-item-details">
                        <div class="lux-item-top">
                            <h5 class="lux-item-name">${item.menu.name}</h5>
                            <button class="lux-item-remove" onclick="removeCartItem(${item.id})">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </button>
                        </div>
                        <span class="lux-item-unit">${unitPrice}</span>
                        <div class="lux-item-bottom">
                            <div class="lux-qty">
                                <button class="lux-qty-btn" onclick="updateCartItem(${item.id}, ${item.quantity}, -1)" ${item.quantity <= 1 ? 'disabled' : ''}>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8"/>
                                    </svg>
                                </button>
                                <span class="lux-qty-num">${item.quantity}</span>
                                <button class="lux-qty-btn" onclick="updateCartItem(${item.id}, ${item.quantity}, 1)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                                    </svg>
                                </button>
                            </div>
                            <span class="lux-item-total">${totalPrice}</span>
                        </div>
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
        
        // Optimistic UI Update - update immediately
        const item = document.querySelector(`.lux-item[data-id="${id}"]`) || 
                     document.querySelector(`.lux-item:has([onclick*="updateCartItem(${id},"])`);
        
        let unitPrice = 0;
        if (item) {
            const qtyEl = item.querySelector('.lux-qty-num');
            const priceEl = item.querySelector('.lux-item-total');
            const unitEl = item.querySelector('.lux-item-unit');
            if (qtyEl) qtyEl.textContent = newQty;
            
            // Update subtotal based on unit price
            if (priceEl && unitEl) {
                unitPrice = parseInt(unitEl.textContent.replace(/[^\d]/g, ''));
                const newTotal = unitPrice * newQty;
                priceEl.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(newTotal);
            }
            
            // Update button state
            const minusBtn = item.querySelector('.lux-qty-btn:first-child');
            if (minusBtn) minusBtn.disabled = (newQty <= 1);
            
            // Update onclick to reflect new quantity
            const minusBtnAll = item.querySelectorAll('.lux-qty-btn');
            if (minusBtnAll[0]) minusBtnAll[0].setAttribute('onclick', `updateCartItem(${id}, ${newQty}, -1)`);
            if (minusBtnAll[1]) minusBtnAll[1].setAttribute('onclick', `updateCartItem(${id}, ${newQty}, 1)`);
        }
        
        // Update grand total immediately
        const totalEl = document.getElementById('cartModalTotal');
        if (totalEl && unitPrice) {
            const currentTotal = parseInt(totalEl.textContent.replace(/[^\d]/g, '')) || 0;
            const priceDiff = unitPrice * change;
            const newGrandTotal = currentTotal + priceDiff;
            totalEl.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(newGrandTotal);
        }
        
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
            // Only update badge count, no re-render needed
            if(data.success) {
                updateCartCount();
            }
        })
        .catch(err => {
            console.error(err);
            refreshCartContent(); // Revert on error only
        });
    }

    function removeCartItem(id) {
        // Find the item element first
        const item = document.querySelector(`.lux-item[data-id="${id}"]`) || 
                     document.querySelector(`.lux-item:has([onclick*="removeCartItem(${id})"])`);
        
        // Optimistic UI - remove immediately with animation
        if (item) {
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '0';
            item.style.transform = 'translateX(30px)';
            setTimeout(() => item.remove(), 300);
        }

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
                refreshCartContent();
                updateCartCount();
            }
        })
        .catch(err => {
            console.error(err);
            refreshCartContent(); // Revert on error
        });
    }

    // Update total price optimistically
    function updateTotalOptimistic(change) {
        const totalEl = document.getElementById('cartModalTotal');
        if (totalEl) {
            // This is a rough estimate, will be corrected by refreshCartContent
            const currentTotal = parseInt(totalEl.textContent.replace(/[^\d]/g, '')) || 0;
            // We don't know exact item price here, so just leave it for server sync
        }
    }

    // Refresh cart content without closing/reopening panel
    function refreshCartContent() {
        fetch('/customer/cart')
            .then(res => res.json())
            .then(data => {
                renderCartModal(data);
                const badge = document.getElementById('cartCountBadge');
                if(badge) badge.textContent = data.count || 0;
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

    /* ========== LUXURY CART DESIGN ========== */
    
    /* Premium Floating Button */
    .lux-cart-float {
        position: fixed;
        bottom: 28px;
        right: 28px;
        z-index: 999;
        opacity: 0;
        transform: translateY(24px) scale(0.9);
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        pointer-events: none;
    }
    
    .lux-cart-float.show {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }
    
    .lux-cart-btn {
        width: 60px;
        height: 60px;
        border: none;
        border-radius: 50%;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        color: #C89B3A;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        box-shadow: 
            0 8px 32px rgba(0, 0, 0, 0.3),
            0 0 0 1px rgba(200, 155, 58, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }
    
    .lux-cart-btn::before {
        content: '';
        position: absolute;
        inset: -3px;
        border-radius: 50%;
        background: linear-gradient(135deg, #C89B3A 0%, #a67c28 100%);
        z-index: -1;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .lux-cart-btn:hover {
        transform: translateY(-3px);
        box-shadow: 
            0 12px 40px rgba(0, 0, 0, 0.35),
            0 0 0 1px rgba(200, 155, 58, 0.4);
    }
    
    .lux-cart-btn:hover::before {
        opacity: 0.15;
    }
    
    .lux-cart-btn:active {
        transform: scale(0.95);
    }
    
    .lux-cart-count {
        position: absolute;
        top: -6px;
        right: -6px;
        min-width: 24px;
        height: 24px;
        background: linear-gradient(135deg, #C89B3A 0%, #a67c28 100%);
        color: #fff;
        font-size: 0.7rem;
        font-weight: 700;
        border-radius: 50px;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 0 7px;
        box-shadow: 0 2px 8px rgba(200, 155, 58, 0.4);
        letter-spacing: -0.02em;
    }

    /* Overlay */
    .lux-overlay {
        position: fixed;
        inset: 0;
        background: rgba(10, 15, 25, 0.7);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 9998;
        opacity: 0;
        visibility: hidden;
        transition: all 0.4s ease;
    }
    
    .lux-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    /* Cart Panel */
    .lux-cart-panel {
        position: fixed;
        top: 0;
        right: 0;
        width: 100%;
        max-width: 420px;
        height: 100%;
        background: linear-gradient(180deg, #0f1923 0%, #0a1018 100%);
        z-index: 9999;
        transform: translateX(100%);
        transition: transform 0.45s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        box-shadow: -20px 0 60px rgba(0, 0, 0, 0.4);
    }
    
    .lux-cart-panel.open {
        transform: translateX(0);
    }

    /* Header */
    .lux-cart-head {
        padding: 0 24px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    
    .lux-cart-head-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 24px 0;
    }
    
    .lux-cart-brand {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    
    .lux-cart-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, rgba(200, 155, 58, 0.15) 0%, rgba(200, 155, 58, 0.05) 100%);
        border: 1px solid rgba(200, 155, 58, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #C89B3A;
    }
    
    .lux-cart-titles {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    
    .lux-cart-label {
        font-size: 1.15rem;
        font-weight: 600;
        color: #fff;
        letter-spacing: -0.02em;
    }
    
    .lux-cart-count-text {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.4);
        font-weight: 400;
    }
    
    .lux-close-btn {
        width: 40px;
        height: 40px;
        border: none;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.5);
        transition: all 0.2s ease;
    }
    
    .lux-close-btn:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
    }

    /* Content */
    .lux-cart-content {
        flex: 1;
        overflow-y: auto;
        padding: 20px 24px;
    }
    
    .lux-cart-content::-webkit-scrollbar {
        width: 5px;
    }
    
    .lux-cart-content::-webkit-scrollbar-thumb {
        background: rgba(200, 155, 58, 0.3);
        border-radius: 5px;
    }

    /* Cart Items */
    .lux-items {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    
    .lux-item {
        display: flex;
        gap: 16px;
        padding: 16px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-radius: 16px;
        animation: luxItemSlide 0.4s ease forwards;
        opacity: 0;
        transform: translateX(20px);
    }
    
    @keyframes luxItemSlide {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .lux-item-img-wrap {
        flex-shrink: 0;
        width: 80px;
        height: 80px;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }
    
    .lux-item-img-wrap::after {
        content: '';
        position: absolute;
        inset: 0;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        pointer-events: none;
    }
    
    .lux-item-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .lux-item:hover .lux-item-img {
        transform: scale(1.05);
    }
    
    .lux-item-details {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
    }
    
    .lux-item-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 8px;
    }
    
    .lux-item-name {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 500;
        color: #fff;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .lux-item-remove {
        flex-shrink: 0;
        width: 24px;
        height: 24px;
        border: none;
        background: transparent;
        color: rgba(255, 255, 255, 0.3);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    
    .lux-item-remove:hover {
        background: rgba(220, 53, 69, 0.15);
        color: #dc3545;
    }
    
    .lux-item-unit {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.4);
        margin-top: 4px;
    }
    
    .lux-item-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 12px;
    }
    
    .lux-qty {
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 10px;
        overflow: hidden;
    }
    
    .lux-qty-btn {
        width: 32px;
        height: 32px;
        border: none;
        background: transparent;
        color: rgba(255, 255, 255, 0.6);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }
    
    .lux-qty-btn:hover:not(:disabled) {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
    }
    
    .lux-qty-btn:disabled {
        opacity: 0.25;
        cursor: not-allowed;
    }
    
    .lux-qty-num {
        min-width: 32px;
        text-align: center;
        font-size: 0.9rem;
        font-weight: 600;
        color: #fff;
    }
    
    .lux-item-total {
        font-size: 0.95rem;
        font-weight: 600;
        color: #C89B3A;
    }

    /* Footer */
    .lux-cart-foot {
        padding: 20px 24px 28px;
        background: linear-gradient(180deg, rgba(15, 25, 35, 0.95) 0%, rgba(10, 16, 24, 1) 100%);
        border-top: 1px solid rgba(255, 255, 255, 0.04);
    }
    
    .lux-shipping-notice {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        background: rgba(200, 155, 58, 0.08);
        border: 1px solid rgba(200, 155, 58, 0.15);
        border-radius: 10px;
        margin-bottom: 16px;
        color: #C89B3A;
        font-size: 0.8rem;
    }
    
    .lux-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    
    .lux-total-label {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.5);
    }
    
    .lux-total-price {
        font-size: 1.4rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: -0.02em;
    }
    
    .lux-checkout-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #C89B3A 0%, #a67c28 100%);
        border: none;
        border-radius: 14px;
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(200, 155, 58, 0.25);
        transition: all 0.3s ease;
    }
    
    .lux-checkout-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(200, 155, 58, 0.35);
        color: #fff;
    }
    
    .lux-checkout-btn.disabled {
        opacity: 0.4;
        pointer-events: none;
    }
    
    .lux-continue-btn {
        width: 100%;
        padding: 14px;
        margin-top: 10px;
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .lux-continue-btn:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.2);
        color: #fff;
    }

    /* Empty State */
    .lux-empty {
        text-align: center;
        padding: 60px 20px;
    }
    
    .lux-empty-icon {
        width: 88px;
        height: 88px;
        margin: 0 auto 24px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.15);
    }
    
    .lux-empty-title {
        font-size: 1.15rem;
        font-weight: 600;
        color: #fff;
        margin: 0 0 8px;
    }
    
    .lux-empty-text {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.4);
        margin: 0 0 24px;
    }
    
    .lux-empty-btn {
        padding: 12px 28px;
        background: linear-gradient(135deg, #C89B3A 0%, #a67c28 100%);
        border: none;
        border-radius: 12px;
        color: #fff;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .lux-empty-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(200, 155, 58, 0.3);
    }
    
    /* Loading */
    .cart-loading {
        display: flex;
        justify-content: center;
        padding: 80px 0;
    }
    
    .cart-spinner {
        width: 32px;
        height: 32px;
        border: 2px solid rgba(255, 255, 255, 0.08);
        border-top-color: #C89B3A;
        border-radius: 50%;
        animation: luxSpin 0.8s linear infinite;
    }
    
    @keyframes luxSpin { to { transform: rotate(360deg); } }

    /* Light Mode */
    [data-theme="light"] .lux-cart-panel {
        background: linear-gradient(180deg, #fefefe 0%, #f8f9fa 100%);
        box-shadow: -20px 0 60px rgba(0, 0, 0, 0.1);
    }
    
    [data-theme="light"] .lux-cart-head {
        border-color: rgba(0, 0, 0, 0.06);
    }
    
    [data-theme="light"] .lux-cart-label,
    [data-theme="light"] .lux-item-name,
    [data-theme="light"] .lux-qty-num,
    [data-theme="light"] .lux-total-price,
    [data-theme="light"] .lux-empty-title {
        color: #0f1923;
    }
    
    [data-theme="light"] .lux-cart-icon {
        background: rgba(200, 155, 58, 0.1);
    }
    
    [data-theme="light"] .lux-close-btn {
        background: rgba(0, 0, 0, 0.04);
        color: rgba(0, 0, 0, 0.5);
    }
    
    [data-theme="light"] .lux-item {
        background: rgba(0, 0, 0, 0.02);
        border-color: rgba(0, 0, 0, 0.04);
    }
    
    [data-theme="light"] .lux-qty {
        background: rgba(0, 0, 0, 0.03);
        border-color: rgba(0, 0, 0, 0.06);
    }
    
    [data-theme="light"] .lux-qty-btn {
        color: rgba(0, 0, 0, 0.5);
    }
    
    [data-theme="light"] .lux-cart-foot {
        background: #fff;
        border-color: rgba(0, 0, 0, 0.06);
    }
    
    [data-theme="light"] .lux-continue-btn {
        border-color: rgba(0, 0, 0, 0.1);
        color: rgba(0, 0, 0, 0.6);
    }

    /* Mobile - Compact View */
    @media (max-width: 480px) {
        .lux-cart-panel {
            max-width: 100%;
        }
        
        .lux-cart-float {
            bottom: 16px;
            right: 16px;
        }
        
        .lux-cart-btn {
            width: 50px;
            height: 50px;
        }
        
        .lux-cart-btn svg {
            width: 18px;
            height: 18px;
        }
        
        .lux-cart-count {
            min-width: 20px;
            height: 20px;
            font-size: 0.65rem;
        }
        
        /* Compact Header */
        .lux-cart-head {
            padding: 0 16px;
        }
        
        .lux-cart-head-inner {
            padding: 16px 0;
        }
        
        .lux-cart-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
        }
        
        .lux-cart-icon svg {
            width: 16px;
            height: 16px;
        }
        
        .lux-cart-brand {
            gap: 10px;
        }
        
        .lux-cart-label {
            font-size: 1rem;
        }
        
        .lux-cart-count-text {
            font-size: 0.7rem;
        }
        
        .lux-close-btn {
            width: 34px;
            height: 34px;
        }
        
        /* Compact Content */
        .lux-cart-content {
            padding: 12px 16px;
        }
        
        .lux-items {
            gap: 10px;
        }
        
        .lux-item {
            padding: 10px;
            gap: 10px;
            border-radius: 12px;
        }
        
        .lux-item-img-wrap {
            width: 60px;
            height: 60px;
            border-radius: 10px;
        }
        
        .lux-item-name {
            font-size: 0.85rem;
        }
        
        .lux-item-unit {
            font-size: 0.7rem;
        }
        
        .lux-item-bottom {
            padding-top: 8px;
        }
        
        .lux-qty {
            border-radius: 8px;
        }
        
        .lux-qty-btn {
            width: 28px;
            height: 28px;
        }
        
        .lux-qty-num {
            min-width: 24px;
            font-size: 0.8rem;
        }
        
        .lux-item-total {
            font-size: 0.85rem;
        }
        
        .lux-item-remove {
            width: 22px;
            height: 22px;
        }
        
        /* Compact Footer */
        .lux-cart-foot {
            padding: 14px 16px 20px;
        }
        
        .lux-shipping-notice {
            padding: 10px 12px;
            font-size: 0.7rem;
            margin-bottom: 12px;
            border-radius: 8px;
            gap: 8px;
        }
        
        .lux-shipping-notice svg {
            width: 14px;
            height: 14px;
        }
        
        .lux-total-row {
            margin-bottom: 12px;
        }
        
        .lux-total-label {
            font-size: 0.8rem;
        }
        
        .lux-total-price {
            font-size: 1.15rem;
        }
        
        .lux-checkout-btn {
            padding: 12px;
            font-size: 0.9rem;
            border-radius: 10px;
        }
        
        .lux-checkout-btn svg {
            width: 16px;
            height: 16px;
        }
        
        .lux-continue-btn {
            padding: 10px;
            font-size: 0.8rem;
            margin-top: 8px;
            border-radius: 10px;
        }
        
        /* Compact Empty State */
        .lux-empty {
            padding: 40px 16px;
        }
        
        .lux-empty-icon {
            width: 64px;
            height: 64px;
            margin-bottom: 16px;
        }
        
        .lux-empty-icon svg {
            width: 28px;
            height: 28px;
        }
        
        .lux-empty-title {
            font-size: 1rem;
        }
        
        .lux-empty-text {
            font-size: 0.8rem;
            margin-bottom: 16px;
        }
        
        .lux-empty-btn {
            padding: 10px 20px;
            font-size: 0.8rem;
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