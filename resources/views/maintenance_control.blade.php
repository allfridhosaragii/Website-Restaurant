@extends('layouts.guest')
@section('title', 'Maintenance Control')
@section('content')
<section class="min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #0B0E10 0%, #1a1f25 100%); padding-top: 100px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(200,155,58,0.15); border: 2px solid rgba(200,155,58,0.3); border-radius: 50%;">
                        <i class="bi bi-shield-lock-fill" style="font-size: 2.5rem; color: #D4AF37;"></i>
                    </div>
                    <h1 class="text-white fw-bold mb-2">Maintenance Control</h1>
                    <p class="text-white-50">Secret admin panel - Only for <strong class="text-warning">{{ auth()->user()->email }}</strong></p>
                </div>
                <div class="row g-4">
                    <div class="col-md-5">
                        <div class="card border-0 h-100" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(200,155,58,0.2) !important;">
                            <div class="card-body p-4">
                                <h5 class="text-white mb-4"><i class="bi bi-gear-fill me-2 text-warning"></i>Control Panel</h5>
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="background: rgba(25,135,84,0.2); border: 1px solid rgba(25,135,84,0.3); color: #75b798;">
                                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                <div class="p-4 rounded-3 mb-4" style="background: rgba(0,0,0,0.3);">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div>
                                            <h6 class="text-white mb-1">Maintenance Mode</h6>
                                            <small class="text-white-50">Toggle to enable/disable</small>
                                        </div>
                                        <span class="badge {{ $isMaintenanceMode ? 'bg-danger' : 'bg-success' }} px-3 py-2">
                                            {{ $isMaintenanceMode ? 'ACTIVE' : 'INACTIVE' }}
                                        </span>
                                    </div>
                                    <form action="{{ url('/maintenance/toggle') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-lg w-100 {{ $isMaintenanceMode ? 'btn-danger' : 'btn-success' }}" id="toggleBtn">
                                            @if($isMaintenanceMode)
                                                <i class="bi bi-toggle-on me-2"></i> Turn OFF Maintenance
                                            @else
                                                <i class="bi bi-toggle-off me-2"></i> Turn ON Maintenance
                                            @endif
                                        </button>
                                    </form>
                                </div>
                                @if($isMaintenanceMode && $maintenanceStartTime)
                                <div class="p-3 rounded-3 mb-4" style="background: rgba(220,53,69,0.15); border: 1px solid rgba(220,53,69,0.3);">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-clock-history text-danger me-2"></i>
                                        <small class="text-white-50">Maintenance Duration</small>
                                    </div>
                                    <div id="durationCounter" class="d-flex gap-2 justify-content-center">
                                        <div class="text-center px-2">
                                            <div class="fs-4 fw-bold text-danger" id="days">00</div>
                                            <small class="text-white-50">Days</small>
                                        </div>
                                        <div class="text-danger fs-4">:</div>
                                        <div class="text-center px-2">
                                            <div class="fs-4 fw-bold text-danger" id="hours">00</div>
                                            <small class="text-white-50">Hours</small>
                                        </div>
                                        <div class="text-danger fs-4">:</div>
                                        <div class="text-center px-2">
                                            <div class="fs-4 fw-bold text-danger" id="minutes">00</div>
                                            <small class="text-white-50">Mins</small>
                                        </div>
                                        <div class="text-danger fs-4">:</div>
                                        <div class="text-center px-2">
                                            <div class="fs-4 fw-bold text-danger" id="seconds">00</div>
                                            <small class="text-white-50">Secs</small>
                                        </div>
                                    </div>
                                    <div class="text-center mt-2">
                                        <small class="text-white-50">Started: {{ \Carbon\Carbon::parse($maintenanceStartTime)->timezone('Asia/Jakarta')->format('d M Y, H:i:s') }} WIB</small>
                                    </div>
                                </div>
                                <script>
                                    const startTime = new Date('{{ $maintenanceStartTime }}');
                                    function updateDuration() {
                                        const now = new Date();
                                        const diff = Math.floor((now - startTime) / 1000);
                                        const days = Math.floor(diff / 86400);
                                        const hours = Math.floor((diff % 86400) / 3600);
                                        const minutes = Math.floor((diff % 3600) / 60);
                                        const seconds = diff % 60;
                                        document.getElementById('days').textContent = String(days).padStart(2, '0');
                                        document.getElementById('hours').textContent = String(hours).padStart(2, '0');
                                        document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
                                        document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
                                    }
                                    updateDuration();
                                    setInterval(updateDuration, 1000);
                                </script>
                                @endif
                                <div class="p-3 rounded-3" style="background: rgba(200,155,58,0.1); border: 1px solid rgba(200,155,58,0.3);">
                                    <small class="text-white-50">
                                        <i class="bi bi-info-circle text-warning me-2"></i>
                                        When ON, all pages except <code>/project</code> will show maintenance page.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card border-0 h-100" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(200,155,58,0.2) !important;">
                            <div class="card-body p-4">
                                <h5 class="text-white mb-4">
                                    <i class="bi bi-eye-fill me-2 text-warning"></i>Live Preview
                                    <span class="badge {{ $isMaintenanceMode ? 'bg-danger' : 'bg-success' }} ms-2">{{ $isMaintenanceMode ? 'Maintenance' : 'Normal' }}</span>
                                </h5>
                                <div class="preview-container rounded-3 overflow-hidden position-relative" style="border: 2px solid rgba(200,155,58,0.2); height: 400px; background: #0B0E10;">
                                    <div class="d-flex align-items-center px-3 py-2" style="background: rgba(0,0,0,0.5); border-bottom: 1px solid rgba(200,155,58,0.2);">
                                        <div class="d-flex gap-1 me-3">
                                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #ff5f57;"></span>
                                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #febc2e;"></span>
                                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #28c840;"></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="px-3 py-1 rounded" style="background: rgba(255,255,255,0.1); font-size: 0.7rem; color: rgba(255,255,255,0.5);">
                                                <i class="bi bi-lock-fill me-1"></i> {{ $isMaintenanceMode ? url('/') . ' (maintenance view)' : url('/') }}
                                            </div>
                                        </div>
                                    </div>
                                    <iframe 
                                        src="{{ $isMaintenanceMode ? url('/maintenance/preview') : url('/') }}" 
                                        style="width: 166.67%; height: 166.67%; border: none; transform: scale(0.6); transform-origin: top left;"
                                        loading="lazy"
                                    ></iframe>
                                </div>
                                <div class="mt-3 d-flex justify-content-between align-items-center">
                                    <small class="text-white-50">
                                        <i class="bi bi-arrow-repeat me-1"></i> Preview updates after toggle
                                    </small>
                                    <a href="{{ $isMaintenanceMode ? url('/maintenance/preview') : url('/') }}" target="_blank" class="btn btn-sm" style="background: rgba(200,155,58,0.15); color: #D4AF37; border: 1px solid rgba(200,155,58,0.2);">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Open in New Tab
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <small class="text-white-50">
                        <i class="bi bi-shield-check me-1"></i> 
                        This page is only accessible by the super admin account
                    </small>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-0" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(200,155,58,0.2) !important;">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="text-white mb-0">
                                        <i class="bi bi-people-fill me-2 text-warning"></i>Visitor History
                                    </h5>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge bg-success" id="active-count">0 Active</span>
                                        <span class="badge bg-secondary" id="total-today">0 Today</span>
                                        <i class="bi bi-arrow-repeat text-white-50 spinning" id="refresh-indicator" style="display: none;"></i>
                                    </div>
                                </div>
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-dark table-hover mb-0" style="background: transparent;">
                                        <thead style="position: sticky; top: 0; background: #1a1f25; z-index: 10;">
                                            <tr class="text-warning" style="border-bottom: 1px solid rgba(200,155,58,0.3);">
                                                <th><i class="bi bi-hash"></i></th>
                                                <th><i class="bi bi-link-45deg me-1"></i>Page</th>
                                                <th><i class="bi bi-geo-alt me-1"></i>IP</th>
                                                <th><i class="bi bi-browser-chrome me-1"></i>Browser</th>
                                                <th><i class="bi bi-phone me-1"></i>Device</th>
                                                <th><i class="bi bi-windows me-1"></i>OS</th>
                                                <th><i class="bi bi-clock me-1"></i>Entry</th>
                                                <th><i class="bi bi-stopwatch me-1"></i>Duration</th>
                                                <th><i class="bi bi-circle-fill me-1"></i>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="visitors-table-body">
                                            <tr>
                                                <td colspan="9" class="text-center text-white-50 py-4">
                                                    <i class="bi bi-hourglass-split me-2"></i>Loading visitors...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3 text-center">
                                    <small class="text-white-50">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Shows all visitor history. Data resets when maintenance mode is turned OFF.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action*="/maintenance/toggle"]');
    const toggleBtn = document.getElementById('toggleBtn');
    const statusBadge = document.querySelector('.badge.bg-danger, .badge.bg-success');
    const previewBadge = document.querySelector('.col-md-7 .badge');
    const iframe = document.querySelector('iframe');
    const durationSection = document.getElementById('durationCounter')?.closest('.p-3.rounded-3.mb-4');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        toggleBtn.disabled = true;
        toggleBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({})
            });
            const data = await response.json();
            if (data.success) {
                // Update status badge
                if (statusBadge) {
                    statusBadge.className = `badge ${data.isMaintenanceMode ? 'bg-danger' : 'bg-success'} px-3 py-2`;
                    statusBadge.textContent = data.isMaintenanceMode ? 'ACTIVE' : 'INACTIVE';
                }
                // Update toggle button
                toggleBtn.className = `btn btn-lg w-100 ${data.isMaintenanceMode ? 'btn-danger' : 'btn-success'}`;
                toggleBtn.innerHTML = data.isMaintenanceMode 
                    ? '<i class="bi bi-toggle-on me-2"></i> Turn OFF Maintenance'
                    : '<i class="bi bi-toggle-off me-2"></i> Turn ON Maintenance';
                // Update preview badge
                if (previewBadge) {
                    previewBadge.className = `badge ${data.isMaintenanceMode ? 'bg-danger' : 'bg-success'} ms-2`;
                    previewBadge.textContent = data.isMaintenanceMode ? 'Maintenance' : 'Normal';
                }
                // Update iframe
                if (iframe) {
                    iframe.src = data.isMaintenanceMode 
                        ? '{{ url("/maintenance/preview") }}'
                        : '{{ url("/") }}';
                }
                // Show success toast
                showToast(data.message, 'success');
                // Handle duration counter - reload page to show/hide it properly
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error toggling maintenance mode', 'danger');
            toggleBtn.disabled = false;
            toggleBtn.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i> Error - Try Again';
        }
    });
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} position-fixed`;
        toast.style.cssText = 'top: 100px; right: 20px; z-index: 9999; animation: fadeIn 0.3s;';
        toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
});
</script>
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.spinning {
    animation: spin 1s linear infinite;
}
</style>
<script>
// Visitor Tracking - Always runs
(function() {
    let visitorData = [];
    let lastFetchTime = Date.now();
    function formatDuration(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    }
    function updateDurations() {
        const elapsed = Math.floor((Date.now() - lastFetchTime) / 1000);
        const durationCells = document.querySelectorAll('.duration-cell[data-active="true"]');
        durationCells.forEach((cell, index) => {
            const baseDuration = parseInt(cell.dataset.baseDuration) || 0;
            cell.textContent = formatDuration(baseDuration + elapsed);
        });
    }
    async function fetchVisitors() {
        const indicator = document.getElementById('refresh-indicator');
        if (indicator) indicator.style.display = 'inline-block';
        try {
            const response = await fetch('/api/site-visitors-history');
            const data = await response.json();
            visitorData = data.visitors || [];
            lastFetchTime = Date.now();
            // Update counts
            document.getElementById('active-count').textContent = data.active_count + ' Active';
            document.getElementById('total-today').textContent = data.total_today + ' Today';
            // Update table
            const tbody = document.getElementById('visitors-table-body');
            if (visitorData.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center text-white-50 py-4">
                            <i class="bi bi-emoji-smile me-2"></i>No visitors recorded yet
                        </td>
                    </tr>
                `;
            } else {
                tbody.innerHTML = visitorData.map((v, i) => `
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); ${v.is_active ? '' : 'opacity: 0.6;'}">
                        <td class="text-white-50">${i + 1}</td>
                        <td><code class="text-info">${v.page_url || '-'}</code></td>
                        <td><code class="text-warning">${v.ip || '-'}</code></td>
                        <td class="text-white">${v.browser || '-'}</td>
                        <td>
                            <span class="badge ${v.device === 'Mobile' ? 'bg-info' : v.device === 'Tablet' ? 'bg-primary' : 'bg-secondary'}">
                                <i class="bi bi-${v.device === 'Mobile' ? 'phone' : v.device === 'Tablet' ? 'tablet' : 'laptop'} me-1"></i>${v.device || '-'}
                            </span>
                        </td>
                        <td class="text-white-50">${v.os || '-'}</td>
                        <td class="text-success">${v.entry_time || '-'}</td>
                        <td class="text-warning fw-bold duration-cell" data-active="${v.is_active}" data-base-duration="${v.duration_seconds || 0}">${formatDuration(v.duration_seconds || 0)}</td>
                        <td>
                            ${v.is_active 
                                ? '<span class="badge bg-success"><i class="bi bi-circle-fill me-1"></i>Online</span>' 
                                : '<span class="badge bg-secondary"><i class="bi bi-circle me-1"></i>Left</span>'}
                        </td>
                    </tr>
                `).join('');
            }
        } catch (error) {
            console.log('Error fetching visitors:', error);
        } finally {
            if (indicator) indicator.style.display = 'none';
        }
    }
    // Initial fetch
    fetchVisitors();
    // Refresh data every 5 seconds
    setInterval(fetchVisitors, 5000);
    // Update duration display every 1 second (only for active visitors)
    setInterval(updateDurations, 1000);
})();
</script>
@endsection