@extends('layouts.admin')
@section('title', 'Edit Menu')
@section('content')
<section class="section bg-cream">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-1">Edit Menu</h3>
                <p class="text-muted mb-0">Perbarui informasi menu</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form action="/admin/menus/{{ $menu->slug }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Menu <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $menu->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description', $menu->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price', $menu->price) }}" min="0" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="Makanan" {{ old('category', $menu->category) == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                                        <option value="Minuman" {{ old('category', $menu->category) == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                                        <option value="Dessert" {{ old('category', $menu->category) == 'Dessert' ? 'selected' : '' }}>Dessert</option>
                                        <option value="Appetizer" {{ old('category', $menu->category) == 'Appetizer' ? 'selected' : '' }}>Appetizer</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @if($menu->image_url)
                                <div class="mb-3">
                                    <label class="form-label">Gambar Saat Ini</label>
                                    <div class="border rounded p-2">
                                        <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="img-fluid rounded" style="max-height: 200px;">
                                    </div>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label">Ganti Gambar</label>
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
                                            Kosongkan jika ingin tetap menggunakan gambar saat ini.
                                        </small>
                                        @error('image_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3" id="imagePreviewContainer" style="display: none;">
                                <label class="form-label">Preview Gambar Baru</label>
                                <div class="border rounded p-2">
                                    <img id="imagePreview" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1" 
                                           {{ old('is_available', $menu->is_available) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_available">
                                        Menu tersedia
                                    </label>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
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
</script>
@endpush
@endsection