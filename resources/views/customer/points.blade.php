@extends('layouts.guest')
@section('content')
<div class="points-page">
    <div class="points-hero">
        <div class="hero-glow"></div>
        <div class="container">
            <div class="hero-content">
                <div class="points-crown">
                    <img src="https://res.cloudinary.com/dh9ysyfit/image/upload/v1766509120/IMG_8021_l1abhj.png" 
                         alt="Reward Crown" 
                         class="crown-icon">
                </div>
                <div class="points-label">Your Reward Points</div>
                <div class="points-value">
                    <span class="points-number">{{ number_format($points, 0, ',', '.') }}</span>
                    <span class="points-suffix">pts</span>
                </div>
                <div class="points-tagline">Terima kasih telah menjadi pelanggan setia kami</div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card order-card">
                <div class="stat-icon">
                    <i class="bi bi-bag-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($orderPoints ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Poin dari {{ $totalOrders ?? 0 }} Order</div>
                </div>
                <div class="stat-rate">
                    <span class="rate-value">1.000</span>
                    <span class="rate-label">pts/order</span>
                </div>
            </div>
            <div class="stat-card reservation-card">
                <div class="stat-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($reservationPoints ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-label">Poin dari {{ $acceptedReservations ?? 0 }} Reservasi</div>
                </div>
                <div class="stat-rate">
                    <span class="rate-value">10.000</span>
                    <span class="rate-label">pts/reservasi</span>
                </div>
            </div>
        </div>
        <div class="earn-section">
            <h3 class="section-title">
                <span class="title-icon"><i class="bi bi-stars"></i></span>
                Cara Mendapatkan Poin
            </h3>
            <div class="earn-grid">
                <div class="earn-item">
                    <div class="earn-step">1</div>
                    <div class="earn-content">
                        <h4>Pesan Makanan</h4>
                        <p>Setiap kali Anda melakukan order dan pembayaran berhasil, dapatkan <strong>1.000 poin</strong> secara otomatis.</p>
                    </div>
                </div>
                <div class="earn-item">
                    <div class="earn-step">2</div>
                    <div class="earn-content">
                        <h4>Reservasi Meja</h4>
                        <p>Setiap reservasi yang dikonfirmasi akan memberikan Anda <strong>10.000 poin</strong> reward.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="history-section">
            <h3 class="section-title">
                <span class="title-icon"><i class="bi bi-clock-history"></i></span>
                Riwayat Poin
            </h3>
            @if($history->count() > 0)
            <div class="history-list">
                @foreach($history as $item)
                <div class="history-item {{ $item['type'] }}">
                    <div class="history-icon">
                        @if($item['type'] === 'order')
                        <i class="bi bi-bag"></i>
                        @else
                        <i class="bi bi-calendar-event"></i>
                        @endif
                    </div>
                    <div class="history-details">
                        <div class="history-title">{{ $item['description'] }}</div>
                        <div class="history-meta">
                            <span class="history-date">
                                <i class="bi bi-calendar3"></i>
                                {{ \Carbon\Carbon::parse($item['date'])->format('d M Y, H:i') }}
                            </span>
                            @if($item['amount'])
                            <span class="history-amount">
                                <i class="bi bi-credit-card"></i>
                                Rp {{ number_format($item['amount'], 0, ',', '.') }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="history-points">
                        <span class="points-badge {{ $item['type'] }}">
                            +{{ number_format($item['points_earned'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h4>Belum Ada Riwayat</h4>
                <p>Mulai pesan makanan atau buat reservasi untuk mendapatkan poin reward!</p>
                <div class="empty-actions">
                    <a href="/menu" class="btn btn-primary">
                        <i class="bi bi-grid me-2"></i>Lihat Menu
                    </a>
                    <a href="/reservation" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-plus me-2"></i>Reservasi
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
<style>
.points-page {
    min-height: 100vh;
    background: var(--bg-primary);
    padding-bottom: 4rem;
}
/* Hero Section */
.points-hero {
    position: relative;
    background: linear-gradient(135deg, #0C2A36 0%, #1a4a5e 50%, #0C2A36 100%);
    padding: 4rem 0 6rem;
    overflow: hidden;
    margin-bottom: -3rem;
}
.hero-glow {
    position: absolute;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(200, 155, 58, 0.15) 0%, transparent 70%);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    pointer-events: none;
}
.hero-content {
    text-align: center;
    position: relative;
    z-index: 2;
}
.points-crown {
    margin-bottom: 1.5rem;
}
.crown-icon {
    width: 100px;
    height: 100px;
    object-fit: contain;
    filter: drop-shadow(0 10px 30px rgba(200, 155, 58, 0.4));
    animation: float 4s ease-in-out infinite;
}
@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-12px) rotate(2deg); }
}
.points-label {
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 3px;
    color: rgba(255, 255, 255, 0.6);
    margin-bottom: 0.5rem;
    font-weight: 500;
}
.points-value {
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}
.points-number {
    font-family: var(--font-heading);
    font-size: 4.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #FFD700 0%, #C89B3A 50%, #FFD700 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 4px 20px rgba(200, 155, 58, 0.3);
    line-height: 1;
}
.points-suffix {
    font-size: 1.5rem;
    color: rgba(255, 255, 255, 0.5);
    font-weight: 400;
}
.points-tagline {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.9rem;
}
/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 3rem;
}
.stat-card {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--accent), var(--accent-light));
    opacity: 0;
    transition: opacity 0.3s ease;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}
.stat-card:hover::before {
    opacity: 1;
}
.stat-card.order-card .stat-icon {
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.15), rgba(25, 135, 84, 0.05));
    color: #198754;
}
.stat-card.reservation-card .stat-icon {
    background: linear-gradient(135deg, rgba(200, 155, 58, 0.2), rgba(200, 155, 58, 0.05));
    color: var(--accent);
}
.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}
.stat-info {
    flex: 1;
}
.stat-value {
    font-family: var(--font-heading);
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.2;
}
.stat-label {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
}
.stat-rate {
    text-align: right;
    padding-left: 1rem;
    border-left: 1px solid var(--border-light);
}
.rate-value {
    display: block;
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--accent);
}
.rate-label {
    font-size: 0.7rem;
    color: var(--text-muted);
}
/* Section Styles */
.section-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-family: var(--font-heading);
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
}
.title-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--accent), var(--accent-hover));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-on-accent);
    font-size: 1rem;
}
/* Earn Section */
.earn-section {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 24px;
    padding: 2rem;
    margin-bottom: 2rem;
}
.earn-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}
.earn-item {
    display: flex;
    gap: 1rem;
    padding: 1.25rem;
    background: var(--bg-primary);
    border-radius: 16px;
    border: 1px solid var(--border-light);
    transition: all 0.3s ease;
}
.earn-item:hover {
    border-color: var(--accent);
    box-shadow: 0 4px 20px rgba(200, 155, 58, 0.1);
}
.earn-step {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, var(--surface), var(--surface-light));
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
    flex-shrink: 0;
}
.earn-content h4 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}
.earn-content p {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin: 0;
    line-height: 1.5;
}
.earn-content strong {
    color: var(--accent);
    font-weight: 600;
}
/* History Section */
.history-section {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 24px;
    padding: 2rem;
}
.history-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.history-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    background: var(--bg-primary);
    border-radius: 14px;
    border: 1px solid var(--border-light);
    transition: all 0.3s ease;
}
.history-item:hover {
    transform: translateX(4px);
    border-color: var(--border-medium);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
}
.history-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.history-item.order .history-icon {
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.15), rgba(25, 135, 84, 0.05));
    color: #198754;
}
.history-item.reservation .history-icon {
    background: linear-gradient(135deg, rgba(200, 155, 58, 0.2), rgba(200, 155, 58, 0.05));
    color: var(--accent);
}
.history-details {
    flex: 1;
    min-width: 0;
}
.history-title {
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}
.history-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}
.history-meta span {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.75rem;
    color: var(--text-muted);
}
.history-meta i {
    font-size: 0.7rem;
}
.history-points {
    flex-shrink: 0;
}
.points-badge {
    display: inline-block;
    padding: 0.4rem 0.85rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.85rem;
}
.points-badge.order {
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.15), rgba(25, 135, 84, 0.08));
    color: #198754;
}
.points-badge.reservation {
    background: linear-gradient(135deg, rgba(200, 155, 58, 0.2), rgba(200, 155, 58, 0.08));
    color: var(--accent);
}
/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
}
.empty-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--bg-primary), var(--border-light));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: var(--text-muted);
}
.empty-state h4 {
    font-family: var(--font-heading);
    font-size: 1.25rem;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}
.empty-state p {
    color: var(--text-muted);
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
    max-width: 300px;
    margin-left: auto;
    margin-right: auto;
}
.empty-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}
/* Responsive */
@media (max-width: 768px) {
    .points-hero {
        padding: 3rem 0 5rem;
    }
    .points-number {
        font-size: 3rem;
    }
    .crown-icon {
        width: 80px;
        height: 80px;
    }
    .stats-grid {
        grid-template-columns: 1fr;
    }
    .earn-grid {
        grid-template-columns: 1fr;
    }
    .stat-card {
        flex-wrap: wrap;
    }
    .stat-rate {
        width: 100%;
        border-left: none;
        border-top: 1px solid var(--border-light);
        padding-left: 0;
        padding-top: 1rem;
        margin-top: 0.5rem;
        text-align: center;
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }
    .history-item {
        flex-wrap: wrap;
    }
    .history-details {
        order: 1;
        width: 100%;
    }
    .history-points {
        order: 0;
    }
    .history-meta {
        margin-top: 0.5rem;
    }
}
/* Dark Mode Support */
[data-theme="dark"] .points-hero {
    background: linear-gradient(135deg, #0B0E10 0%, #16252B 50%, #0B0E10 100%);
}
[data-theme="dark"] .stat-card,
[data-theme="dark"] .earn-section,
[data-theme="dark"] .history-section {
    background: rgba(22, 37, 43, 0.9);
    border-color: rgba(255, 255, 255, 0.06);
}
[data-theme="dark"] .earn-item,
[data-theme="dark"] .history-item {
    background: rgba(11, 14, 16, 0.6);
    border-color: rgba(255, 255, 255, 0.04);
}
[data-theme="dark"] .earn-item:hover,
[data-theme="dark"] .stat-card:hover {
    border-color: var(--accent);
}
[data-theme="dark"] .history-item:hover {
    border-color: rgba(255, 255, 255, 0.1);
}
[data-theme="dark"] .empty-icon {
    background: linear-gradient(135deg, rgba(22, 37, 43, 0.8), rgba(30, 48, 56, 0.5));
}
</style>
@endsection