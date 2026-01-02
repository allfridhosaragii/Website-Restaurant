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
                        <button class="btn btn-light rounded-circle position-absolute bottom-0 end-0 m-2 shadow-sm btn-favorite" 
                                style="width: 35px; height: 35px; padding: 0; display: flex; align-items: center; justify-content: center;"
                                onclick="toggleFavorite(this, {{ $menu->id }})">
                            <i class="bi {{ in_array($menu->id, $favorites ?? []) ? 'bi-heart-fill text-danger' : 'bi-heart' }}" style="font-size: 1.1rem; color: #dc3545;"></i>
                        </button>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-light text-muted mb-2">{{ $menu->category }}</span>
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
                        <button class="btn btn-primary btn-sm w-100 mt-auto" style="isolation: isolate; position: relative; z-index: 2;">
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
</section>
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
            flex: 0 0 50% !important;
            max-width: 50% !important;
            width: 50% !important;
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