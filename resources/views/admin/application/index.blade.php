@extends('layouts.admin')
@section('title', 'Pengaturan Aplikasi')
@push('styles')
<style>
.app-settings-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}
.app-settings-header h1 {
    margin: 0;
    font-size: 1.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.app-settings-header h1 i {
    color: var(--accent);
}
.app-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 1.5rem;
    overflow: hidden;
    margin-bottom: 1.5rem;
}
.app-card-header {
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(12, 42, 54, 0.03) 0%, rgba(200, 155, 58, 0.05) 100%);
    border-bottom: 1px solid rgba(12, 42, 54, 0.08);
}
.app-card-header h3 {
    margin: 0 0 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.app-card-header h3 i {
    color: var(--accent);
}
.app-card-header p {
    margin: 0;
    color: var(--text-muted);
    font-size: 0.9rem;
}
.app-card-body {
    padding: 1.5rem;
}
.app-form-group {
    margin-bottom: 1.5rem;
}
.app-form-group:last-child {
    margin-bottom: 0;
}
.app-form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
}
.app-form-help {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 0.5rem;
}
.app-form-input {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid rgba(12, 42, 54, 0.1);
    border-radius: 0.75rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.8);
}
.app-form-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 4px rgba(200, 155, 58, 0.15);
    outline: none;
}
.app-upload-zone {
    border: 2px dashed rgba(12, 42, 54, 0.2);
    border-radius: 1rem;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    background: rgba(12, 42, 54, 0.02);
}
.app-upload-zone:hover {
    border-color: var(--accent);
    background: rgba(200, 155, 58, 0.05);
}
.app-upload-zone.dragover {
    border-color: var(--accent);
    background: rgba(200, 155, 58, 0.1);
}
.app-upload-zone i {
    font-size: 3rem;
    color: var(--accent);
    margin-bottom: 1rem;
}
.app-upload-zone h5 {
    margin: 0 0 0.5rem;
    color: var(--text-primary);
}
.app-upload-zone p {
    margin: 0;
    color: var(--text-muted);
    font-size: 0.9rem;
}
.app-current-file {
    background: rgba(25, 135, 84, 0.1);
    border: 1px solid rgba(25, 135, 84, 0.2);
    border-radius: 0.75rem;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.app-current-file i {
    font-size: 2.5rem;
    color: #198754;
}
.app-current-file-info {
    flex: 1;
}
.app-current-file-info h6 {
    margin: 0 0 0.25rem;
    font-weight: 600;
}
.app-current-file-info p {
    margin: 0;
    font-size: 0.85rem;
    color: var(--text-muted);
}
.app-current-file-actions {
    display: flex;
    gap: 0.5rem;
}
.app-save-bar {
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.1) 0%, rgba(25, 135, 84, 0.05) 100%);
    border-top: 1px solid rgba(25, 135, 84, 0.2);
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}
.app-info-box {
    background: rgba(13, 110, 253, 0.1);
    border: 1px solid rgba(13, 110, 253, 0.2);
    border-radius: 0.75rem;
    padding: 1rem;
}
.app-info-box h6 {
    margin: 0 0 0.75rem;
    color: #0d6efd;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.app-info-box ul {
    margin: 0;
    padding-left: 1.25rem;
}
.app-info-box li {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}
[data-theme="dark"] .app-card {
    background: rgba(22, 37, 43, 0.9);
    border-color: rgba(255, 255, 255, 0.1);
}
[data-theme="dark"] .app-card-header {
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 255, 255, 0.06);
}
[data-theme="dark"] .app-form-input {
    background: rgba(22, 37, 43, 0.8);
    border-color: rgba(255, 255, 255, 0.1);
    color: var(--text-light);
}
[data-theme="dark"] .app-upload-zone {
    border-color: rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.02);
}
</style>
@endpush
@section('content')
<div class="app-settings-header">
    <h1><i class="bi bi-phone"></i> Pengaturan Aplikasi</h1>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

    <div class="col-lg-8">
        <div class="app-card">
            <div class="app-card-header">
                <h3><i class="bi bi-upload"></i> Upload File APK</h3>
                <p>Upload file APK langsung untuk download instan tanpa login</p>
            </div>
            <div class="app-card-body">
                @if($currentApk)
                <div class="app-current-file mb-3">
                    <i class="bi bi-file-earmark-zip"></i>
                    <div class="app-current-file-info">
                        <h6>{{ $currentApk['name'] }}</h6>
                        <p>{{ $currentApk['size'] }} • Diupload {{ $currentApk['date'] }}</p>
                    </div>
                    <div class="app-current-file-actions">
                        <a href="{{ $currentApk['url'] }}" class="btn btn-sm btn-outline-success" target="_blank">
                            <i class="bi bi-download"></i> Test Download
                        </a>
                    </div>
                </div>
                @endif
                
                <div class="app-form-group">
                    <label class="app-form-label">File APK Baru</label>
                    <div class="app-upload-zone" id="uploadZone" onclick="document.getElementById('apkFile').click()">
                        <i class="bi bi-cloud-arrow-up"></i>
                        <h5>Klik atau drag file APK ke sini</h5>
                        <p>Maksimal ukuran file: 200MB (Direct Speed 🚀)</p>
                    </div>
                    <input type="file" name="apk_file" id="apkFile" accept=".apk" style="display: none;">
                    
                    <div id="filePreview" style="display: none;" class="mt-3">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-file-earmark me-2"></i>
                            <span id="fileName"></span>
                            <span class="text-muted ms-2" id="fileSize"></span>
                        </div>
                    </div>

                    <div id="uploadProgressContainer" style="display: none;" class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-primary fw-semibold" id="uploadStatusText">Mengunggah...</span>
                            <span class="text-primary fw-semibold" id="uploadPercentage">0%</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 5px;">
                            <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-save-bar">
                <button type="button" id="btnUpload" class="btn btn-primary btn-lg">
                    <i class="bi bi-cloud-upload me-2"></i>Mulai Upload
                </button>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="app-card">
            <div class="app-card-header">
                <h3><i class="bi bi-clock-history"></i> Riwayat Upload</h3>
                <p>100 Upload terakhir</p>
            </div>
            <div class="app-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Nama File</th>
                                <th>Ukuran</th>
                                <th class="pe-3">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $item)
                            <tr>
                                <td class="ps-3 fw-medium">{{ $item['name'] }}</td>
                                <td>{{ $item['size'] }}</td>
                                <td class="pe-3 text-muted">{{ $item['date'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Belum ada riwayat upload</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const uploadZone = document.getElementById('uploadZone');
const fileInput = document.getElementById('apkFile');
const filePreview = document.getElementById('filePreview');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');
const btnUpload = document.getElementById('btnUpload');
const uploadProgressContainer = document.getElementById('uploadProgressContainer');
const uploadProgressBar = document.getElementById('uploadProgressBar');
const uploadPercentage = document.getElementById('uploadPercentage');
const uploadStatusText = document.getElementById('uploadStatusText');

// Drag and drop
uploadZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadZone.classList.add('dragover');
});

uploadZone.addEventListener('dragleave', () => {
    uploadZone.classList.remove('dragover');
});

uploadZone.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadZone.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0 && files[0].name.endsWith('.apk')) {
        fileInput.files = files;
        showFilePreview(files[0]);
    } else {
        alert('Hanya file APK yang diperbolehkan');
    }
});

// File input change
fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        showFilePreview(e.target.files[0]);
    }
});

function showFilePreview(file) {
    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);
    filePreview.style.display = 'block';
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

// Direct Upload Logic
btnUpload.addEventListener('click', async () => {
    const file = fileInput.files[0];
    if (!file) {
        alert('Silakan pilih file APK terlebih dahulu');
        return;
    }

    try {
        btnUpload.disabled = true;
        uploadProgressContainer.style.display = 'block';
        uploadStatusText.textContent = 'Menyiapkan upload...';
        
        // 1. Dapatkan Signed URL dari Laravel
        const urlResponse = await fetch('/admin/application/generate-upload-url', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ filename: file.name })
        });

        const urlData = await urlResponse.json();
        if (!urlData.success) throw new Error(urlData.message);

        const uploadUrl = urlData.upload_url;
        const newFilename = urlData.filename;

        // 2. Upload langsung ke Supabase pakai XHR (biar ada progress)
        uploadStatusText.textContent = 'Mengunggah file ke Cloud...';
        
        const xhr = new XMLHttpRequest();
        xhr.open('PUT', uploadUrl, true);
        
        // Supabase expects the file in the body for PUT
        xhr.upload.onprogress = (e) => {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                uploadProgressBar.style.width = percent + '%';
                uploadPercentage.textContent = percent + '%';
            }
        };

        xhr.onload = async () => {
            if (xhr.status >= 200 && xhr.status < 300) {
                // 3. Finalisasi di Laravel
                uploadStatusText.textContent = 'Menyimpan konfigurasi...';
                const finalResponse = await fetch('/admin/application/finalize-upload', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        filename: newFilename,
                        size: formatFileSize(file.size)
                    })
                });

                if (finalResponse.ok) {
                    uploadStatusText.textContent = 'Berhasil!';
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    throw new Error('Gagal finalisasi data');
                }
            } else {
                console.error(xhr.responseText);
                throw new Error('Gagal mengunggah file ke Supabase storage');
            }
        };

        xhr.onerror = () => {
            throw new Error('Koneksi terputus saat mengunggah');
        };

        xhr.send(file);

    } catch (error) {
        alert('Error: ' + error.message);
        btnUpload.disabled = false;
        uploadProgressContainer.style.display = 'none';
    }
});
</script>
@endpush
