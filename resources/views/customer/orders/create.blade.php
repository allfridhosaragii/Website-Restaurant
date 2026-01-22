@extends('layouts.guest')
@section('title', __('messages.create_order_title'))
@section('content')
<section class="bg-gradient-primary text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="mb-1" data-i18n="create_order_title">{{ __('messages.create_order_title') }}</h4>
                <p class="mb-0 opacity-75" data-i18n="create_order_desc">{{ __('messages.create_order_desc') }}</p>
            </div>
        </div>
    </div>
</section>
<section class="section bg-cream">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">{{ __('messages.select_order_type') }}</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="order-type-card p-4 rounded-3 border-2 text-center active" data-type="dine_in">
                                    <i class="bi bi-cup-hot-fill fs-1 text-primary mb-2 d-block"></i>
                                    <h5 data-i18n="dine_in">{{ __('messages.dine_in') }}</h5>
                                    <small class="text-muted" data-i18n="dine_in_desc">{{ __('messages.dine_in_desc') }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="order-type-card p-4 rounded-3 border-2 text-center" data-type="take_away">
                                    <i class="bi bi-bag-fill fs-1 text-secondary mb-2 d-block"></i>
                                    <h5 data-i18n="take_away">{{ __('messages.take_away') }}</h5>
                                    <small class="text-muted" data-i18n="take_away_desc">{{ __('messages.take_away_desc') }}</small>
                                </div>
                            </div>
                        </div>
                        <div id="tableSelection" class="mt-4">
                            <h5 class="mb-3" data-i18n="select_table">{{ __('messages.select_table') }}</h5>
                            <div class="row g-2">
                                <div class="col-3 col-md-2">
                                    <div class="table-select p-3 rounded border text-center available">
                                        <strong>1</strong>
                                        <small class="d-block">2-4 {{ __('messages.people') }}</small>
                                    </div>
                                </div>
                                <div class="col-3 col-md-2">
                                    <div class="table-select p-3 rounded border text-center occupied" disabled>
                                        <strong>2</strong>
                                        <small class="d-block text-danger">{{ __('messages.occupied') }}</small>
                                    </div>
                                </div>
                                <div class="col-3 col-md-2">
                                    <div class="table-select p-3 rounded border text-center available active">
                                        <strong>3</strong>
                                        <small class="d-block">4-6 {{ __('messages.people') }}</small>
                                    </div>
                                </div>
                                <div class="col-3 col-md-2">
                                    <div class="table-select p-3 rounded border text-center available">
                                        <strong>4</strong>
                                        <small class="d-block">2-4 {{ __('messages.people') }}</small>
                                    </div>
                                </div>
                                <div class="col-3 col-md-2">
                                    <div class="table-select p-3 rounded border text-center available">
                                        <strong>5</strong>
                                        <small class="d-block">6-8 {{ __('messages.people') }}</small>
                                    </div>
                                </div>
                                <div class="col-3 col-md-2">
                                    <div class="table-select p-3 rounded border text-center available">
                                        <strong>VIP</strong>
                                        <small class="d-block">8+ {{ __('messages.people') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-white">
                        <ul class="nav nav-pills gap-2" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#semua" data-i18n="all_category">{{ __('messages.all_category') }}</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#nasi" data-i18n="rice_noodle_category">{{ __('messages.rice_noodle_category') }}</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#lauk" data-i18n="side_dish_category">{{ __('messages.side_dish_category') }}</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#minum" data-i18n="drink_category">{{ __('messages.drink_category') }}</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @forelse($menus as $menu)
                            <div class="col-md-6 menu-item-container" data-category="{{ strtolower($menu->category) }}">
                                <div class="d-flex align-items-center p-3 border rounded-3 menu-item" data-id="{{ $menu->id }}" data-name="{{ $menu->name }}" data-price="{{ $menu->price }}">
                                    @if($menu->image_url)
                                    <img src="{{ $menu->image_url }}" 
                                         class="rounded-3 me-3" style="width: 80px; height: 80px; object-fit: cover;"
                                         alt="{{ $menu->name }}">
                                    @else
                                    <div class="rounded-3 me-3 bg-secondary d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="bi bi-image text-white"></i>
                                    </div>
                                    @endif
                                    <div class="grow">
                                        <h6 class="mb-1">{{ $menu->name }}</h6>
                                        <small class="text-muted d-block mb-2">{{ Str::limit($menu->description, 40) }}</small>
                                        <strong class="text-primary">Rp {{ number_format($menu->price, 0, ',', '.') }}</strong>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button class="btn btn-sm btn-outline-secondary qty-btn" data-action="minus">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <span class="qty-value">0</span>
                                        <button class="btn btn-sm btn-primary qty-btn" data-action="plus">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12 text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Belum ada menu tersedia</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-cart3 text-primary me-2"></i><span data-i18n="cart_title">{{ __('messages.cart_title') }}</span>
                            <span class="badge bg-primary ms-2" id="cartCount">0</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="cart-items mb-4" id="cartItemsContainer">
                            <div class="text-center py-4 text-muted" id="emptyCartMessage">
                                <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
                                <p class="mb-0">Keranjang masih kosong</p>
                                <small>Pilih menu untuk memulai</small>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" data-i18n="note_optional">{{ __('messages.note_optional') }}</label>
                            <textarea class="form-control" rows="2" placeholder="{{ __('messages.note_placeholder') }}" id="orderNotes"></textarea>
                        </div>
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span data-i18n="subtotal">{{ __('messages.subtotal') }}</span>
                                <span id="subtotalAmount">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span><span data-i18n="tax">{{ __('messages.tax') }}</span> (10%)</span>
                                <span id="taxAmount">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 fw-bold fs-5">
                                <span data-i18n="total">{{ __('messages.total') }}</span>
                                <span class="text-primary" id="totalAmount">Rp 0</span>
                            </div>
                            <button class="btn btn-primary btn-lg w-100" id="payButton" disabled>
                                <i class="bi bi-credit-card me-2"></i><span data-i18n="pay_now">{{ __('messages.pay_now') }}</span>
                            </button>
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-shield-check me-1"></i><span data-i18n="secure_payment">{{ __('messages.secure_payment') }}</span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('styles')
<style>
    .order-type-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .order-type-card:hover,
    .order-type-card.active {
        border-color: var(--primary) !important;
        background: rgba(139, 0, 0, 0.05);
    }
    .order-type-card.active {
        box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
    }
    .table-select {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .table-select.available:hover,
    .table-select.available.active {
        border-color: var(--primary) !important;
        background: rgba(139, 0, 0, 0.1);
    }
    .table-select.occupied {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .menu-item {
        transition: all 0.3s ease;
    }
    .menu-item:hover {
        border-color: var(--primary) !important;
        box-shadow: var(--shadow-sm);
    }
</style>
@endpush
@push('scripts')
<script>
(function() {
    // Cart state
    const cart = {};
    let selectedOrderType = 'dine_in';
    let selectedTable = '3';
    // Format currency
    function formatRupiah(amount) {
        return 'Rp ' + amount.toLocaleString('id-ID');
    }
    // Load cart from server (sync with menu page cart)
    function loadCartFromServer() {
        fetch('/customer/cart', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.items && data.items.length > 0) {
                data.items.forEach(item => {
                    if (item.menu) {
                        const id = item.menu.id;
                        cart[id] = {
                            id: id,
                            name: item.menu.name,
                            price: item.menu.price,
                            qty: item.quantity
                        };
                        // Update qty display on menu item
                        const menuItem = document.querySelector(`.menu-item[data-id="${id}"]`);
                        if (menuItem) {
                            menuItem.querySelector('.qty-value').textContent = item.quantity;
                        }
                    }
                });
                updateCart();
            }
        })
        .catch(err => console.error('Failed to load cart:', err));
    }
    // Update cart display
    function updateCart() {
        const container = document.getElementById('cartItemsContainer');
        const emptyMessage = document.getElementById('emptyCartMessage');
        const cartCount = document.getElementById('cartCount');
        const subtotalEl = document.getElementById('subtotalAmount');
        const taxEl = document.getElementById('taxAmount');
        const totalEl = document.getElementById('totalAmount');
        const payBtn = document.getElementById('payButton');
        const items = Object.values(cart).filter(item => item.qty > 0);
        if (items.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4 text-muted" id="emptyCartMessage">
                    <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
                    <p class="mb-0">Keranjang masih kosong</p>
                    <small>Pilih menu untuk memulai</small>
                </div>
            `;
            cartCount.textContent = '0';
            subtotalEl.textContent = 'Rp 0';
            taxEl.textContent = 'Rp 0';
            totalEl.textContent = 'Rp 0';
            payBtn.disabled = true;
            return;
        }
        let html = '';
        let subtotal = 0;
        let totalItems = 0;
        items.forEach(item => {
            const itemTotal = item.price * item.qty;
            subtotal += itemTotal;
            totalItems += item.qty;
            html += `
                <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                    <div class="flex-grow-1">
                        <h6 class="mb-0">${item.name}</h6>
                        <small class="text-muted">x${item.qty} @ ${formatRupiah(item.price)}</small>
                    </div>
                    <div class="text-end">
                        <strong>${formatRupiah(itemTotal)}</strong>
                        <button class="btn btn-sm btn-link text-danger p-0 ms-2" onclick="removeFromCart(${item.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
        const tax = Math.round(subtotal * 0.1);
        const total = subtotal + tax;
        cartCount.textContent = totalItems;
        subtotalEl.textContent = formatRupiah(subtotal);
        taxEl.textContent = formatRupiah(tax);
        totalEl.textContent = formatRupiah(total);
        payBtn.disabled = false;
    }
    // Remove item from cart
    window.removeFromCart = function(id) {
        if (cart[id]) {
            cart[id].qty = 0;
            // Update the qty display on menu
            const menuItem = document.querySelector(`.menu-item[data-id="${id}"]`);
            if (menuItem) {
                menuItem.querySelector('.qty-value').textContent = '0';
            }
            updateCart();
            // Also remove from server cart
            fetch(`/customer/cart/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).catch(err => console.error(err));
        }
    };
    // Order type selection
    document.querySelectorAll('.order-type-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.order-type-card').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            selectedOrderType = this.dataset.type;
            // Show/hide table selection
            const tableSection = document.getElementById('tableSelection');
            if (selectedOrderType === 'take_away') {
                tableSection.style.display = 'none';
            } else {
                tableSection.style.display = 'block';
            }
        });
    });
    // Table selection
    document.querySelectorAll('.table-select.available').forEach(table => {
        table.addEventListener('click', function() {
            document.querySelectorAll('.table-select').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            selectedTable = this.querySelector('strong').textContent;
        });
    });
    // Quantity buttons
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const menuItem = this.closest('.menu-item');
            const id = menuItem.dataset.id;
            const name = menuItem.dataset.name;
            const price = parseInt(menuItem.dataset.price);
            const qtyEl = menuItem.querySelector('.qty-value');
            let qty = parseInt(qtyEl.textContent);
            if (this.dataset.action === 'plus') {
                qty++;
            } else if (this.dataset.action === 'minus' && qty > 0) {
                qty--;
            }
            qtyEl.textContent = qty;
            // Update cart
            if (qty > 0) {
                cart[id] = { id, name, price, qty };
            } else {
                delete cart[id];
            }
            updateCart();
            // Sync with server cart
            syncCartItem(id, qty);
        });
    });
    // Sync cart item with server
    function syncCartItem(menuId, qty) {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        if (qty > 0) {
            // Check if item exists in server cart, if so update, otherwise add
            fetch('/customer/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ menu_id: menuId, quantity: qty, replace: true })
            }).catch(err => console.error(err));
        } else {
            // Remove from server cart - find cart item id first
            fetch('/customer/cart')
                .then(res => res.json())
                .then(data => {
                    const cartItem = data.items?.find(i => i.menu?.id == menuId);
                    if (cartItem) {
                        fetch(`/customer/cart/${cartItem.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        });
                    }
                });
        }
    }
    // Category filter
    document.querySelectorAll('.nav-pills .nav-link').forEach(tab => {
        tab.addEventListener('click', function() {
            const target = this.dataset.bsTarget;
            const category = target.replace('#', '').toLowerCase();
            document.querySelectorAll('.menu-item-container').forEach(item => {
                if (category === 'semua') {
                    item.style.display = 'block';
                } else {
                    const itemCategory = item.dataset.category.toLowerCase();
                    // Map category names
                    const categoryMap = {
                        'nasi': ['makanan'],
                        'lauk': ['appetizer', 'dessert'],
                        'minum': ['minuman']
                    };
                    if (categoryMap[category] && categoryMap[category].includes(itemCategory)) {
                        item.style.display = 'block';
                    } else if (category === 'semua') {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });
        });
    });
    // Pay button
    document.getElementById('payButton').addEventListener('click', function() {
        const btn = this;
        if (Object.keys(cart).length === 0 || Object.values(cart).every(item => item.qty === 0)) {
            alert('Keranjang masih kosong!');
            return;
        }
        // Prepare order data
        const items = Object.entries(cart)
            .filter(([id, item]) => item.qty > 0)
            .map(([id, item]) => ({
                menu_id: parseInt(id),
                quantity: item.qty
            }));
        const orderData = {
            type: selectedOrderType,
            table_number: selectedOrderType === 'dine_in' ? selectedTable : null,
            items: items,
            notes: document.getElementById('orderNotes').value
        };
        // Disable button and show loading
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        // Submit order via AJAX
        fetch('/customer/orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert('✅ Pesanan berhasil dibuat!\\n\\nNomor Pesanan: ' + data.order_number + '\\nTotal: Rp ' + data.total.toLocaleString('id-ID'));
                // Redirect to orders page
                // Redirect to payment page
                window.location.href = '/customer/payment/' + data.order_id + '/pay';
            } else {
                throw new Error(data.message || 'Gagal membuat pesanan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Gagal membuat pesanan: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-credit-card me-2"></i>Bayar Sekarang';
        });
    });
    // Initialize - load cart from server first
    loadCartFromServer();
})();
</script>
@endpush