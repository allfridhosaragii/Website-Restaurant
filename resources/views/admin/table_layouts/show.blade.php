@extends('layouts.admin')

@section('title', 'Desain Layout: ' . $layout->name)

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
        background-color: #0d6efd;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        cursor: grab;
        border: 2px solid #0a58ca;
        user-select: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: box-shadow 0.2s;
    }

    .table-element:active {
        cursor: grabbing;
        box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        z-index: 1000;
    }

    .table-element.shape-round {
        border-radius: 50%;
    }

    .table-element.shape-square {
        border-radius: 8px;
    }

    .table-element.shape-rectangle {
        border-radius: 8px;
    }

    .table-label {
        font-weight: bold;
        font-size: 1.2rem;
    }
    .table-capacity {
        font-size: 0.8rem;
        opacity: 0.9;
    }
    
    .delete-btn {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 10px;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
    }
    .table-element:hover .delete-btn {
        display: flex;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Desain Layout: {{ $layout->name }}</h1>
            <p class="text-muted">Grid: {{ $layout->grid_width }}x{{ $layout->grid_height }} | Total Meja: {{ $layout->tables->count() }}</p>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTableModal">
                <i class="bi bi-plus-lg"></i> Tambah Meja
            </button>
            <a href="{{ url('/admin/table-layouts') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body overflow-auto">
            <div id="gridContainer" class="grid-container" 
                 style="width: {{ $layout->grid_width * 50 }}px; height: {{ $layout->grid_height * 50 }}px;">
                
                @foreach($layout->tables as $table)
                    <div class="table-element shape-{{ $table->shape }}"
                         data-id="{{ $table->id }}"
                         style="left: {{ $table->position_x * 50 }}px; 
                                top: {{ $table->position_y * 50 }}px; 
                                width: {{ $table->width * 50 }}px; 
                                height: {{ $table->height * 50 }}px;">
                        
                        <button class="delete-btn" onclick="deleteTable({{ $table->id }})">
                            <i class="bi bi-x"></i>
                        </button>
                        
                        <span class="table-label">{{ $table->number }}</span>
                        <span class="table-capacity"><i class="bi bi-people-fill"></i> {{ $table->capacity }}</span>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>

<!-- Add Table Modal -->
<div class="modal fade" id="addTableModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="addTableForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Meja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Meja</label>
                            <input type="number" class="form-control" name="number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kapasitas (Orang)</label>
                            <input type="number" class="form-control" name="capacity" value="4" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bentuk Meja</label>
                        <select class="form-select" name="shape" id="shapeSelect">
                            <option value="square">Kotak (Square)</option>
                            <option value="rectangle">Persegi Panjang (Rectangle)</option>
                            <option value="round">Bundar (Round)</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lebar (Grid Cell)</label>
                            <input type="number" class="form-control" name="width" id="widthInput" value="2" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tinggi (Grid Cell)</label>
                            <input type="number" class="form-control" name="height" id="heightInput" value="2" min="1" required>
                        </div>
                    </div>
                    
                    <input type="hidden" name="position_x" value="0">
                    <input type="hidden" name="position_y" value="0">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveTable">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const GRID_SIZE = 50;
    const layoutId = {{ $layout->id }};
    const maxGridX = {{ $layout->grid_width }};
    const maxGridY = {{ $layout->grid_height }};
    const token = document.querySelector('meta[name="csrf-token"]').content;

    // Default shape sizing
    document.getElementById('shapeSelect').addEventListener('change', function(e) {
        if(e.target.value === 'square' || e.target.value === 'round') {
            document.getElementById('widthInput').value = 2;
            document.getElementById('heightInput').value = 2;
        } else if(e.target.value === 'rectangle') {
            document.getElementById('widthInput').value = 3;
            document.getElementById('heightInput').value = 2;
        }
    });

    // Add Table AJAX
    document.getElementById('addTableForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveTable');
        btn.disabled = true;

        const formData = new FormData(this);
        const data = Object.fromEntries(formData);

        fetch(`/admin/table-layouts/${layoutId}/tables`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                location.reload();
            } else {
                alert(res.message || 'Gagal menambahkan meja');
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan koneksi');
            btn.disabled = false;
        });
    });

    // Delete Table AJAX
    window.deleteTable = function(tableId) {
        if(!confirm('Hapus meja ini?')) return;
        
        fetch(`/admin/table-layouts/${layoutId}/tables/${tableId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                location.reload();
            }
        });
    };

    // Drag and Drop Logic
    const container = document.getElementById('gridContainer');
    const elements = document.querySelectorAll('.table-element');
    
    let isDragging = false;
    let currentElement = null;
    let startMouseX, startMouseY;
    let startElemX, startElemY;

    elements.forEach(el => {
        el.addEventListener('mousedown', function(e) {
            if(e.target.closest('.delete-btn')) return; // Ignore if clicking delete button
            
            isDragging = true;
            currentElement = this;
            
            startMouseX = e.clientX;
            startMouseY = e.clientY;
            
            startElemX = parseInt(this.style.left) || 0;
            startElemY = parseInt(this.style.top) || 0;
            
            // Prevent text selection
            e.preventDefault();
        });
    });

    document.addEventListener('mousemove', function(e) {
        if(!isDragging || !currentElement) return;

        let dx = e.clientX - startMouseX;
        let dy = e.clientY - startMouseY;

        let newX = startElemX + dx;
        let newY = startElemY + dy;

        currentElement.style.left = newX + 'px';
        currentElement.style.top = newY + 'px';
    });

    document.addEventListener('mouseup', function(e) {
        if(!isDragging || !currentElement) return;
        isDragging = false;

        // Snap to grid
        let currentX = parseInt(currentElement.style.left) || 0;
        let currentY = parseInt(currentElement.style.top) || 0;

        let gridX = Math.round(currentX / GRID_SIZE);
        let gridY = Math.round(currentY / GRID_SIZE);

        // Constrain to container boundaries
        let elemWidthCells = Math.round(currentElement.offsetWidth / GRID_SIZE);
        let elemHeightCells = Math.round(currentElement.offsetHeight / GRID_SIZE);

        if(gridX < 0) gridX = 0;
        if(gridY < 0) gridY = 0;
        if(gridX + elemWidthCells > maxGridX) gridX = maxGridX - elemWidthCells;
        if(gridY + elemHeightCells > maxGridY) gridY = maxGridY - elemHeightCells;

        let finalPxX = gridX * GRID_SIZE;
        let finalPxY = gridY * GRID_SIZE;

        currentElement.style.left = finalPxX + 'px';
        currentElement.style.top = finalPxY + 'px';

        const tableId = currentElement.dataset.id;
        currentElement = null;

        // AJAX update position
        fetch(`/admin/table-layouts/${layoutId}/tables/${tableId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                position_x: gridX,
                position_y: gridY,
                width: elemWidthCells,
                height: elemHeightCells
            })
        }).catch(err => console.error('Failed to save position', err));
    });

</script>
@endpush
