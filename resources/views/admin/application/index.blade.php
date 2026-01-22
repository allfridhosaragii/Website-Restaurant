@extends('layouts.admin')
@section('title', 'Pengaturan Aplikasi')
@push('styles')
<style>
:root {
    --premium-gradient: linear-gradient(135deg, #0c2a36 0%, #16252b 100%);
    --accent-gradient: linear-gradient(135deg, #c89b3a 0%, #f0d78c 100%);
    --card-bg: rgba(255, 255, 255, 0.85);
    --card-border: rgba(12, 42, 54, 0.08);
    --glass-bg: rgba(255, 255, 255, 0.7);
}
[data-theme="dark"] {
    --card-bg: rgba(22, 37, 43, 0.85);
    --card-border: rgba(255, 255, 255, 0.08);
    --glass-bg: rgba(12, 42, 54, 0.6);
}
.app-settings-header {
    margin-bottom: 2.5rem;
}
.app-settings-header h1 {
    font-weight: 800;
    letter-spacing: -0.025em;
    background: var(--accent-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.app-card {
    background: var(--card-bg);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid var(--card-border);
    border-radius: 1.5rem;
    box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.app-card-header {
    padding: 1.75rem;
    border-bottom: 1px solid var(--card-border);
    background: rgba(12, 42, 54, 0.02);
}
.app-card-header h3 {
    font-weight: 700;
    margin: 0;
    font-size: 1.25rem;
}
.app-card-header p {
    margin: 0.5rem 0 0;
    color: var(--text-muted);
    font-size: 0.9rem;
}
.app-card-body {
    padding: 1.75rem;
}
.app-upload-zone {
    border: 2px dashed var(--card-border);
    border-radius: 1.25rem;
    padding: 3rem 2rem;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    background: var(--glass-bg);
    position: relative;
    overflow: hidden;
}
.app-upload-zone::before {
    content: '';
    position: absolute;
    inset: 0;
    background: var(--accent-gradient);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 0;
}
.app-upload-zone:hover {
    border-color: var(--accent);
    transform: translateY(-2px);
}
.app-upload-zone.dragover {
    background: rgba(200, 155, 58, 0.05);
    border-color: var(--accent);
}
.app-upload-zone * {
    position: relative;
    z-index: 1;
}
.app-upload-zone i {
    font-size: 3.5rem;
    background: var(--accent-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 1.5rem;
    display: inline-block;
}
.history-table {
    border-collapse: separate;
    border-spacing: 0 0.5rem;
}
.history-table thead th {
    border: none;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    font-weight: 700;
    color: var(--text-muted);
    padding: 1rem 1.5rem;
}
.history-table tbody tr {
    transition: all 0.2s ease;
    background: rgba(0, 0, 0, 0.02);
}
[data-theme="dark"] .history-table tbody tr {
    background: rgba(255, 255, 255, 0.02);
}
.history-table tbody td {
    border: none;
    padding: 1rem 1.5rem;
    vertical-align: middle;
}
.history-table tbody tr td:first-child { border-top-left-radius: 1rem; border-bottom-left-radius: 1rem; }
.history-table tbody tr td:last-child { border-top-right-radius: 1rem; border-bottom-right-radius: 1rem; }
.history-item-name {
    font-weight: 600;
    color: var(--text-primary);
}
.history-item-meta {
    font-size: 0.8rem;
    color: var(--text-muted);
}
.empty-history {
    padding: 4rem 2rem;
    text-align: center;
    color: var(--text-muted);
}
.empty-history i {
    font-size: 3rem;
    opacity: 0.3;
    margin-bottom: 1rem;
    display: block;
}
.progress {
    background-color: rgba(0, 0, 0, 0.05) !important;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
    border-radius: 10px;
    height: 12px;
}
.progress-bar {
    background: var(--accent-gradient);
    box-shadow: 0 0 15px rgba(200, 155, 58, 0.3);
}
.btn-primary {
    background: var(--accent-gradient);
    border: none;
    font-weight: 700;
    padding: 0.8rem 2rem;
    border-radius: 1rem;
    box-shadow: 0 4px 15px rgba(200, 155, 58, 0.2);
    transition: all 0.3s ease;
}
.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(200, 155, 58, 0.3);
    background: var(--accent-gradient);
}
    /* Live Activity Styles */
    .live-dot {
        width: 8px;
        height: 8px;
        background: #10B981;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        box-shadow: 0 0 10px #10B981;
        animation: livePulse 2s infinite;
    }
    @keyframes livePulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.5); opacity: 0.5; }
        100% { transform: scale(1); opacity: 1; }
    }
    .user-tag {
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 4px;
        background: rgba(200, 155, 58, 0.1);
        color: var(--accent);
        font-weight: 600;
    }
    .activity-row {
        animation: slideIn 0.3s ease-out;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
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
                <h3><i class="bi bi-cloud-upload"></i> APK Deployment Center</h3>
                <p>Deploy pembaruan APK langsung ke CDN dengan akses instan 🚀</p>
            </div>
            <div class="app-card-body">
                @if($currentApk)
                <div class="app-current-file mb-4" style="background: rgba(25, 135, 84, 0.05); border: 1px solid rgba(25, 135, 84, 0.15);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 rounded-circle" style="background: rgba(25, 135, 84, 0.1);">
                            <i class="bi bi-file-earmark-check" style="font-size: 2rem; color: #198754;"></i>
                        </div>
                        <div class="grow">
                            <h6 class="mb-1 fw-bold">{{ $currentApk['name'] }}</h6>
                            <p class="mb-0 text-muted small">{{ $currentApk['size'] }} • Versi saat ini di-deploy pada {{ $currentApk['date'] }}</p>
                        </div>
                        <a href="{{ $currentApk['url'] }}" class="btn btn-sm btn-outline-success px-3" target="_blank">
                            <i class="bi bi-cloud-download me-1"></i> Verifikasi
                        </a>
                    </div>
                </div>
                @endif
                <div class="app-form-group">
                    <label class="app-form-label mb-3">Pilar Utama Aplikasi (File APK)</label>
                    <div class="app-upload-zone" id="uploadZone" onclick="document.getElementById('apkFile').click()">
                        <div class="py-4">
                            <i class="bi bi-cloud-arrow-up"></i>
                            <h5 class="fw-bold">Unggah Versi Baru</h5>
                            <p class="text-muted">Seret file APK ke sini atau klik untuk menjelajah</p>
                            <span class="badge rounded-pill bg-light text-dark mt-3 px-3 py-2 border">Maksimal 200MB</span>
                        </div>
                    </div>
                    <input type="file" name="apk_file" id="apkFile" accept=".apk" style="display: none;">
                    <div id="filePreview" style="display: none;" class="mt-4">
                        <div class="p-3 rounded-3 border d-flex align-items-center gap-3 bg-light bg-opacity-10">
                            <i class="bi bi-file-earmark-zip fw-bold text-accent" style="font-size: 1.5rem;"></i>
                            <div>
                                <div id="fileName" class="fw-bold"></div>
                                <div id="fileSize" class="small text-muted"></div>
                            </div>
                            <div class="ms-auto">
                                <span class="badge bg-success">Siap Upload</span>
                            </div>
                        </div>
                    </div>
                    <div id="uploadProgressContainer" style="display: none;" class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold" id="uploadStatusText">Mempersiapkan...</span>
                            <span class="fw-extrabold text-accent" id="uploadPercentage">0%</span>
                        </div>
                        <div class="progress">
                            <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-save-bar border-0 bg-transparent px-4 pb-4">
                <button type="button" id="btnUpload" class="btn btn-primary btn-lg w-100 py-3 shadow-lg">
                    <i class="bi bi-lightning-fill me-2"></i>Luncurkan Pembaruan
                </button>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="app-card">
            <div class="app-card-header">
                <h3 class="mb-0">Riwayat Penempatan</h3>
                <p>Log pembaruan sistem</p>
            </div>
            <div class="app-card-body p-0">
                <div class="table-responsive">
                    <table class="table history-table mb-0">
                        <thead>
                            <tr>
                                <th>Arsip APK</th>
                                <th class="text-end">Rincian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $item)
                            <tr>
                                <td>
                                    <div class="history-item-name">{{ $item['name'] }}</div>
                                    <div class="history-item-meta">{{ $item['date'] }}</div>
                                </td>
                                <td class="text-end">
                                    <span class="badge rounded-pill bg-light text-dark border">{{ $item['size'] }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2">
                                    <div class="empty-history">
                                        <i class="bi bi-journal-x"></i>
                                        <p>Belum ada rekaman pembaruan</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="app-card mt-4">
            <div class="app-card-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0"><span class="live-dot"></span> Real-time Downloads</h3>
                    <p>Aktivitas pengunduhan saat ini</p>
                </div>
                <div id="downloadBadge" class="badge rounded-pill bg-accent-light text-accent">Checking...</div>
            </div>
            <div class="app-card-body p-0">
                <div class="table-responsive">
                    <table class="table history-table mb-0">
                        <thead>
                            <tr>
                                <th>Pengguna</th>
                                <th>File / IP</th>
                                <th>Perangkat</th>
                                <th class="text-end">Waktu</th>
                            </tr>
                        </thead>
                        <tbody id="liveDownloadBody">
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="spinner-border text-accent spinner-border-sm me-2"></div>
                                    Memuat aktivitas terbaru...
                                </td>
                            </tr>
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
// Live Tracking Logic
function timeAgo(dateStr) {
    const date = new Date(dateStr);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);
    if (diff < 60) return diff + ' detik lalu';
    if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
    if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
    return Math.floor(diff / 86400) + ' hari lalu';
}
async function fetchLiveDownloads() {
    try {
        const res = await fetch('/admin/application/api/downloads');
        const data = await res.json();
        if (data.success) {
            const tbody = document.getElementById('liveDownloadBody');
            const badge = document.getElementById('downloadBadge');
            badge.textContent = data.data.length + ' log';
            if (data.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-5 text-muted">Belum ada aktivitas unduhan</td></tr>';
                return;
            }
            
            function getDeviceIcon(type) {
                switch(type?.toLowerCase()) {
                    case 'mobile': return 'bi-phone';
                    case 'tablet': return 'bi-tablet';
                    case 'desktop': return 'bi-laptop';
                    default: return 'bi-device-hdd';
                }
            }
            
            tbody.innerHTML = data.data.map(a => `
                <tr class="activity-row">
                    <td>
                        <div class="fw-bold">${a.user_name || 'Guest'}</div>
                        <div class="small text-muted">${a.user_email || 'Tidak ada email'}</div>
                    </td>
                    <td>
                        <div class="small fw-semibold">${a.description.replace('Mendownload file: ', '')}</div>
                        <code class="small text-accent">${a.ip_address}</code>
                    </td>
                    <td>
                        <div class="small">
                            <i class="bi ${getDeviceIcon(a.device_type)} me-1"></i>
                            ${a.device_name || 'Unknown'}
                        </div>
                        <div class="small text-muted">${a.browser || ''} ${a.os ? '• ' + a.os : ''}</div>
                    </td>
                    <td class="text-end">
                        <span class="text-muted small">${timeAgo(a.created_at)}</span>
                    </td>
                </tr>
            `).join('');
        }
    } catch (err) {
        console.error('Failed to fetch live downloads:', err);
    }
}
// Initial pull and interval
fetchLiveDownloads();
setInterval(fetchLiveDownloads, 5000);
</script>
@endpush