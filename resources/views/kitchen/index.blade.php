<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Display System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1d20;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .kds-card {
            background-color: #2c3034;
            border: 1px solid #495057;
            border-radius: 8px;
            height: 100%;
        }
        .kds-header {
            padding: 10px 15px;
            border-bottom: 2px solid #495057;
            font-size: 1.25rem;
            font-weight: bold;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .kds-body {
            padding: 15px;
        }
        .kds-item {
            padding: 10px 0;
            border-bottom: 1px solid #495057;
            font-size: 1.1rem;
        }
        .kds-item:last-child {
            border-bottom: none;
        }
        .timer {
            font-family: monospace;
            font-size: 1.1rem;
        }
        .timer.danger {
            color: #dc3545;
            animation: blink 1s infinite;
        }
        @keyframes blink {
            50% { opacity: 0; }
        }
        .status-new { border-top: 4px solid #dc3545; }
        .status-cooking { border-top: 4px solid #ffc107; }
        .status-ready { border-top: 4px solid #198754; }
        
        /* High Contrast Buttons */
        .btn-kds {
            font-size: 1.1rem;
            font-weight: bold;
            padding: 8px 16px;
        }
    </style>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="kitchenSystem()">
    
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark sticky-top shadow-sm border-bottom border-secondary">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1 fs-3"><i class="bi bi-display"></i> Kitchen Display System</span>
            
            <div class="d-flex gap-2 align-items-center">
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="filter" id="filter-all" value="all" x-model="filterStatus">
                    <label class="btn btn-outline-light" for="filter-all">Semua</label>

                    <input type="radio" class="btn-check" name="filter" id="filter-new" value="new" x-model="filterStatus">
                    <label class="btn btn-outline-danger" for="filter-new">New</label>

                    <input type="radio" class="btn-check" name="filter" id="filter-cooking" value="cooking" x-model="filterStatus">
                    <label class="btn btn-outline-warning" for="filter-cooking">Cooking</label>
                    
                    <input type="radio" class="btn-check" name="filter" id="filter-ready" value="ready" x-model="filterStatus">
                    <label class="btn btn-outline-success" for="filter-ready">Ready</label>
                </div>
                <div class="text-white ms-3">
                    <i class="bi bi-clock"></i> <span x-text="currentTime"></span>
                </div>
                <a href="/admin/dashboard" class="btn btn-secondary ms-3"><i class="bi bi-box-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid p-4">
        <!-- Loading State -->
        <div x-show="loading" class="text-center py-5">
            <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h4 class="mt-3">Memuat Data Pesanan...</h4>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4" x-show="!loading" x-cloak>
            <template x-for="order in filteredOrders" :key="order.order_id">
                <div class="col">
                    <div class="kds-card shadow-lg d-flex flex-column" 
                         :class="{
                            'status-new': order.items.some(i => i.kitchen_status == 'new'),
                            'status-cooking': order.items.every(i => i.kitchen_status != 'new') && order.items.some(i => i.kitchen_status == 'cooking'),
                            'status-ready': order.items.every(i => i.kitchen_status == 'ready')
                         }">
                        <div class="kds-header d-flex justify-content-between align-items-center bg-dark text-white">
                            <div>
                                <span x-text="order.table_name"></span>
                                <div class="fs-6 text-muted mt-1">#<span x-text="order.order_number"></span></div>
                            </div>
                            <div class="text-end">
                                <div class="timer" :class="{'danger': order.elapsed_minutes > 15}">
                                    <i class="bi bi-stopwatch"></i> <span x-text="order.elapsed_minutes + ' mnt'"></span>
                                </div>
                                <div class="fs-6 text-muted mt-1" x-text="order.time_ordered"></div>
                            </div>
                        </div>
                        <div class="kds-body flex-grow-1">
                            <template x-for="item in order.items" :key="item.id">
                                <div class="kds-item">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="fw-bold fs-5">
                                            <span x-text="item.quantity + 'x'"></span>
                                            <span x-text="item.menu_name" class="ms-2"></span>
                                        </div>
                                    </div>
                                    
                                    <!-- Modifiers -->
                                    <template x-if="item.modifiers && item.modifiers.length > 0">
                                        <div class="text-warning small ms-4 mb-1">
                                            <template x-for="mod in item.modifiers">
                                                <div><i class="bi bi-dash"></i> <span x-text="mod.option_name"></span></div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Notes -->
                                    <template x-if="item.notes">
                                        <div class="text-info small ms-4 mb-2">
                                            <i class="bi bi-chat-text"></i> <span x-text="item.notes"></span>
                                        </div>
                                    </template>

                                    <!-- Actions per item -->
                                    <div class="d-flex justify-content-end gap-2 mt-2">
                                        <button x-show="item.kitchen_status === 'new'" 
                                                @click="updateStatus(item.id, 'cooking')" 
                                                class="btn btn-kds btn-warning w-100">
                                            <i class="bi bi-fire"></i> Mulai Masak
                                        </button>
                                        <button x-show="item.kitchen_status === 'cooking'" 
                                                @click="updateStatus(item.id, 'ready')" 
                                                class="btn btn-kds btn-success w-100">
                                            <i class="bi bi-check-circle"></i> Siap
                                        </button>
                                        <button x-show="item.kitchen_status === 'ready'" 
                                                @click="updateStatus(item.id, 'served')" 
                                                class="btn btn-kds btn-outline-light w-100">
                                            <i class="bi bi-box-arrow-up-right"></i> Served
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
            
            <div x-show="filteredOrders.length === 0" class="col-12 text-center py-5">
                <i class="bi bi-cup-hot text-secondary" style="font-size: 5rem;"></i>
                <h3 class="text-secondary mt-3">Tidak ada pesanan aktif.</h3>
            </div>
        </div>
    </div>

    <!-- Audio for notification -->
    <audio id="notificationSound" preload="auto">
        <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
    </audio>

    <script>
        function kitchenSystem() {
            return {
                orders: [],
                loading: true,
                filterStatus: 'all',
                currentTime: '',
                pollingInterval: null,
                lastOrderCount: 0,
                
                init() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000); // update clock every second
                    
                    this.fetchData();
                    this.pollingInterval = setInterval(() => this.fetchData(), 5000); // Poll every 5s
                },
                
                updateTime() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                },
                
                async fetchData() {
                    try {
                        const response = await fetch('/kitchen/data');
                        const data = await response.json();
                        
                        // Check for new orders to play sound
                        const newOrderCount = data.reduce((acc, order) => {
                            return acc + order.items.filter(i => i.kitchen_status === 'new').length;
                        }, 0);
                        
                        if (newOrderCount > this.lastOrderCount && this.lastOrderCount > 0) {
                            this.playSound();
                        }
                        this.lastOrderCount = newOrderCount;
                        
                        this.orders = data;
                        this.loading = false;
                    } catch (error) {
                        console.error('Error fetching KDS data:', error);
                    }
                },
                
                playSound() {
                    const audio = document.getElementById('notificationSound');
                    if (audio) {
                        audio.play().catch(e => console.log('Audio play failed:', e));
                    }
                },
                
                async updateStatus(itemId, status) {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                                        || '{{ csrf_token() }}'; // fallback if no meta tag

                        const response = await fetch(`/kitchen/items/${itemId}/status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ status: status })
                        });
                        
                        const result = await response.json();
                        if (result.success) {
                            this.fetchData(); // Refresh data immediately
                        }
                    } catch (error) {
                        console.error('Error updating status:', error);
                        alert('Gagal mengupdate status pesanan!');
                    }
                },
                
                get filteredOrders() {
                    if (this.filterStatus === 'all') return this.orders;
                    
                    // Filter orders that have at least one item matching the filter status
                    return this.orders.filter(order => {
                        return order.items.some(item => item.kitchen_status === this.filterStatus);
                    }).map(order => {
                        // Return a copy of the order with only matching items, OR keep all items to preserve context?
                        // For KDS, it's usually better to keep all items but maybe just visually dim the non-matching ones.
                        // Here we just return the full order if it matches the filter.
                        return order;
                    });
                }
            }
        }
    </script>
</body>
</html>
