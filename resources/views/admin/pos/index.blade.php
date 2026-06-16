@extends('layouts.admin')

@section('title', 'Point of Sale (POS)')

@section('content')
<div class="container-fluid" x-data="posSystem()" x-init="initPos()">
    <div class="row">
        <!-- Left Side: Menus -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Menu</h6>
                    <input type="text" class="form-control w-25" placeholder="Cari menu..." x-model="searchQuery">
                </div>
                <div class="card-body">
                    <div class="row">
                        <template x-for="menu in filteredMenus" :key="menu.id">
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="card h-100 cursor-pointer" @click="addToCart(menu)" style="cursor: pointer;">
                                    <img :src="menu.image_url || 'https://via.placeholder.com/150'" class="card-img-top" alt="..." style="height:120px; object-fit:cover;">
                                    <div class="card-body p-2 text-center">
                                        <h6 class="card-title text-truncate mb-1" x-text="menu.name"></h6>
                                        <p class="card-text text-primary font-weight-bold" x-text="formatCurrency(menu.price)"></p>
                                        <small class="text-muted" x-show="menu.stock !== undefined">Stok: <span x-text="menu.stock"></span></small>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="filteredMenus.length === 0" class="col-12 text-center py-4">
                            <p class="text-muted">Tidak ada menu yang sesuai pencarian.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Cart -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Keranjang Pesanan</h6>
                </div>
                <div class="card-body p-0">
                    <div class="p-3 border-bottom">
                        <div class="form-group mb-2">
                            <label>Tipe Pesanan</label>
                            <select class="form-control" x-model="orderType">
                                <option value="dine_in">Dine In (Makan di tempat)</option>
                                <option value="take_away">Take Away (Bungkus)</option>
                            </select>
                        </div>
                        <div class="form-group mb-0" x-show="orderType === 'dine_in'">
                            <label>Nomor Meja</label>
                            <select class="form-control" x-model="selectedTable">
                                <option value="">Pilih Meja...</option>
                                <template x-for="table in tables" :key="table.id">
                                    <option :value="table.number" x-text="'Meja ' + table.number + ' (Kapasitas: ' + table.capacity + ')'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Cart Items -->
                    <div style="max-height: 350px; overflow-y: auto;" class="p-3">
                        <template x-for="(item, index) in cart" :key="item.menu_id">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div style="width: 50%;">
                                    <h6 class="mb-0 text-truncate" x-text="item.name"></h6>
                                    <small class="text-muted" x-text="formatCurrency(item.price)"></small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <button class="btn btn-sm btn-outline-secondary px-2 py-0" @click="decreaseQty(index)">-</button>
                                    <span class="mx-2" x-text="item.quantity"></span>
                                    <button class="btn btn-sm btn-outline-secondary px-2 py-0" @click="increaseQty(index)">+</button>
                                </div>
                                <div class="text-right" style="width: 25%;">
                                    <strong x-text="formatCurrency(item.price * item.quantity)"></strong>
                                </div>
                            </div>
                        </template>
                        <div x-show="cart.length === 0" class="text-center py-4">
                            <p class="text-muted mb-0">Keranjang masih kosong</p>
                        </div>
                    </div>

                    <!-- Summary & Checkout -->
                    <div class="p-3 bg-light">
                        <div class="form-group mb-2">
                            <label>Catatan</label>
                            <textarea class="form-control" rows="2" x-model="notes" placeholder="Catatan opsional..."></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Metode Pembayaran</label>
                            <select class="form-control" x-model="paymentMethod">
                                <option value="cash">Tunai (Cash)</option>
                                <option value="qris">QRIS</option>
                                <option value="debit">Kartu Debit/Kredit</option>
                            </select>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span x-text="formatCurrency(subtotal)"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span>Pajak (10%)</span>
                            <span x-text="formatCurrency(tax)"></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="mb-0 font-weight-bold">Total</h5>
                            <h5 class="mb-0 font-weight-bold" x-text="formatCurrency(total)"></h5>
                        </div>
                        
                        <button class="btn btn-success btn-block btn-lg" 
                                :disabled="cart.length === 0 || isProcessing"
                                @click="checkout()">
                            <span x-show="!isProcessing"><i class="fas fa-check-circle mr-1"></i> BAYAR SEKARANG</span>
                            <span x-show="isProcessing"><i class="fas fa-spinner fa-spin mr-1"></i> Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<!-- Axios -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('posSystem', () => ({
        menus: [],
        tables: [],
        cart: [],
        searchQuery: '',
        orderType: 'dine_in',
        selectedTable: '',
        paymentMethod: 'cash',
        notes: '',
        isProcessing: false,

        get filteredMenus() {
            if (this.searchQuery === '') {
                return this.menus;
            }
            return this.menus.filter(menu => {
                return menu.name.toLowerCase().includes(this.searchQuery.toLowerCase());
            });
        },

        get subtotal() {
            return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        },

        get tax() {
            return this.subtotal * 0.10;
        },

        get total() {
            return this.subtotal + this.tax;
        },

        initPos() {
            // Fetch Menus
            axios.get('/admin/pos-api/menus')
            .then(res => {
                if(res.data.success) {
                    this.menus = res.data.data;
                }
            })
            .catch(err => console.error("Error fetching menus", err));

            // Fetch Tables
            axios.get('/admin/pos-api/tables')
            .then(res => {
                if(res.data.success) {
                    this.tables = res.data.data;
                }
            })
            .catch(err => console.error("Error fetching tables", err));
        },

        addToCart(menu) {
            if (menu.stock !== undefined && menu.stock <= 0) {
                alert('Stok menu ini habis!');
                return;
            }
            let existingItem = this.cart.find(item => item.menu_id === menu.id);
            if (existingItem) {
                if (menu.stock !== undefined && existingItem.quantity >= menu.stock) {
                    alert('Tidak bisa menambah lebih dari stok yang tersedia!');
                    return;
                }
                existingItem.quantity++;
            } else {
                this.cart.push({
                    menu_id: menu.id,
                    name: menu.name,
                    price: menu.price,
                    quantity: 1,
                    max_stock: menu.stock
                });
            }
        },

        increaseQty(index) {
            let item = this.cart[index];
            if (item.max_stock !== undefined && item.quantity >= item.max_stock) {
                 alert('Maksimal stok tercapai!');
                 return;
            }
            item.quantity++;
        },

        decreaseQty(index) {
            if (this.cart[index].quantity > 1) {
                this.cart[index].quantity--;
            } else {
                this.cart.splice(index, 1);
            }
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
        },

        checkout() {
            if (this.orderType === 'dine_in' && !this.selectedTable) {
                alert('Pilih meja terlebih dahulu untuk Dine In!');
                return;
            }
            
            if(!confirm('Proses pembayaran sebesar ' + this.formatCurrency(this.total) + '?')) {
                return;
            }

            this.isProcessing = true;

            const payload = {
                type: this.orderType,
                table_number: this.selectedTable,
                payment_method: this.paymentMethod,
                notes: this.notes,
                items: this.cart.map(item => ({
                    menu_id: item.menu_id,
                    quantity: item.quantity
                }))
            };

            // Using web routes so we don't need bearer token, Laravel handles CSRF and Session automatically via Axios
            axios.post('/admin/pos-api/checkout', payload)
            .then(res => {
                if(res.data.success) {
                    alert('Pesanan ' + res.data.order_number + ' Berhasil Diproses!');
                    this.cart = [];
                    this.selectedTable = '';
                    this.notes = '';
                    this.paymentMethod = 'cash';
                    this.initPos(); // refresh stock
                }
            })
            .catch(err => {
                alert('Gagal memproses pesanan: ' + (err.response?.data?.message || err.message));
            })
            .finally(() => {
                this.isProcessing = false;
            });
        }
    }));
});
</script>
@endsection
