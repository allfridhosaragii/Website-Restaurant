<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0C2A36">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Culinaire Admin">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <title>@yield('title', 'Dashboard') - Culinaire Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta name="view-transition" content="same-origin">
    <style>
        /* View Transitions - Smooth page navigation */
        @view-transition {
            navigation: auto;
        }
        ::view-transition-old(root) {
            animation: fade-out 0.25s ease-out forwards;
        }
        ::view-transition-new(root) {
            animation: fade-in 0.25s ease-in forwards;
        }
        @keyframes fade-out {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.98); }
        }
        @keyframes fade-in {
            from { opacity: 0; transform: scale(1.02); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
    @stack('styles')
</head>
<body>
    @include('components.sidebar')
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="main-content-admin">
        <nav class="navbar navbar-expand-lg bg-white shadow-sm rounded-3 mb-4 px-4" style="overflow: visible;">
            <div class="container-fluid">
                <button class="btn btn-link d-lg-none p-0 me-3" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="d-none d-md-flex grow" style="max-width: 400px;">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control bg-light border-0" placeholder="Cari...">
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    {{-- Shift Widget --}}
                    <div id="shiftWidget" x-data="shiftWidget()" x-init="init()">
                        {{-- Active Shift Badge --}}
                        <template x-if="shiftActive">
                            <div class="d-flex align-items-center gap-2">
                                <div class="text-end d-none d-md-block">
                                    <small class="text-success d-block fw-bold"><i class="bi bi-clock"></i> Shift Aktif</small>
                                    <small class="text-muted" x-text="'Mulai ' + clockIn + ' · ' + duration"></small>
                                </div>
                                <button class="btn btn-sm btn-outline-danger" @click="openCloseModal()">
                                    <i class="bi bi-stop-circle"></i> Tutup Shift
                                </button>
                            </div>
                        </template>
                        <template x-if="!shiftActive">
                            <button class="btn btn-sm btn-success" @click="openStartModal()">
                                <i class="bi bi-play-circle"></i> Mulai Shift
                            </button>
                        </template>

                        {{-- Modal: Mulai Shift --}}
                        <div class="modal" :class="{'d-block':showStartModal,'show':showStartModal}" :style="showStartModal?'background:rgba(0,0,0,0.5)':''">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title"><i class="bi bi-play-circle"></i> Mulai Shift</h5>
                                        <button class="btn-close btn-close-white" @click="showStartModal=false"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Modal Awal Kas (Rp)</label>
                                            <input type="number" class="form-control form-control-lg" x-model.number="openingCash" min="0" step="1000" placeholder="0">
                                            <small class="text-muted">Jumlah uang tunai di laci kasir saat memulai shift.</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" @click="showStartModal=false">Batal</button>
                                        <button class="btn btn-success" @click="startShift()" :disabled="isLoading">
                                            <span x-show="!isLoading"><i class="bi bi-play-circle"></i> Mulai Shift</span>
                                            <span x-show="isLoading"><i class="bi bi-hourglass-split"></i> Memproses...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modal: Tutup Shift --}}
                        <div class="modal" :class="{'d-block':showCloseModal,'show':showCloseModal}" :style="showCloseModal?'background:rgba(0,0,0,0.5)':''">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title"><i class="bi bi-stop-circle"></i> Tutup Shift</h5>
                                        <button class="btn-close btn-close-white" @click="showCloseModal=false"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-info">
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <div class="fw-bold fs-5" x-text="totalOrders"></div>
                                                    <small>Total Order</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="fw-bold fs-5" x-text="'Rp '+formatNum(totalRevenue)"></div>
                                                    <small>Total Penjualan</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="fw-bold fs-5" x-text="'Rp '+formatNum(expectedCash)"></div>
                                                    <small>Estimasi Kas Tunai</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Kas Aktual di Laci (Rp)</label>
                                            <input type="number" class="form-control form-control-lg" x-model.number="closingCash" min="0" step="1000">
                                            <div class="mt-2" x-show="closingCash !== null && closingCash !== ''">
                                                <small>Selisih: </small>
                                                <strong :class="(closingCash - expectedCash) >= 0 ? 'text-success' : 'text-danger'"
                                                        x-text="((closingCash - expectedCash) >= 0 ? '+' : '') + 'Rp ' + formatNum(closingCash - expectedCash)"></strong>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Catatan (Opsional)</label>
                                            <textarea class="form-control" x-model="closeNotes" rows="2" placeholder="Catatan penutupan shift..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" @click="showCloseModal=false">Batal</button>
                                        <button class="btn btn-danger" @click="closeShift()" :disabled="isLoading">
                                            <span x-show="!isLoading"><i class="bi bi-stop-circle"></i> Tutup Shift</span>
                                            <span x-show="isLoading"><i class="bi bi-hourglass-split"></i> Memproses...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button class="theme-toggle" id="themeToggle">
                        <i class="bi bi-moon-fill icon-moon"></i>
                        <i class="bi bi-sun-fill icon-sun"></i>
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-link p-0 position-relative" data-bs-toggle="dropdown">
                            <i class="bi bi-bell fs-5 text-muted"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow" style="width: 320px;">
                            <h6 class="dropdown-header">Notifikasi</h6>
                            <a class="dropdown-item py-3" href="#">
                                <div class="d-flex">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="bi bi-calendar-check text-primary"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small">Reservasi baru dari <strong>John Doe</strong></p>
                                        <small class="text-muted">5 menit yang lalu</small>
                                    </div>
                                </div>
                            </a>
                            <a class="dropdown-item py-3" href="#">
                                <div class="d-flex">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="bi bi-cash-stack text-success"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small">Pembayaran diterima <strong>#ORD-2024</strong></p>
                                        <small class="text-muted">15 menit yang lalu</small>
                                    </div>
                                </div>
                            </a>
                            <a class="dropdown-item py-3" href="#">
                                <div class="d-flex">
                                    <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="bi bi-exclamation-triangle text-warning"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small">Stok <strong>Rendang</strong> hampir habis</p>
                                        <small class="text-muted">1 jam yang lalu</small>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center text-primary small" href="#">
                                Lihat semua notifikasi
                            </a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link p-0 d-flex align-items-center gap-2 text-decoration-none" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="Admin" class="rounded-circle" 
                                 style="width: 40px; height: 40px; object-fit: cover;">
                            <div class="d-none d-md-block text-start">
                                <strong class="d-block text-dark small">{{ Auth::user()->name ?? 'Admin' }}</strong>
                                <small class="text-muted">Administrator</small>
                            </div>
                            <i class="bi bi-chevron-down text-muted small"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item" href="{{ url('/admin/profile') }}"><i class="bi bi-person me-2"></i>Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const themeToggle = document.getElementById('themeToggle');
        const htmlElement = document.documentElement;
        const savedTheme = localStorage.getItem('theme') || 'light';
        htmlElement.setAttribute('data-theme', savedTheme);
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                htmlElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
            });
        }
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.toggle('show');
                }
            });
        }
        // Close sidebar when clicking on overlay
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        }
        // Fix dropdown positioning for admin navbar
        document.querySelectorAll('.main-content-admin .dropdown').forEach(function(dropdown) {
            dropdown.addEventListener('show.bs.dropdown', function(e) {
                const toggle = e.target.querySelector('[data-bs-toggle="dropdown"]') || e.target;
                const menu = dropdown.querySelector('.dropdown-menu');
                if (menu && toggle) {
                    const rect = toggle.getBoundingClientRect();
                    menu.style.top = (rect.bottom + 5) + 'px';
                    menu.style.right = (window.innerWidth - rect.right) + 'px';
                    menu.style.left = 'auto';
                }
            });
        });
    </script>
    <script src="{{ asset('js/cursor.js') }}"></script>
    <script src="{{ asset('js/performance-core.js') }}"></script>
    @stack('scripts')
    <script>
        (function() {
            let maintenanceCheckInterval;
            // Check maintenance status
            async function checkMaintenanceStatus() {
                try {
                    const response = await fetch('/api/maintenance-status', {
                        headers: { 'Accept': 'application/json' },
                        cache: 'no-store'
                    });
                    const data = await response.json();
                    if (data.maintenance) {
                        // Stop polling
                        clearInterval(maintenanceCheckInterval);
                        // Redirect to landing page
                        window.location.href = '/';
                    }
                } catch (error) {
                    console.log('Maintenance check failed:', error);
                }
            }
            // Start polling every 5 seconds
            maintenanceCheckInterval = setInterval(checkMaintenanceStatus, 5000);
            // Check immediately on page load
            checkMaintenanceStatus();
        })();
    </script>
    <script src="{{ asset('js/error-tracker.js') }}" defer></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('[PWA] Service Worker registered:', reg.scope))
                    .catch(err => console.log('[PWA] SW registration failed:', err));
            });
        }
    </script>
    <script>
    // Shift Widget Alpine Component
    document.addEventListener('alpine:init', () => {
        Alpine.data('shiftWidget', () => ({
            shiftActive: false,
            clockIn: '',
            duration: '',
            totalOrders: 0,
            totalRevenue: 0,
            expectedCash: 0,
            openingCash: 0,
            closingCash: 0,
            closeNotes: '',
            showStartModal: false,
            showCloseModal: false,
            isLoading: false,

            init() {
                this.fetchStatus();
                setInterval(() => { if (this.shiftActive) this.fetchStatus(); }, 60000);
            },

            async fetchStatus() {
                try {
                    const r = await fetch('/admin/shift/active');
                    const d = await r.json();
                    this.shiftActive = d.active;
                    if (d.active) {
                        this.clockIn = d.clock_in;
                        this.duration = d.duration;
                        this.totalOrders = d.total_orders;
                        this.totalRevenue = d.total_revenue;
                        this.expectedCash = d.expected_cash;
                        this.openingCash = d.opening_cash;
                    }
                } catch(e) {}
            },

            openStartModal() { this.openingCash = 0; this.showStartModal = true; },

            async startShift() {
                this.isLoading = true;
                try {
                    const r = await fetch('/admin/shift/start', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                        body: JSON.stringify({ opening_cash: this.openingCash })
                    });
                    const d = await r.json();
                    if (d.success) {
                        this.showStartModal = false;
                        await this.fetchStatus();
                        alert('Shift dimulai pukul ' + d.clock_in + '! Selamat bekerja.');
                    } else { alert('Error: ' + d.message); }
                } catch(e) { alert('Gagal memulai shift.'); }
                this.isLoading = false;
            },

            async openCloseModal() {
                await this.fetchStatus();
                this.closingCash = 0;
                this.closeNotes = '';
                this.showCloseModal = true;
            },

            async closeShift() {
                this.isLoading = true;
                try {
                    const r = await fetch('/admin/shift/close', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                        body: JSON.stringify({ closing_cash: this.closingCash, notes: this.closeNotes })
                    });
                    const d = await r.json();
                    if (d.success) {
                        this.showCloseModal = false;
                        this.shiftActive = false;
                        const selisih = d.cash_difference;
                        alert(`Shift ditutup!\nDurasi: ${d.duration}\nSelisih Kas: ${selisih >= 0 ? '+' : ''}Rp ${this.formatNum(selisih)}`);
                    } else { alert('Error: ' + d.message); }
                } catch(e) { alert('Gagal menutup shift.'); }
                this.isLoading = false;
            },

            formatNum(n) { return new Intl.NumberFormat('id-ID').format(Math.round(n)); }
        }));
    });
    </script>
</body>
</html>