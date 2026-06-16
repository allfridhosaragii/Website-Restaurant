@extends('layouts.admin')

@section('title', 'Table Map Kasir')

@push('styles')
<style>
    .grid-container {
        position: relative;
        background-color: #f8f9fa;
        background-image: linear-gradient(to right, #dee2e6 1px, transparent 1px),
                          linear-gradient(to bottom, #dee2e6 1px, transparent 1px);
        background-size: 50px 50px;
        border: 1px solid #dee2e6;
        margin: 0 auto;
        overflow: hidden;
    }
    
    .table-element {
        position: absolute;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        user-select: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        text-decoration: none !important;
        border: 2px solid transparent;
    }

    .table-element:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 15px rgba(0,0,0,0.2);
    }

    /* Shape */
    .table-element.shape-round { border-radius: 50%; }
    .table-element.shape-square { border-radius: 8px; }
    .table-element.shape-rectangle { border-radius: 8px; }

    /* Status Colors */
    .table-available {
        background-color: #198754; /* Green */
        color: white;
        border-color: #146c43;
    }
    .table-occupied {
        background-color: #ffc107; /* Yellow */
        color: #000;
        border-color: #ffcd39;
    }
    .table-reserved {
        background-color: #dc3545; /* Red */
        color: white;
        border-color: #b02a37;
    }

    .table-label {
        font-weight: bold;
        font-size: 1.2rem;
    }
    .table-info {
        font-size: 0.7rem;
        opacity: 0.9;
        text-align: center;
        line-height: 1.1;
    }
    
    .status-legend {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .legend-box {
        width: 20px;
        height: 20px;
        border-radius: 4px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid" x-data="tableMapSystem()" x-init="initMap()">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">Visual Table Map</h1>
        
        <div class="d-flex gap-2">
            <select class="form-select" x-model="selectedLayout" @change="changeLayout()">
                @foreach($layouts as $l)
                    <option value="{{ $l->id }}">{{ $l->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-primary" @click="fetchTables()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
            <a href="{{ route('admin.pos.index') }}" class="btn btn-primary">
                <i class="bi bi-cart"></i> POS Kasir
            </a>
        </div>
    </div>

    <div class="status-legend">
        <div class="legend-item"><div class="legend-box bg-success"></div> Tersedia (Klik buat pesanan)</div>
        <div class="legend-item"><div class="legend-box bg-warning"></div> Terisi (Klik lihat pesanan)</div>
        <div class="legend-item"><div class="legend-box bg-danger"></div> Reservasi</div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body overflow-auto" style="min-height: 60vh;">
            @if($layout)
            <div class="grid-container" 
                 style="width: {{ $layout->grid_width * 50 }}px; height: {{ $layout->grid_height * 50 }}px;">
                
                <template x-for="table in tables" :key="table.id">
                    <a href="#" @click.prevent="handleTableClick(table)"
                       class="table-element"
                       :class="[
                           'shape-' + table.shape, 
                           'table-' + table.status,
                           table.table_group_id ? 'border-primary border-3 shadow-lg border-dashed' : ''
                       ]"
                       :title="getTooltip(table)"
                       :style="'left: ' + (table.position_x * 50) + 'px; ' +
                              'top: ' + (table.position_y * 50) + 'px; ' +
                              'width: ' + (table.width * 50) + 'px; ' +
                              'height: ' + (table.height * 50) + 'px;'">
                        
                        <div x-show="table.table_group_id" class="position-absolute top-0 end-0 badge bg-primary rounded-circle" style="transform: translate(30%, -30%); font-size: 0.6rem;">G</div>
                        
                        <span class="table-label" x-text="table.number"></span>
                        <div class="table-info mt-1">
                            <div><i class="bi bi-people-fill"></i> <span x-text="table.capacity"></span></div>
                            <div x-show="table.customer_name" class="mt-1 font-weight-bold" style="font-size:0.65rem;" x-text="table.customer_name"></div>
                        </div>
                    </a>
                </template>

            </div>
            
            <!-- Table Action Modal -->
            <div class="modal fade" id="tableActionModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Meja <span x-text="selectedTable?.number"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="d-grid gap-3">
                                <button type="button" class="btn btn-primary" @click="goToOrder()">
                                    <i class="bi bi-eye me-2"></i>Lihat Pesanan
                                </button>
                                
                                <template x-if="!selectedTable?.table_group_id">
                                    <button type="button" class="btn btn-outline-info" @click="showMergeForm()">
                                        <i class="bi bi-diagram-3 me-2"></i>Gabung Meja (Merge)
                                    </button>
                                </template>
                            </div>
                            
                            <!-- Merge Form -->
                            <div x-show="isMerging" class="mt-4 p-3 border rounded bg-light">
                                <h6>Pilih Meja untuk Digabung</h6>
                                <p class="text-muted small">Maksimal 4 meja tambahan.</p>
                                
                                <form action="{{ url('/admin/table-groups/merge') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="table_ids[]" :value="selectedTable?.id">
                                    
                                    <div class="row g-2 mb-3">
                                        <template x-for="t in getMergeableTables()" :key="t.id">
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="table_ids[]" :value="t.id" :id="'merge_' + t.id">
                                                    <label class="form-check-label" :for="'merge_' + t.id">
                                                        Meja <span x-text="t.number"></span>
                                                        <div class="small text-muted" x-text="t.customer_name"></div>
                                                    </label>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-secondary" @click="isMerging = false">Batal</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Konfirmasi Gabung</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="text-center py-5">
                <p class="text-muted">Tidak ada layout meja aktif. Silakan buat layout di menu Manajemen Layout Meja terlebih dahulu.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function tableMapSystem() {
    return {
        selectedLayout: '{{ $selectedLayoutId }}',
        tables: [],
        pollInterval: null,

        selectedTable: null,
        isMerging: false,
        actionModal: null,

        initMap() {
            if(this.selectedLayout) {
                this.fetchTables();
                // Polling every 10 seconds
                this.pollInterval = setInterval(() => {
                    this.fetchTables();
                }, 10000);
            }
            // Initialize modal
            const modalEl = document.getElementById('tableActionModal');
            if(modalEl) {
                this.actionModal = new bootstrap.Modal(modalEl);
            }
        },

        changeLayout() {
            window.location.href = '?layout_id=' + this.selectedLayout;
        },

        fetchTables() {
            fetch(`/admin/pos/table-map/data?layout_id=${this.selectedLayout}`)
                .then(res => res.json())
                .then(data => {
                    this.tables = data;
                })
                .catch(err => console.error("Error fetching tables", err));
        },

        getTooltip(table) {
            let statusText = 'Tersedia';
            if (table.status === 'occupied') {
                statusText = 'Terisi - ' + table.customer_name;
                if (table.table_group_id) statusText += ' (Grup)';
            }
            if (table.status === 'reserved') statusText = 'Di-reservasi';
            return `Meja ${table.number} | Kapasitas: ${table.capacity} | Status: ${statusText}`;
        },

        handleTableClick(table) {
            if (table.status === 'available') {
                // Redirect to POS with table selected
                window.location.href = '{{ route("admin.pos.index") }}?table=' + table.number;
            } else if (table.status === 'occupied') {
                this.selectedTable = table;
                this.isMerging = false;
                this.actionModal.show();
            } else if (table.status === 'reserved') {
                alert('Meja ini sedang dalam status reservasi.');
            }
        },
        
        goToOrder() {
            if (this.selectedTable && this.selectedTable.order_id) {
                window.location.href = `/admin/orders/${this.selectedTable.order_id}`;
            }
        },
        
        showMergeForm() {
            this.isMerging = true;
        },
        
        getMergeableTables() {
            if (!this.selectedTable) return [];
            return this.tables.filter(t => 
                t.status === 'occupied' && 
                t.id !== this.selectedTable.id && 
                !t.table_group_id
            );
        }
    }
}
</script>
@endpush
