@extends('layouts.admin')
@section('title', 'Statistik & Monitoring')

@push('styles')
<style>
    .stat-card {
        background: linear-gradient(135deg, var(--surface) 0%, var(--surface-light) 100%);
        border: 1px solid var(--border-medium);
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .stat-card .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }
    .stat-card .stat-label {
        font-size: 0.85rem;
        opacity: 0.7;
    }
    
    .live-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .live-badge .pulse-dot {
        width: 8px;
        height: 8px;
        background: #ef4444;
        border-radius: 50%;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
    }
    
    .tab-nav {
        display: flex;
        gap: 8px;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--border-light);
        padding-bottom: 8px;
    }
    .tab-btn {
        padding: 10px 20px;
        border: none;
        background: transparent;
        color: var(--text-muted);
        font-weight: 600;
        cursor: pointer;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s;
        position: relative;
    }
    .tab-btn.active {
        color: var(--accent);
        background: rgba(212, 175, 55, 0.1);
    }
    .tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--accent);
    }
    .tab-btn .badge {
        margin-left: 8px;
        font-size: 0.7rem;
    }
    
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }

    .table-responsive-wrapper {
        border-radius: 12px;
        border: 1px solid var(--border-light);
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        background: var(--surface);
    }
    
    .activity-table, .error-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .activity-table th, .error-table th {
        background: var(--surface);
        color: var(--text-primary);
        font-weight: 600;
        padding: 12px 16px;
        text-align: left;
        border-bottom: 2px solid var(--border-medium);
    }
    .activity-table td, .error-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-light);
        vertical-align: top;
    }
    .activity-table tbody tr:hover, .error-table tbody tr:hover {
        background: var(--surface-light);
    }
    
    .user-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .user-badge.admin {
        background: rgba(139, 92, 246, 0.15);
        color: #8b5cf6;
    }
    .user-badge.customer {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
    }
    .user-badge.guest {
        background: rgba(156, 163, 175, 0.15);
        color: #9ca3af;
    }
    
    .error-severity {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    .error-severity.critical {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }
    .error-severity.resolved {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
    }
    
    .error-message {
        font-family: 'Fira Code', monospace;
        font-size: 0.8rem;
        background: var(--bg-secondary);
        padding: 8px 12px;
        border-radius: 6px;
        max-width: 500px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        white-space: normal;
        word-break: break-all;
        line-height: 1.4;
    }
    
    .filter-bar {
        display: flex;
        gap: 12px;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }
    .filter-bar select {
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid var(--border-medium);
        background: var(--surface);
        color: var(--text-primary);
        font-size: 0.85rem;
    }
    
    .refresh-indicator {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    .refresh-indicator i {
        animation: spin 2s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--text-muted);
    }
    .empty-state i {
        font-size: 3rem;
        opacity: 0.3;
        margin-bottom: 1rem;
    }
    
    .btn-resolve {
        padding: 4px 12px;
        font-size: 0.75rem;
        border-radius: 6px;
        border: none;
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: white;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-resolve:hover {
        transform: scale(1.05);
    }
    .btn-resolve:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .btn-copy-md {
        padding: 4px 12px;
        font-size: 0.75rem;
        border-radius: 6px;
        border: 1px solid var(--border-medium);
        background: var(--surface);
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 5px;
    }
    .btn-copy-md:hover {
        background: var(--surface-light);
        border-color: var(--accent);
        color: var(--accent);
    }
    .btn-copy-md.copied {
        background: #22c55e;
        color: white;
        border-color: #22c55e;
    }
    
    .screenshot-thumb {
        width: 60px;
        height: 40px;
        border-radius: 6px;
        object-fit: cover;
        cursor: pointer;
        border: 2px solid var(--border-medium);
        transition: all 0.2s;
    }
    .screenshot-thumb:hover {
        transform: scale(1.1);
        border-color: var(--accent);
    }
    
    .browser-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }
    
    .file-location {
        font-family: 'Fira Code', monospace;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 4px;
        background: rgba(139, 92, 246, 0.1);
        color: #8b5cf6;
        cursor: pointer;
        display: inline-block;
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        transition: all 0.2s;
    }
    .file-location:hover {
        background: rgba(139, 92, 246, 0.2);
        max-width: none;
        position: relative;
        z-index: 5;
    }

    .markdown-code-block {
        font-family: 'Fira Code', monospace;
        font-size: 0.7rem;
        background: #000;
        color: #0f0;
        padding: 8px;
        border-radius: 6px;
        max-height: 120px;
        max-width: 300px;
        overflow: auto;
        white-space: pre-wrap;
        word-break: break-all;
        border: 1px solid #333;
    }
    .markdown-code-block::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }
    .markdown-code-block::-webkit-scrollbar-thumb {
        background: #444;
        border-radius: 10px;
    }
    
    /* Screenshot Modal */
    .screenshot-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.85);
        z-index: 10000;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    .screenshot-modal.active {
        display: flex;
    }
    .screenshot-modal img {
        max-width: 90%;
        max-height: 90%;
        border-radius: 12px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.5);
    }
    .screenshot-modal .close-btn {
        position: absolute;
        top: 20px;
        right: 30px;
        font-size: 2rem;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
    }
    .screenshot-modal .close-btn:hover {
        transform: scale(1.2);
    }
    .screenshot-modal .error-details {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 12px 24px;
        border-radius: 10px;
        max-width: 80%;
        text-align: center;
    }

    /* History Modal Styles */
    .history-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(5px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1050;
    }
    .history-modal.active {
        display: flex;
    }
    .history-container {
        background: var(--surface);
        width: 100%;
        max-width: 600px;
        max-height: 80vh;
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        flex-column: column;
        box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        border: 1px solid var(--border-light);
    }
    .history-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--surface-light);
    }
    .history-body {
        padding: 24px;
        overflow-y: auto;
        flex-grow: 1;
    }
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 5px;
        bottom: 5px;
        width: 2px;
        background: var(--border-light);
    }
    .timeline-item {
        position: relative;
        margin-bottom: 24px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -24px;
        top: 6px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--accent);
        border: 2px solid var(--surface);
        z-index: 2;
    }
    .timeline-item.visit::before {
        background: var(--success);
    }
    .timeline-time {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-bottom: 4px;
    }
    .timeline-content {
        background: var(--surface-light);
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid var(--border-light);
    }
    .timeline-action {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 2px;
    }
    .timeline-desc {
        font-size: 0.8rem;
        color: var(--text-muted);
    }
</style>
@endpush

@section('content')
<section class="section bg-cream">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1">
                    <i class="bi bi-graph-up-arrow text-primary me-2"></i>Statistik & Monitoring
                </h3>
                <p class="text-muted mb-0">Real-time activity tracking dan error logging</p>
            </div>
            <div class="refresh-indicator" id="refreshIndicator">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Auto-refresh aktif</span>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-activity"></i>
                        </div>
                        <div>
                            <div class="stat-value text-primary" id="statTodayActivities">{{ $todayActivities }}</div>
                            <div class="stat-label">Aktivitas Hari Ini</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-eye"></i>
                        </div>
                        <div>
                            <div class="stat-value text-success d-flex align-items-center gap-2" id="statLiveVisitors">
                                {{ $liveVisitors }}
                                <span class="live-badge">
                                    <span class="pulse-dot"></span>
                                    LIVE
                                </span>
                            </div>
                            <div class="stat-label">Pengunjung Online</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-bug"></i>
                        </div>
                        <div>
                            <div class="stat-value text-danger" id="statUnresolvedErrors">{{ $unresolvedErrors }}</div>
                            <div class="stat-label">Error Belum Diperbaiki</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <div class="stat-value text-info" id="statTotalActivities">{{ $totalActivities }}</div>
                            <div class="stat-label">Total Aktivitas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="card">
            <div class="card-body">
                <div class="tab-nav">
                    <button class="tab-btn active" data-tab="activities">
                        <i class="bi bi-person-lines-fill me-1"></i>Aktivitas
                        <span class="badge bg-primary" id="badgeActivities">0</span>
                    </button>
                    <button class="tab-btn" data-tab="errors">
                        <i class="bi bi-bug me-1"></i>Error Logs
                        <span class="badge bg-danger" id="badgeErrors">{{ $unresolvedErrors }}</span>
                    </button>
                    <button class="tab-btn" data-tab="visitors">
                        <i class="bi bi-eye me-1"></i>Live Visitors
                        <span class="badge bg-success" id="badgeVisitors">{{ $liveVisitors }}</span>
                    </button>
                </div>

                {{-- Activities Tab --}}
                <div class="tab-content active" id="tab-activities">
                    <div class="filter-bar">
                        <select id="filterActivityType">
                            <option value="all">Semua User</option>
                            <option value="admin">Admin Saja</option>
                            <option value="customer">Customer Saja</option>
                        </select>
                        <select id="filterActivityDate">
                            <option value="all">Semua Waktu</option>
                            <option value="today">Hari Ini</option>
                            <option value="week">7 Hari Terakhir</option>
                        </select>
                    </div>
                    <div class="table-responsive-wrapper">
                        <table class="activity-table" style="min-width: 800px;">
                            <thead>
                                <tr>
                                    <th style="width: 120px;">Waktu</th>
                                    <th style="width: 150px;">User</th>
                                    <th style="width: 150px;">Aksi</th>
                                    <th>Detail</th>
                                    <th style="width: 120px;">IP</th>
                                </tr>
                            </thead>
                            <tbody id="activityTableBody">
                                <tr>
                                    <td colspan="5" class="empty-state">
                                        <i class="bi bi-hourglass-split d-block"></i>
                                        Memuat data...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Errors Tab --}}
                <div class="tab-content" id="tab-errors">
                    <div class="filter-bar">
                        <select id="filterErrorStatus">
                            <option value="all">Semua Status</option>
                            <option value="unresolved">Belum Diperbaiki</option>
                            <option value="resolved">Sudah Diperbaiki</option>
                        </select>
                        <select id="filterErrorDate">
                            <option value="all">Semua Waktu</option>
                            <option value="today">Hari Ini</option>
                            <option value="week">7 Hari Terakhir</option>
                        </select>
                    </div>
                    <div class="table-responsive-wrapper">
                        <table class="error-table" style="min-width: 1400px;">
                            <thead>
                                <tr>
                                    <th style="width: 120px;">Waktu</th>
                                    <th style="width: 80px;">Screenshot</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 120px;">Tipe</th>
                                    <th style="min-width: 250px;">Pesan</th>
                                    <th style="width: 180px;">Lokasi</th>
                                    <th style="width: 150px;">Browser</th>
                                    <th style="width: 320px;">Markdown Code</th>
                                    <th style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="errorTableBody">
                                <tr>
                                    <td colspan="8" class="empty-state">
                                        <i class="bi bi-hourglass-split d-block"></i>
                                        Memuat data...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Visitors Tab --}}
                <div class="tab-content" id="tab-visitors">
                    <div class="table-responsive-wrapper">
                        <table class="activity-table" style="min-width: 800px;">
                            <thead>
                                <tr>
                                    <th>Halaman</th>
                                    <th style="width: 150px;">Browser</th>
                                    <th style="width: 150px;">Device</th>
                                    <th style="width: 150px;">Masuk Sejak</th>
                                </tr>
                            </thead>
                            <tbody id="visitorTableBody">
                                <tr>
                                    <td colspan="4" class="empty-state">
                                        <i class="bi bi-hourglass-split d-block"></i>
                                        Memuat data...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Screenshot Modal -->
    <div class="screenshot-modal" id="screenshotModal" onclick="closeScreenshotModal()">
        <span class="close-btn" onclick="closeScreenshotModal()">&times;</span>
        <img id="screenshotImage" src="" alt="Error Screenshot">
        <div class="error-details" id="screenshotDetails"></div>
    </div>

    <!-- History Modal -->
    <div class="history-modal" id="historyModal" onclick="closeHistoryModal()">
        <div class="history-container" onclick="event.stopPropagation()">
            <div class="history-header">
                <div>
                    <h5 class="mb-0" id="historyTitle">User History</h5>
                    <small class="text-muted" id="historySubtitle"></small>
                </div>
                <button class="btn-close" onclick="closeHistoryModal()"></button>
            </div>
            <div class="history-body" id="historyBody">
                <!-- Timeline will be injected here -->
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('tab-' + this.dataset.tab).classList.add('active');
        });
    });

    // Format time
    function formatTime(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleString('id-ID', { 
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    }

    // Format time ago
    function timeAgo(dateStr) {
        const date = new Date(dateStr);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);
        if (diff < 60) return diff + ' detik lalu';
        if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
        if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
        return Math.floor(diff / 86400) + ' hari lalu';
    }

    // Load activities
    async function loadActivities() {
        const type = document.getElementById('filterActivityType').value;
        const date = document.getElementById('filterActivityDate').value;
        
        try {
            const res = await fetch(`/admin/statistik/api/activities?type=${type}&date=${date}`);
            const data = await res.json();
            
            const tbody = document.getElementById('activityTableBody');
            document.getElementById('badgeActivities').textContent = data.count;
            
            if (data.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="empty-state">
                    <i class="bi bi-inbox d-block"></i>Tidak ada aktivitas</td></tr>`;
                return;
            }
            
            tbody.innerHTML = data.data.map(a => `
                <tr>
                    <td><small class="text-muted">${timeAgo(a.created_at)}</small></td>
                    <td>
                        <span class="user-badge ${a.is_admin ? 'admin' : (a.user_name ? 'customer' : 'guest')}"
                              onclick="showUserHistory('${a.user_id || ''}', '${a.ip_address || ''}', '${a.user_name || 'Guest'}')">
                            <i class="bi bi-${a.is_admin ? 'shield-check' : (a.user_name ? 'person' : 'person-dash')}"></i>
                            ${a.user_name || 'Guest'}
                        </span>
                    </td>
                    <td><strong>${a.action}</strong></td>
                    <td><small>${a.description || '-'}</small></td>
                    <td><code class="small">${a.ip_address || '-'}</code></td>
                </tr>
            `).join('');
        } catch (e) {
            console.error('Failed to load activities:', e);
        }
    }

    // Global store for error data to avoid quote issues in HTML attributes
    window.errorStore = {};

    // Load errors
    async function loadErrors() {
        const status = document.getElementById('filterErrorStatus').value;
        const date = document.getElementById('filterErrorDate').value;
        
        try {
            const res = await fetch(`/admin/statistik/api/errors?status=${status}&date=${date}`);
            const data = await res.json();
            
            const tbody = document.getElementById('errorTableBody');
            document.getElementById('badgeErrors').textContent = data.data.filter(e => !e.is_resolved).length;
            document.getElementById('statUnresolvedErrors').textContent = data.data.filter(e => !e.is_resolved).length;
            
            if (data.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="empty-state">
                    <i class="bi bi-check-circle d-block text-success"></i>Tidak ada error tercatat. Bagus!</td></tr>`;
                return;
            }
            
            tbody.innerHTML = data.data.map(e => {
                const fileName = e.file ? e.file.split('/').pop().split('\\').pop() : '-';
                const fileLocation = `${fileName}:${e.line || '?'}`;
                
                // Store markdown in memory
                window.errorStore[e.id] = e.markdown;
                
                // Sanitize message for JS attributes (escape quotes and newlines)
                const safeMessage = e.message 
                    ? e.message.replace(/'/g, "\\'").replace(/"/g, "&quot;").replace(/\n/g, "\\n").replace(/\r/g, "\\r")
                    : '';
                
                return `
                <tr>
                    <td><small class="text-muted">${timeAgo(e.created_at)}</small></td>
                    <td>
                        ${e.screenshot_url 
                            ? `<img src="${e.screenshot_url}" class="screenshot-thumb" 
                                   onclick="showScreenshot('${e.screenshot_url}', '${safeMessage}', '${fileLocation}')"
                                   alt="Screenshot">`
                            : '<small class="text-muted">-</small>'
                        }
                    </td>
                    <td>
                        <span class="error-severity ${e.is_resolved ? 'resolved' : 'critical'}">
                            ${e.is_resolved ? 'Resolved' : 'Open'}
                        </span>
                    </td>
                    <td><code class="small">${e.type ? e.type.split('\\').pop() : 'Error'}</code></td>
                    <td>
                        <div class="error-message" title="${safeMessage}">${e.message}</div>
                    </td>
                    <td>
                        <span class="file-location" title="${e.file || 'Unknown'}:${e.line || '?'}">
                            ${fileLocation}
                        </span>
                    </td>
                    <td>
                        ${e.browser 
                            ? `<span class="browser-badge"><i class="bi bi-globe"></i>${e.browser}</span>`
                            : '<small class="text-muted">-</small>'
                        }
                        ${e.device_type ? `<br><small class="text-muted">${e.device_type}</small>` : ''}
                    </td>
                    <td>
                        <pre class="markdown-code-block"><code>${e.markdown || ''}</code></pre>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-1">
                            ${e.is_resolved 
                                ? '<small class="text-success"><i class="bi bi-check-circle"></i> Fixed</small>'
                                : `<button class="btn-resolve" onclick="resolveError(${e.id})">
                                    <i class="bi bi-check"></i> Resolve
                                   </button>`
                            }
                            <button class="btn-copy-md" onclick='copyErrorMarkdown(${e.id}, this)'>
                                <i class="bi bi-markdown"></i> Copy MD
                            </button>
                        </div>
                    </td>
                </tr>
            `}).join('');
        } catch (e) {
            console.error('Failed to load errors:', e);
        }
    }

    // Screenshot Modal Functions
    window.showScreenshot = function(url, message, location) {
        document.getElementById('screenshotImage').src = url;
        document.getElementById('screenshotDetails').innerHTML = `
            <strong>${location}</strong><br>
            <small>${message}</small>
        `;
        document.getElementById('screenshotModal').classList.add('active');
    };

    window.closeScreenshotModal = function() {
        document.getElementById('screenshotModal').classList.remove('active');
    };

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeScreenshotModal();
    });

    // Copy Error as Markdown
    window.copyErrorMarkdown = function(id, btn) {
        const markdown = window.errorStore[id];
        if (!markdown) return;
        
        const el = document.createElement('textarea');
        el.value = markdown;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        
        // Visual feedback
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check"></i> Copied!';
        btn.classList.add('copied');
        
        setTimeout(() => {
            btn.innerHTML = originalContent;
            btn.classList.remove('copied');
        }, 2000);
    };

    // User History Functions
    window.showUserHistory = async function(userId, ip, name) {
        const title = document.getElementById('historyTitle');
        const subtitle = document.getElementById('historySubtitle');
        const body = document.getElementById('historyBody');
        const modal = document.getElementById('historyModal');

        title.textContent = `Riwayat: ${name}`;
        subtitle.textContent = ip ? `IP: ${ip}` : `User ID: ${userId}`;
        body.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Mengambil riwayat...</p></div>';
        modal.classList.add('active');

        try {
            const res = await fetch(`/admin/statistik/api/user-history?user_id=${userId}&ip=${ip}`);
            const result = await res.json();

            if (result.success && result.data.length > 0) {
                body.innerHTML = `
                    <div class="timeline">
                        ${result.data.map(item => `
                            <div class="timeline-item ${item.type}">
                                <div class="timeline-time">${formatTime(item.time)}</div>
                                <div class="timeline-content">
                                    <div class="timeline-action">${item.action}</div>
                                    <div class="timeline-desc">${item.description || '-'}</div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else {
                body.innerHTML = '<div class="text-center py-5"><i class="bi bi-info-circle fs-1 text-muted"></i><p class="mt-2">Tidak ada riwayat ditemukan.</p></div>';
            }
        } catch (e) {
            console.error('Failed to load history:', e);
            body.innerHTML = '<div class="text-center py-5 text-danger"><p>Gagal memuat riwayat.</p></div>';
        }
    };

    window.closeHistoryModal = function() {
        document.getElementById('historyModal').classList.remove('active');
    };

    // Load visitors
    async function loadVisitors() {
        try {
            const res = await fetch('/admin/statistik/api/live-visitors');
            const data = await res.json();
            
            const tbody = document.getElementById('visitorTableBody');
            document.getElementById('badgeVisitors').textContent = data.count;
            document.getElementById('statLiveVisitors').innerHTML = `
                ${data.count}
                <span class="live-badge">
                    <span class="pulse-dot"></span>
                    LIVE
                </span>
            `;
            
            if (data.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="empty-state">
                    <i class="bi bi-person-slash d-block"></i>Tidak ada pengunjung aktif saat ini</td></tr>`;
                return;
            }
            
            tbody.innerHTML = data.data.map(v => `
                <tr>
                    <td>
                        <strong>${v.page_title || v.page_url}</strong>
                        <br><small class="text-muted">${v.page_url}</small>
                    </td>
                    <td>${v.browser || '-'}</td>
                    <td>${v.device_type || '-'}</td>
                    <td><small class="text-muted">${timeAgo(v.entry_time)}</small></td>
                </tr>
            `).join('');
        } catch (e) {
            console.error('Failed to load visitors:', e);
        }
    }

    // Resolve error
    window.resolveError = async function(id) {
        if (!confirm('Tandai error ini sudah diperbaiki?')) return;
        
        try {
            const res = await fetch(`/admin/statistik/api/errors/${id}/resolve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                loadErrors();
            }
        } catch (e) {
            console.error('Failed to resolve error:', e);
        }
    };

    // Filter change handlers
    document.getElementById('filterActivityType').addEventListener('change', loadActivities);
    document.getElementById('filterActivityDate').addEventListener('change', loadActivities);
    document.getElementById('filterErrorStatus').addEventListener('change', loadErrors);
    document.getElementById('filterErrorDate').addEventListener('change', loadErrors);

    // Initial load
    loadActivities();
    loadErrors();
    loadVisitors();

    // Auto-refresh every 5 seconds
    setInterval(() => {
        loadActivities();
        loadErrors();
        loadVisitors();
    }, 5000);
});
</script>
@endpush
