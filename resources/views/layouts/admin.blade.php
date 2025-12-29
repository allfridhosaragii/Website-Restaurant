<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Culinaire Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <!-- View Transitions API -->
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
                <div class="d-none d-md-flex flex-grow-1" style="max-width: 400px;">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control bg-light border-0" placeholder="Cari...">
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 ms-auto">
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
                            <img src="https://i.pravatar.cc/40?img=12" alt="Admin" class="rounded-circle" 
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
    
    <!-- Real-time Maintenance Mode Detection for Admin -->
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
</body>
</html>