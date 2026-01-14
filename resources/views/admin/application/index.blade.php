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
.app-preview-box {
    background: rgba(25, 135, 84, 0.1);
    border: 1px solid rgba(25, 135, 84, 0.2);
    border-radius: 0.75rem;
    padding: 1rem;
    margin-top: 1rem;
}
.app-preview-box h6 {
    margin: 0 0 0.5rem;
    color: #198754;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.app-preview-link {
    font-family: monospace;
    font-size: 0.9rem;
    word-break: break-all;
    color: #0d6efd;
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
.app-info-box ol {
    margin: 0;
    padding-left: 1.25rem;
}
.app-info-box li {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}
.app-save-bar {
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.1) 0%, rgba(25, 135, 84, 0.05) 100%);
    border-top: 1px solid rgba(25, 135, 84, 0.2);
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
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

<div class="row">
    <div class="col-lg-8">
        <form action="{{ url('/admin/application') }}" method="POST">
            @csrf
            <div class="app-card">
                <div class="app-card-header">
                    <h3><i class="bi bi-google"></i> Link Download Google Drive</h3>
                    <p>Masukkan link Google Drive untuk file APK aplikasi Anda</p>
                </div>
                <div class="app-card-body">
                    <div class="app-form-group">
                        <label class="app-form-label">Link Google Drive</label>
                        <input type="url" 
                               name="app_download_link" 
                               class="app-form-input" 
                               id="gdriveLink"
                               value="{{ $appDownloadLink ?? '' }}"
                               placeholder="https://drive.google.com/file/d/xxxxx/view?usp=sharing">
                        <p class="app-form-help">
                            Pastikan file memiliki akses "Anyone with the link can view"
                        </p>
                    </div>
                    
                    <div class="app-preview-box" id="previewBox" style="{{ ($appDownloadLink ?? '') ? '' : 'display: none;' }}">
                        <h6><i class="bi bi-check-circle-fill"></i> Direct Download URL</h6>
                        <div class="app-preview-link" id="previewLink">
                            {{ $directDownloadUrl ?? '' }}
                        </div>
                    </div>
                </div>
                <div class="app-save-bar">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Simpan Pengaturan
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <div class="col-lg-4">
        <div class="app-card">
            <div class="app-card-header">
                <h3><i class="bi bi-info-circle"></i> Panduan</h3>
            </div>
            <div class="app-card-body">
                <div class="app-info-box">
                    <h6><i class="bi bi-question-circle"></i> Cara Mendapatkan Link</h6>
                    <ol>
                        <li>Upload file APK ke Google Drive</li>
                        <li>Klik kanan pada file → "Share"</li>
                        <li>Ubah akses menjadi "Anyone with the link"</li>
                        <li>Klik "Copy link"</li>
                        <li>Paste link di form ini</li>
                    </ol>
                </div>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb me-1"></i>
                        Link akan otomatis dikonversi ke format direct download untuk kecepatan maksimal.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('gdriveLink').addEventListener('input', function(e) {
    const link = e.target.value;
    const previewBox = document.getElementById('previewBox');
    const previewLink = document.getElementById('previewLink');
    
    // Extract file ID from Google Drive link
    const match = link.match(/\/d\/([a-zA-Z0-9_-]+)/);
    
    if (match && match[1]) {
        const fileId = match[1];
        const directUrl = 'https://drive.google.com/uc?export=download&id=' + fileId;
        previewLink.textContent = directUrl;
        previewBox.style.display = 'block';
    } else {
        previewBox.style.display = 'none';
    }
});
</script>
@endpush
