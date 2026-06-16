@extends('layouts.admin')

@section('title', 'Sistem Waiter')

@section('content')
<div class="container-fluid" x-data="posSystem()" x-init="initPos()">
    <div class="row">
        <!-- Left Side: Menus -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Menu</h6>
                    <div class="d-flex gap-2 w-50 justify-content-end">
                        <a href="{{ route('admin.pos.table-map') }}" class="btn btn-sm btn-info text-white mr-2">
                            <i class="fas fa-map"></i> Visual Map
                        </a>
                        <input type="text" class="form-control w-50" placeholder="Cari menu..." x-model="searchQuery">
                    </div>
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
            <!-- Widget Reservasi Hari Ini -->
            @if(isset($todayReservations) && $todayReservations->count() > 0)
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3 bg-info text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Reservasi Hari Ini</h6>
                    <span class="badge badge-light text-info">{{ $todayReservations->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 250px; overflow-y: auto;">
                        @foreach($todayReservations as $res)
                        <div class="list-group-item p-3 {{ $res->status == 'checked_in' ? 'bg-light' : '' }}">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 font-weight-bold">{{ $res->time }}</h6>
                                <span class="badge badge-{{ $res->status == 'checked_in' ? 'success' : 'warning' }}">{{ ucfirst($res->status) }}</span>
                            </div>
                            <p class="mb-1 text-truncate">{{ $res->name }} (Meja {{ $res->table ? $res->table->number : '?' }})</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted"><i class="bi bi-people-fill"></i> {{ $res->guests }} org</small>
                                @if($res->status !== 'checked_in')
                                <form action="{{ route('admin.reservations.check-in', $res->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-info py-0">Check-in</button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer bg-white text-center p-2">
                        <a href="{{ route('admin.reservations.calendar') }}" class="btn btn-sm btn-link text-info">Lihat Kalender</a>
                    </div>
                </div>
            </div>
            @endif

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
                    
                    <div class="p-3 border-bottom bg-light">
                        <div class="form-group mb-0">
                            <label>Pelanggan (Opsional)</label>
                            <select class="form-control" x-model="selectedCustomer">
                                <option value="">Pelanggan Umum (Guest)</option>
                                <template x-for="customer in customers" :key="customer.id">
                                    <option :value="customer.id" x-text="customer.name + ' (Deposit: Rp ' + formatCurrency(customer.deposit_balance) + ')'"></option>
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
                                    <template x-if="item.modifiers && item.modifiers.length > 0">
                                        <div class="small text-muted mt-1">
                                            <template x-for="mod in item.modifiers" :key="mod.option_id">
                                                <div><span x-text="'- ' + mod.name + ': ' + mod.option_name"></span> <span x-show="mod.price > 0" x-text="'(+' + formatCurrency(mod.price) + ')'"></span></div>
                                            </template>
                                        </div>
                                    </template>
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
                            <label>Kode Voucher (Opsional)</label>
                            <div class="input-group">
                                <input type="text" class="form-control text-uppercase" x-model="voucherCode" placeholder="Masukkan kode">
                            </div>
                        </div>

                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span x-text="formatCurrency(subtotal)"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span>Pajak (10%) <small>*(di hitung backend)</small></span>
                            <span></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="mb-0 font-weight-bold">Estimasi Total</h5>
                            <h5 class="mb-0 font-weight-bold" x-text="formatCurrency(total)"></h5>
                        </div>
                        
                        <button class="btn btn-success btn-block btn-lg" 
                                :disabled="cart.length === 0 || isProcessing"
                                @click="checkout()">
                            <i class="fas fa-paper-plane mr-1"></i> KIRIM PESANAN
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modifier Modal -->
    <div class="modal" tabindex="-1" :class="{'d-block': showModifierModal, 'show': showModifierModal}" :style="showModifierModal ? 'background: rgba(0,0,0,0.5)' : ''">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kustomisasi Menu</h5>
                    <button type="button" class="close" @click="closeModifierModal()">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-1" x-text="currentModMenu?.name"></h6>
                            <span class="text-primary font-weight-bold" x-text="formatCurrency(currentModMenu?.price || 0)"></span>
                        </div>
                    </div>
                    
                    <template x-if="currentModMenu && currentModMenu.modifiers">
                        <template x-for="(mod, mIndex) in currentModMenu.modifiers" :key="mod.id">
                            <div class="mb-3 p-3 bg-light rounded">
                                <h6 class="mb-2">
                                    <span x-text="mod.name"></span>
                                    <span x-show="mod.is_required" class="text-danger">*</span>
                                </h6>
                                <template x-for="(opt, oIndex) in mod.options" :key="opt.id">
                                    <div class="custom-control mb-2" :class="mod.type === 'multiple' ? 'custom-checkbox' : 'custom-radio'">
                                        <input :type="mod.type === 'multiple' ? 'checkbox' : 'radio'" 
                                               :name="'mod_' + mIndex"
                                               :id="'mod_' + mIndex + '_' + oIndex"
                                               :value="opt.id"
                                               class="custom-control-input mod-input"
                                               :data-mod-index="mIndex"
                                               :data-opt-index="oIndex"
                                               @change="calculateModTotal()">
                                        <label class="custom-control-label d-flex justify-content-between w-100" :for="'mod_' + mIndex + '_' + oIndex">
                                            <span x-text="opt.name"></span>
                                            <span x-show="opt.price > 0" class="badge badge-secondary" x-text="'+' + formatCurrency(opt.price)"></span>
                                        </label>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </template>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <small class="text-muted d-block">Total Tambahan:</small>
                        <strong class="text-primary" x-text="formatCurrency(currentModTotal)"></strong>
                    </div>
                    <button type="button" class="btn btn-primary" @click="addModifierToCart()">
                        <i class="fas fa-cart-plus mr-1"></i> Tambah
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal" tabindex="-1" :class="{'d-block': showPaymentModal, 'show': showPaymentModal}" :style="showPaymentModal ? 'background: rgba(0,0,0,0.5)' : ''">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Pembayaran (Split Payment)</h5>
                    <button type="button" class="close text-white" @click="showPaymentModal = false">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info d-flex justify-content-between align-items-center">
                        <span class="font-weight-bold">Total Tagihan (Estimasi):</span>
                        <h4 class="mb-0 font-weight-bold" x-text="formatCurrency(total)"></h4>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Metode</th>
                                    <th>Nominal</th>
                                    <th>No. Referensi <small>(Opsional)</small></th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(payment, index) in payments" :key="index">
                                    <tr>
                                        <td>
                                            <select class="form-control" x-model="payment.method">
                                                <option value="cash">Tunai (Cash)</option>
                                                <option value="qris">QRIS</option>
                                                <option value="debit_card">Kartu Debit</option>
                                                <option value="credit_card">Kartu Kredit</option>
                                                <option value="e_wallet">E-Wallet</option>
                                                <option value="deposit" x-show="selectedCustomer">Deposit Pelanggan</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" x-model.number="payment.amount" min="0" step="1000">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" x-model="payment.reference_number" placeholder="Ref. No">
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-danger" @click="removePayment(index)" x-show="payments.length > 1">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button class="btn btn-outline-primary btn-sm" @click="addPayment()" x-show="payments.length < 3">
                            <i class="fas fa-plus"></i> Tambah Metode Bayar
                        </button>
                        <span class="text-muted small" x-show="payments.length >= 3">Maksimal 3 metode pembayaran</span>
                    </div>

                    <div class="card bg-light">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Total Dibayar:</span>
                                <strong x-text="formatCurrency(totalPaid)" :class="totalPaid >= total ? 'text-success' : 'text-danger'"></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span x-text="totalPaid > total ? 'Kembalian:' : 'Kekurangan:'"></span>
                                <strong x-text="formatCurrency(Math.abs(totalPaid - total))" :class="totalPaid >= total ? 'text-primary' : 'text-danger'"></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showPaymentModal = false">Batal</button>
                    <button type="button" class="btn btn-success" 
                            :disabled="totalPaid < total || isProcessing"
                            @click="checkout()">
                        <span x-show="!isProcessing"><i class="fas fa-check-circle mr-1"></i> Proses Bayar</span>
                        <span x-show="isProcessing"><i class="fas fa-spinner fa-spin mr-1"></i> Memproses...</span>
                    </button>
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
        customers: [],
        cart: [],
        searchQuery: '',
        orderType: 'dine_in',
        selectedTable: '',
        selectedCustomer: '',
        notes: '',
        voucherCode: '',
        isProcessing: false,
        showModifierModal: false,
        showPaymentModal: false,
        payments: [{ method: 'cash', amount: 0, reference_number: '' }],
        currentModMenu: null,
        currentModTotal: 0,

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

        get totalPaid() {
            return this.payments.reduce((sum, p) => sum + Number(p.amount || 0), 0);
        },

        initPos() {
            this.fetchMenus();
            this.fetchTables();
            this.fetchCustomers();
        },

        fetchCustomers() {
            axios.get('/api/waiter/customers')
                .then(res => {
                    if(res.data.success) {
                        this.customers = res.data.data;
                    }
                })
                .catch(err => console.error(err));
        },

        fetchMenus() {
            axios.get('/api/waiter/menus')
            .then(res => {
                if(res.data.success) {
                    this.menus = res.data.data;
                }
            })
            .catch(err => console.error("Error fetching menus", err));
        },

        fetchTables() {
            axios.get('/api/waiter/tables')
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

            if (menu.modifiers && menu.modifiers.length > 0) {
                this.currentModMenu = menu;
                this.currentModTotal = 0;
                this.showModifierModal = true;
                setTimeout(() => {
                    document.querySelectorAll('.mod-input').forEach(el => el.checked = false);
                }, 100);
                return;
            }

            const signature = menu.id + '-[]';
            let existingItem = this.cart.find(item => item.signature === signature);
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
                    max_stock: menu.stock,
                    modifiers: [],
                    signature: signature
                });
            }
        },

        closeModifierModal() {
            this.showModifierModal = false;
            this.currentModMenu = null;
        },

        calculateModTotal() {
            let total = 0;
            const inputs = document.querySelectorAll('.mod-input:checked');
            inputs.forEach(input => {
                const mIndex = input.getAttribute('data-mod-index');
                const oIndex = input.getAttribute('data-opt-index');
                const price = this.currentModMenu.modifiers[mIndex].options[oIndex].price;
                total += parseFloat(price);
            });
            this.currentModTotal = total;
        },

        addModifierToCart() {
            let isValid = true;
            let selectedMods = [];
            
            this.currentModMenu.modifiers.forEach((mod, mIndex) => {
                const inputs = document.querySelectorAll(`input[name="mod_${mIndex}"]:checked`);
                if (mod.is_required && inputs.length === 0) {
                    isValid = false;
                    alert(`Silakan pilih ${mod.name}`);
                }
                inputs.forEach(input => {
                    const oIndex = input.getAttribute('data-opt-index');
                    const opt = mod.options[oIndex];
                    selectedMods.push({
                        id: mod.id,
                        name: mod.name,
                        option_id: opt.id,
                        option_name: opt.name,
                        price: parseFloat(opt.price)
                    });
                });
            });

            if (!isValid) return;

            selectedMods.sort((a,b) => a.option_id - b.option_id);
            const signature = this.currentModMenu.id + '-' + JSON.stringify(selectedMods);
            const finalPrice = this.currentModMenu.price + this.currentModTotal;

            let existingItem = this.cart.find(item => item.signature === signature);
            if (existingItem) {
                if (this.currentModMenu.stock !== undefined && existingItem.quantity >= this.currentModMenu.stock) {
                    alert('Tidak bisa menambah lebih dari stok yang tersedia!');
                    return;
                }
                existingItem.quantity++;
            } else {
                this.cart.push({
                    menu_id: this.currentModMenu.id,
                    name: this.currentModMenu.name,
                    price: finalPrice,
                    quantity: 1,
                    max_stock: this.currentModMenu.stock,
                    modifiers: selectedMods,
                    signature: signature
                });
            }

            this.closeModifierModal();
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

            this.isProcessing = true;

            let tableId = null;
            if (this.orderType === 'dine_in') {
                const tableObj = this.tables.find(t => t.number == this.selectedTable);
                if (tableObj) tableId = tableObj.id;
            }

            const payload = {
                type: this.orderType,
                table_number: this.orderType === 'dine_in' ? this.selectedTable : null,
                table_id: tableId,
                customer_id: this.selectedCustomer || null,
                items: this.cart.map(item => ({
                    menu_id: item.menu_id,
                    quantity: item.quantity,
                    modifiers: item.modifiers || []
                })),
                notes: this.notes,
                voucher_code: this.voucherCode || null
            };

            axios.post('/api/waiter/checkout', payload)
            .then(res => {
                if(res.data.success) {
                    alert('Pesanan ' + res.data.order_number + ' Berhasil Diteruskan!');
                    this.cart = [];
                    this.selectedTable = '';
                    this.notes = '';
                    this.voucherCode = '';
                    this.initPos(); // refresh
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
