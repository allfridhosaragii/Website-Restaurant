<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="{{ url('/admin') }}" class="text-decoration-none text-center d-block">
            <img src="https://res.cloudinary.com/dh9ysyfit/image/upload/v1766594949/IMG_8083_szjpgb.png" alt="Admin Logo" style="height: 50px; width: auto; object-fit: contain; margin-bottom: 5px;">
            <small class="d-block text-light opacity-50 fw-normal" style="font-size: 0.75rem;">Admin Panel</small>
        </a>
    </div>
    <nav>
        <ul class="sidebar-nav">
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/dashboard') }}" class="sidebar-nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/pos') }}" class="sidebar-nav-link {{ request()->is('admin/pos*') ? 'active' : '' }}">
                    <i class="bi bi-calculator"></i>
                    <span>Point of Sale (POS)</span>
                </a>
            </li>
            @if(auth()->user()->role == 'waiter' || auth()->user()->is_admin)
            <li class="sidebar-nav-item">
                <a href="{{ url('/waiter') }}" class="sidebar-nav-link {{ request()->is('waiter*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i>
                    <span>Sistem Waiter</span>
                </a>
            </li>
            @endif
            <li class="sidebar-nav-item mt-4">
                <small class="text-uppercase text-light opacity-50 px-3 mb-2 d-block" style="font-size: 0.7rem;">
                    Manajemen Menu
                </small>
            </li>
            @if(auth()->user()->hasAdminPermission('menus'))
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/menus') }}" class="sidebar-nav-link {{ request()->is('admin/menus*') ? 'active' : '' }}">
                    <i class="bi bi-book"></i>
                    <span>Daftar Menu</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/discounts') }}" class="sidebar-nav-link {{ request()->is('admin/discounts*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i>
                    <span>Manajemen Diskon</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasAdminPermission('categories'))
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/categories') }}" class="sidebar-nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i>
                    <span>Kategori</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasAdminPermission('inventory'))
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/inventory') }}" class="sidebar-nav-link {{ request()->is('admin/inventory*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Stok Harian</span>
                </a>
            </li>
            @endif
            <li class="sidebar-nav-item mt-4">
                <small class="text-uppercase text-light opacity-50 px-3 mb-2 d-block" style="font-size: 0.7rem;">
                    Pesanan & Reservasi
                </small>
            </li>
            @if(auth()->user()->hasAdminPermission('orders'))
            <li class="sidebar-nav-item">
                @php $pendingOrders = \DB::table('orders')->where('status', 'pending')->count(); @endphp
                <a href="{{ url('/admin/orders') }}" class="sidebar-nav-link {{ request()->is('admin/orders*') ? 'active' : '' }}">
                    <i class="bi bi-bag"></i>
                    <span>Pesanan</span>
                    @if($pendingOrders > 0)
                    <span class="badge bg-danger ms-auto">{{ $pendingOrders }}</span>
                    @endif
                </a>
            </li>
            @endif
            @if(auth()->user()->hasAdminPermission('reservations'))
            <li class="sidebar-nav-item">
                @php $pendingReservations = \DB::table('reservations')->where('status', 'pending')->count(); @endphp
                <a href="{{ url('/admin/reservations') }}" class="sidebar-nav-link {{ request()->is('admin/reservations*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i>
                    <span>Reservasi</span>
                    @if($pendingReservations > 0)
                    <span class="badge bg-warning text-dark ms-auto">{{ $pendingReservations }}</span>
                    @endif
                </a>
            </li>
            @endif
            @if(auth()->user()->hasAdminPermission('tables'))
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/table-layouts') }}" class="sidebar-nav-link {{ request()->is('admin/table-layouts*') ? 'active' : '' }}">
                    <i class="bi bi-grid-3x3"></i>
                    <span>Manajemen Layout Meja</span>
                </a>
            </li>
            @endif
            <li class="sidebar-nav-item mt-4">
                <small class="text-uppercase text-light opacity-50 px-3 mb-2 d-block" style="font-size: 0.7rem;">
                    Karyawan
                </small>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/shifts') }}" class="sidebar-nav-link {{ request()->is('admin/shifts*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat Shift</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/attendance') }}" class="sidebar-nav-link {{ request()->is('admin/attendance*') ? 'active' : '' }}">
                    <i class="bi bi-person-check"></i>
                    <span>Absensi</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/payroll') }}" class="sidebar-nav-link {{ request()->is('admin/payroll*') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin"></i>
                    <span>Payroll</span>
                </a>
            </li>
            </li>
            
            <li class="sidebar-nav-item mt-4">
                <small class="text-uppercase text-light opacity-50 px-3 mb-2 d-block" style="font-size: 0.7rem;">
                    CRM & Promo
                </small>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('admin.membership_tiers.index') }}" class="sidebar-nav-link {{ request()->is('admin/membership_tiers*') ? 'active' : '' }}">
                    <i class="bi bi-star"></i>
                    <span>Membership Tiers</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('admin.promos.index') }}" class="sidebar-nav-link {{ request()->is('admin/promos*') ? 'active' : '' }}">
                    <i class="bi bi-gift"></i>
                    <span>Promo Buy X Get Y</span>
                </a>
            </li>
            <li class="sidebar-nav-item mt-4">
                <small class="text-uppercase text-light opacity-50 px-3 mb-2 d-block" style="font-size: 0.7rem;">
                    Keuangan & Akuntansi
                </small>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/expenses') }}" class="sidebar-nav-link {{ request()->is('admin/expenses*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i>
                    <span>Pengeluaran Ops.</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/supplier-debts') }}" class="sidebar-nav-link {{ request()->is('admin/supplier-debts*') ? 'active' : '' }}">
                    <i class="bi bi-journal-minus"></i>
                    <span>Hutang Supplier</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/finance') }}" class="sidebar-nav-link {{ request()->is('admin/finance*') ? 'active' : '' }}">
                    <i class="bi bi-bank"></i>
                    <span>Laporan Keuangan</span>
                </a>
            </li>
            <li class="sidebar-nav-item mt-4">
                <small class="text-uppercase text-light opacity-50 px-3 mb-2 d-block" style="font-size: 0.7rem;">
                    Laporan &amp; Analitik
                </small>
            </li>
            @if(auth()->user()->hasAdminPermission('statistics') || auth()->user()->hasAdminPermission('reports'))
            <li class="sidebar-nav-item">
                    <a href="{{ url('/admin/reports') }}" class="sidebar-nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> <span>Laporan</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ url('/admin/reviews') }}" class="sidebar-nav-link {{ request()->is('admin/reviews*') ? 'active' : '' }}">
                        <i class="bi bi-star"></i> <span>Rating & Review</span>
                    </a>
                </li>
            @endif
            @if(auth()->user()->hasAdminPermission('developer'))
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/developer') }}" class="sidebar-nav-link {{ request()->is('admin/developer') ? 'active' : '' }}">
                    <i class="bi bi-code-slash"></i>
                    <span>{{ __('messages.developer') }}</span>
                </a>
            </li>
            @endif
            <li class="sidebar-nav-item mt-4">
                <small class="text-uppercase text-light opacity-50 px-3 mb-2 d-block" style="font-size: 0.7rem;">
                    Content Management
                </small>
            </li>
            @if(auth()->user()->hasAdminPermission('cms_dashboard'))
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/developer') }}" class="sidebar-nav-link {{ request()->is('admin/developer') ? 'active' : '' }}">
                    <i class="bi bi-collection"></i>
                    <span>CMS Dashboard</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasAdminPermission('cms_pages'))
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/developer/pages') }}" class="sidebar-nav-link {{ request()->is('admin/developer/pages*') ? 'active' : '' }}">
                    <i class="bi bi-file-richtext"></i>
                    <span>Pages</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasAdminPermission('cms_media'))
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/developer/media') }}" class="sidebar-nav-link {{ request()->is('admin/developer/media*') ? 'active' : '' }}">
                    <i class="bi bi-images"></i>
                    <span>Media Library</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasAdminPermission('cms_settings'))
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/developer/settings') }}" class="sidebar-nav-link {{ request()->is('admin/developer/settings*') ? 'active' : '' }}">
                    <i class="bi bi-sliders"></i>
                    <span>Site Settings</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasAdminPermission('cms_settings'))
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/application') }}" class="sidebar-nav-link {{ request()->is('admin/application*') ? 'active' : '' }}">
                    <i class="bi bi-phone"></i>
                    <span>Aplikasi</span>
                </a>
            </li>
            @endif
            <li class="sidebar-nav-item mt-4">
                <small class="text-uppercase text-light opacity-50 px-3 mb-2 d-block" style="font-size: 0.7rem;">
                    Admin
                </small>
            </li>
            @if(auth()->user()->hasAdminPermission('users'))
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/users') }}" class="sidebar-nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Pengguna</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/deposits') }}" class="sidebar-nav-link {{ request()->is('admin/deposits*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i>
                    <span>Manajemen Deposit</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->isSuperAdmin())
            <li class="sidebar-nav-item">
                <a href="{{ url('/admin/access') }}" class="sidebar-nav-link {{ request()->is('admin/access*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock"></i>
                    <span>Admin</span>
                </a>
            </li>
            @endif
        </ul>
    </nav>
    <div class="mt-auto p-3 border-top border-light border-opacity-10">
        <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm w-100">
            <i class="bi bi-house me-2"></i>Kembali ke Website
        </a>
    </div>
</aside>