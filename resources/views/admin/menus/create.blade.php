@extends('layouts.admin')
@section('title', 'Tambah Menu')
@section('content')
<section class="section bg-cream">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-1">Tambah Menu Baru</h3>
                <p class="text-muted mb-0">Isi form di bawah untuk menambahkan menu baru</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form action="/admin/menus" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Menu <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price') }}" min="0" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="Makanan" {{ old('category') == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                                        <option value="Minuman" {{ old('category') == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                                        <option value="Dessert" {{ old('category') == 'Dessert' ? 'selected' : '' }}>Dessert</option>
                                        <option value="Appetizer" {{ old('category') == 'Appetizer' ? 'selected' : '' }}>Appetizer</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="stock" class="form-label">Stok Awal <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                           id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" required>
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="min_stock" class="form-label">Batas Stok Minimum <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('min_stock') is-invalid @enderror" 
                                           id="min_stock" name="min_stock" value="{{ old('min_stock', 5) }}" min="0" required>
                                    @error('min_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gambar Menu</label>
                                <ul class="nav nav-tabs" id="imageTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="upload-tab" data-bs-toggle="tab" 
                                                data-bs-target="#upload" type="button">
                                            <i class="bi bi-cloud-upload me-1"></i>Upload File
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="url-tab" data-bs-toggle="tab" 
                                                data-bs-target="#url" type="button">
                                            <i class="bi bi-link-45deg me-1"></i>URL Gambar
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content border border-top-0 p-3 rounded-bottom" id="imageTabContent">
                                    <div class="tab-pane fade show active" id="upload" role="tabpanel">
                                        <input type="file" class="form-control" id="image_file_input" accept="image/*">
                                        <input type="hidden" name="image_url" id="uploaded_image_url">
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <label class="form-label fw-semibold mb-2">
                                                <i class="bi bi-sliders me-1"></i>Mode Upload:
                                            </label>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="uploadMode" id="modeOriginal" value="original" checked>
                                                    <label class="form-check-label" for="modeOriginal">
                                                        <strong>🖼️ Original</strong>
                                                        <small class="text-muted d-block">Resolusi & ukuran asli (upload lebih lama)</small>
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="uploadMode" id="modeCompressed" value="compressed">
                                                    <label class="form-check-label" for="modeCompressed">
                                                        <strong>⚡ Cepat</strong>
                                                        <small class="text-muted d-block">Kompres 98% (upload 5x lebih cepat)</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="uploadProgressContainer" class="mt-2"></div>
                                    </div>
                                    <div class="tab-pane fade" id="url" role="tabpanel">
                                        <input type="url" class="form-control @error('image_url') is-invalid @enderror" 
                                               id="image_url_manual" name="image_url" 
                                               placeholder="https://images.unsplash.com/photo-xxx" 
                                               value="{{ old('image_url') }}">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Gunakan URL dari Unsplash atau sumber publik lainnya.
                                        </small>
                                        @error('image_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3" id="imagePreviewContainer" style="display: none;">
                                <label class="form-label">Preview Gambar</label>
                                <div class="border rounded p-2">
                                    <img id="imagePreview" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            </div>
                            <div x-data="modifiersManager([])" class="mb-4">
                                <hr class="my-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Modifiers / Add-ons</h5>
                                    <button type="button" @click="addModifier()" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Modifier
                                    </button>
                                </div>
                                <div class="alert alert-info py-2" x-show="modifiers.length === 0">
                                    Menu ini belum memiliki modifier.
                                </div>
                                
                                <template x-for="(modifier, mIndex) in modifiers" :key="mIndex">
                                    <div class="card mb-3 border-primary shadow-sm">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                            <h6 class="mb-0 text-primary">Modifier #<span x-text="mIndex + 1"></span></h6>
                                            <button type="button" @click="removeModifier(mIndex)" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <div class="card-body py-3">
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label small">Nama Group <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm" x-model="modifier.name" placeholder="Contoh: Level Pedas" required>
                                                    <input type="hidden" :name="`modifiers[${mIndex}][id]`" :value="modifier.id">
                                                    <input type="hidden" :name="`modifiers[${mIndex}][name]`" :value="modifier.name">
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label small">Tipe</label>
                                                    <select class="form-select form-select-sm" x-model="modifier.type">
                                                        <option value="single">Single (Radio)</option>
                                                        <option value="multiple">Multiple (Checkbox)</option>
                                                    </select>
                                                    <input type="hidden" :name="`modifiers[${mIndex}][type]`" :value="modifier.type">
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <label class="form-label small">Wajib Pilih</label>
                                                    <div class="form-check form-switch mt-1">
                                                        <input class="form-check-input" type="checkbox" x-model="modifier.is_required" :value="1">
                                                    </div>
                                                    <input type="hidden" :name="`modifiers[${mIndex}][is_required]`" :value="modifier.is_required ? 1 : 0">
                                                </div>
                                                <div class="col-md-3 mb-2" x-show="modifier.type === 'multiple'">
                                                    <label class="form-label small">Max Select</label>
                                                    <input type="number" class="form-control form-control-sm" x-model="modifier.max_select" min="1">
                                                    <input type="hidden" :name="`modifiers[${mIndex}][max_select]`" :value="modifier.max_select">
                                                </div>
                                            </div>
                                            
                                            <div class="mt-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label mb-0 fw-bold">Options / Pilihan</label>
                                                    <button type="button" @click="addOption(mIndex)" class="btn btn-sm btn-light border">
                                                        <i class="bi bi-plus me-1"></i>Option
                                                    </button>
                                                </div>
                                                
                                                <table class="table table-sm table-bordered mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Nama Pilihan</th>
                                                            <th width="30%">Harga Tambahan (Rp)</th>
                                                            <th width="10%"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <template x-for="(opt, oIndex) in modifier.options" :key="oIndex">
                                                            <tr>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm" x-model="opt.name" placeholder="Level 1" required>
                                                                    <input type="hidden" :name="`modifiers[${mIndex}][options][${oIndex}][id]`" :value="opt.id">
                                                                    <input type="hidden" :name="`modifiers[${mIndex}][options][${oIndex}][name]`" :value="opt.name">
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control form-control-sm" x-model="opt.price" min="0">
                                                                    <input type="hidden" :name="`modifiers[${mIndex}][options][${oIndex}][price]`" :value="opt.price">
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button" @click="removeOption(mIndex, oIndex)" class="btn btn-sm btn-outline-danger">
                                                                        <i class="bi bi-x"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                        <tr x-show="modifier.options.length === 0">
                                                            <td colspan="3" class="text-center text-muted py-2">Belum ada option.</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-2"></i>Simpan Menu
                                </button>
                                <a href="/admin/menus" class="btn btn-outline-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script src="{{ asset('js/cloudinary-upload.js') }}"></script>
<script>
(function() {
    const fileInput = document.getElementById('image_file_input');
    const urlInput = document.getElementById('uploaded_image_url');
    const manualUrlInput = document.getElementById('image_url_manual');
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');
    let isUploading = false;
    // Initialize progress bar
    const progressBar = CloudinaryUploader.createProgressBar('uploadProgressContainer');
    fileInput.addEventListener('change', async function(e) {
        const file = e.target.files[0];
        if (!file) return;
        // Validate file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            alert('Ukuran file maksimal 10MB');
            fileInput.value = '';
            return;
        }
        // Show preview immediately
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreviewContainer').style.display = 'block';
        };
        reader.readAsDataURL(file);
        // Start upload
        isUploading = true;
        submitBtn.disabled = true;
        progressBar.reset();
        progressBar.show();
        try {
            // Check compression mode
            const isCompressed = document.getElementById('modeCompressed').checked;
            const result = await CloudinaryUploader.upload(file, {
                folder: 'culinaire/menus',
                onProgress: (percent, loaded, total) => {
                    progressBar.update(percent, loaded, total);
                },
                compress: isCompressed,
                compressionOptions: {
                    maxWidth: 4096,  // 4K max
                    maxHeight: 4096,
                    quality: 0.98   // 98% quality
                }
            });
            urlInput.value = result.secure_url;
            progressBar.success('Upload berhasil!');
            document.getElementById('imagePreview').src = result.secure_url;
        } catch (error) {
            console.error('Upload error:', error);
            progressBar.error(error.message || 'Upload gagal');
            fileInput.value = '';
            urlInput.value = '';
        } finally {
            isUploading = false;
            submitBtn.disabled = false;
        }
    });
    // Manual URL input
    manualUrlInput.addEventListener('input', function(e) {
        const url = e.target.value;
        if (url) {
            document.getElementById('imagePreview').src = url;
            document.getElementById('imagePreviewContainer').style.display = 'block';
            urlInput.value = ''; // Clear uploaded URL when using manual
        } else {
            document.getElementById('imagePreviewContainer').style.display = 'none';
        }
    });
    // Prevent form submission during upload
    form.addEventListener('submit', function(e) {
        if (isUploading) {
            e.preventDefault();
            alert('Mohon tunggu, gambar sedang diupload...');
            return false;
        }
    });
})();

document.addEventListener('alpine:init', () => {
    Alpine.data('modifiersManager', (initialModifiers) => ({
        modifiers: initialModifiers || [],
        
        addModifier() {
            this.modifiers.push({
                id: null,
                name: '',
                type: 'single',
                is_required: false,
                max_select: 1,
                options: [
                    { id: null, name: '', price: 0 }
                ]
            });
        },
        
        removeModifier(index) {
            if (confirm('Hapus modifier ini?')) {
                this.modifiers.splice(index, 1);
            }
        },
        
        addOption(modifierIndex) {
            this.modifiers[modifierIndex].options.push({
                id: null,
                name: '',
                price: 0
            });
        },
        
        removeOption(modifierIndex, optionIndex) {
            this.modifiers[modifierIndex].options.splice(optionIndex, 1);
        }
    }));
});
</script>
@endpush
@endsection