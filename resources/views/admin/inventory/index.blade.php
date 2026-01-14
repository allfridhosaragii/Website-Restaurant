@extends('layouts.admin')
@section('title', 'Stok Harian')

@push('styles')
<style>
    .inventory-card {
        background: linear-gradient(135deg, var(--surface) 0%, var(--surface-light) 100%);
        border: 1px solid var(--border-medium);
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.3s ease;
    }
    .inventory-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .inventory-card .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1;
    }
    .inventory-card .stat-label {
        font-size: 0.8rem;
        opacity: 0.7;
    }
    
    .stock-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .stock-table th {
        background: var(--surface);
        font-weight: 600;
        padding: 12px 16px;
        text-align: left;
        border-bottom: 2px solid var(--border-medium);
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .stock-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-light);
        vertical-align: middle;
    }
    .stock-table tbody tr:hover {
        background: var(--surface-light);
    }
    
    .menu-thumb {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        object-fit: cover;
        background: var(--surface-light);
    }
    
    .stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .stock-badge.success { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .stock-badge.info { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .stock-badge.warning { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .stock-badge.danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    
    .stock-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .stock-input {
        width: 70px;
        text-align: center;
        padding: 6px 8px;
        border: 1px solid var(--border-medium);
        border-radius: 8px;
        background: var(--surface);
        color: var(--text-primary);
        font-weight: 600;
    }
    .stock-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 1rem;
    }
    .stock-btn:hover { transform: scale(1.1); }
    .stock-btn.minus { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .stock-btn.plus { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .stock-btn.reset { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    
    .availability-toggle {
        position: relative;
        width: 50px;
        height: 26px;
        background: #ccc;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s;
    }
    .availability-toggle.active { background: #22c55e; }
    .availability-toggle::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 20px;
        height: 20px;
        background: white;
        border-radius: 50%;
        transition: all 0.3s;
    }
    .availability-toggle.active::after { left: 27px; }
    
    .filter-bar {
        display: flex;
        gap: 12px;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        align-items: center;
    }
    .filter-bar select {
        padding: 10px 16px;
        border-radius: 10px;
        border: 1px solid var(--border-medium);
        background: var(--surface);
        color: var(--text-primary);
        font-size: 0.9rem;
    }
    
    .btn-reset-all {
        padding: 10px 20px;
        border-radius: 10px;
        border: none;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-reset-all:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4); }
    
    .category-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        background: rgba(139, 92, 246, 0.15);
        color: #8b5cf6;
    }
</style>
@endpush

@section('content')
<section class="section bg-cream">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h3 class="mb-1">
                    <i class="bi bi-box-seam text-primary me-2"></i>Stok Harian
                </h3>
                <p class="text-muted mb-0">Kelola ketersediaan menu setiap hari</p>
            </div>
            <button class="btn-reset-all" onclick="resetAllStock()">
                <i class="bi bi-arrow-clockwise"></i>
                Reset Semua Stok
            </button>
        </div>

        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="inventory-card">
                    <div class="stat-value text-primary">{{ $totalMenus }}</div>
                    <div class="stat-label">Total Menu</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="inventory-card">
                    <div class="stat-value text-success">{{ $availableMenus }}</div>
                    <div class="stat-label">Tersedia</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="inventory-card">
                    <div class="stat-value text-warning">{{ $lowStockMenus }}</div>
                    <div class="stat-label">Stok Rendah</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="inventory-card">
                    <div class="stat-value text-danger">{{ $outOfStockMenus }}</div>
                    <div class="stat-label">Habis</div>
                </div>
            </div>
        </div>

        {{-- Filter & Table --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" class="filter-bar">
                    <select name="category" onchange="this.form.submit()">
                        <option value="all">Semua Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    <select name="status" onchange="this.form.submit()">
                        <option value="all">Semua Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Stok Rendah</option>
                        <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>Habis</option>
                    </select>
                </form>

                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="stock-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;"></th>
                                <th>Menu</th>
                                <th>Kategori</th>
                                <th>Stok Hari Ini</th>
                                <th>Max Stok</th>
                                <th>Status</th>
                                <th>Tersedia</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus as $menu)
                            <tr id="menu-row-{{ $menu->id }}">
                                <td>
                                    <img src="{{ $menu->image_url ?: 'https://via.placeholder.com/50' }}" 
                                         alt="{{ $menu->name }}" 
                                         class="menu-thumb">
                                </td>
                                <td>
                                    <strong>{{ $menu->name }}</strong>
                                    <br><small class="text-muted">Rp {{ number_format($menu->price, 0, ',', '.') }}</small>
                                </td>
                                <td><span class="category-badge">{{ $menu->category }}</span></td>
                                <td>
                                    <div class="stock-controls">
                                        <button class="stock-btn minus" onclick="adjustStock({{ $menu->id }}, 'decrease')">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <input type="number" 
                                               class="stock-input" 
                                               id="stock-{{ $menu->id }}" 
                                               value="{{ $menu->daily_stock }}"
                                               onchange="updateStock({{ $menu->id }})">
                                        <button class="stock-btn plus" onclick="adjustStock({{ $menu->id }}, 'increase')">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" 
                                           class="stock-input" 
                                           id="max-stock-{{ $menu->id }}" 
                                           value="{{ $menu->max_daily_stock }}"
                                           onchange="updateStock({{ $menu->id }})">
                                </td>
                                <td>
                                    <span class="stock-badge {{ $menu->stock_color }}" id="status-{{ $menu->id }}">
                                        {{ $menu->stock_status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="availability-toggle {{ $menu->is_available ? 'active' : '' }}" 
                                         id="toggle-{{ $menu->id }}"
                                         onclick="toggleAvailability({{ $menu->id }})"></div>
                                </td>
                                <td>
                                    <button class="stock-btn reset" onclick="adjustStock({{ $menu->id }}, 'reset')" title="Reset ke Max">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada menu ditemukan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';

async function updateStock(menuId) {
    const dailyStock = document.getElementById('stock-' + menuId).value;
    const maxStock = document.getElementById('max-stock-' + menuId).value;

    try {
        const res = await fetch(`/admin/inventory/${menuId}/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                daily_stock: parseInt(dailyStock),
                max_daily_stock: parseInt(maxStock)
            })
        });
        const data = await res.json();
        if (data.success) {
            updateRowUI(data.menu);
        }
    } catch (e) {
        console.error('Failed to update stock:', e);
    }
}

async function adjustStock(menuId, action) {
    try {
        const res = await fetch(`/admin/inventory/${menuId}/adjust`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ action: action, quantity: 1 })
        });
        const data = await res.json();
        if (data.success) {
            updateRowUI(data.menu);
        }
    } catch (e) {
        console.error('Failed to adjust stock:', e);
    }
}

async function toggleAvailability(menuId) {
    try {
        const res = await fetch(`/admin/inventory/${menuId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        const data = await res.json();
        if (data.success) {
            const toggle = document.getElementById('toggle-' + menuId);
            toggle.classList.toggle('active', data.is_available);
        }
    } catch (e) {
        console.error('Failed to toggle availability:', e);
    }
}

async function resetAllStock() {
    if (!confirm('Reset semua stok menu ke nilai maksimum?')) return;

    try {
        const res = await fetch('/admin/inventory/reset-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        const data = await res.json();
        if (data.success) {
            location.reload();
        }
    } catch (e) {
        console.error('Failed to reset all stock:', e);
    }
}

function updateRowUI(menu) {
    document.getElementById('stock-' + menu.id).value = menu.daily_stock;
    document.getElementById('max-stock-' + menu.id).value = menu.max_daily_stock;
    
    const statusEl = document.getElementById('status-' + menu.id);
    statusEl.className = 'stock-badge ' + menu.stock_color;
    statusEl.textContent = menu.stock_status;
    
    const toggle = document.getElementById('toggle-' + menu.id);
    toggle.classList.toggle('active', menu.is_available);
}
</script>
@endpush
