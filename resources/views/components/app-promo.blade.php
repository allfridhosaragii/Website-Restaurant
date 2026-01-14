{{-- App Promotion Floating Button --}}
<div id="appPromoButtonContainer" class="app-promo-btn-container" style="display: none;">
    <button id="appPromoBtn" class="app-promo-btn" onclick="openAppPromoModal()">
        <span class="app-promo-ripple"></span>
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="app-promo-icon">
            <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
            <line x1="12" y1="18" x2="12.01" y2="18"></line>
        </svg>
        <span class="app-promo-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="none">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
            </svg>
        </span>
        <div class="app-promo-tooltip">
            <div class="app-promo-tooltip-title">📱 Download Aplikasi</div>
            <div class="app-promo-tooltip-desc">Bonus 5.000 poin!</div>
        </div>
    </button>
</div>

{{-- App Promotion Modal --}}
<div id="appPromoModal" class="app-modal-overlay">
    <div class="app-modal-backdrop" onclick="closeAppPromoModal()"></div>
    <div class="app-modal-wrapper">
        <div class="app-modal-card">
            <div class="app-modal-header">
                <div class="app-modal-circle-1"></div>
                <div class="app-modal-circle-2"></div>
                <button class="app-modal-close" onclick="closeAppPromoModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <div class="app-modal-icon-container">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                        <line x1="12" y1="18" x2="12.01" y2="18"></line>
                    </svg>
                </div>
                <h2 class="app-modal-title">Download Aplikasi Culinaire</h2>
                <p class="app-modal-subtitle">Pengalaman Kuliner Premium di Genggaman Anda</p>
            </div>
            
            <div class="app-modal-content">
                <div class="app-modal-benefits">
                    <div class="app-benefit-card" style="animation-delay: 0.1s">
                        <div class="app-benefit-icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                            </svg>
                        </div>
                        <div class="app-benefit-text">
                            <div class="app-benefit-title">Pengalaman Lebih Cepat</div>
                            <div class="app-benefit-desc">Akses menu dan pesan dalam hitungan detik</div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="app-benefit-check">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="app-benefit-card" style="animation-delay: 0.2s">
                        <div class="app-benefit-icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                <polyline points="20 12 20 22 4 22 4 12"></polyline>
                                <rect x="2" y="7" width="20" height="5"></rect>
                                <line x1="12" y1="22" x2="12" y2="7"></line>
                                <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
                                <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
                            </svg>
                        </div>
                        <div class="app-benefit-text">
                            <div class="app-benefit-title">Poin & Rewards Eksklusif</div>
                            <div class="app-benefit-desc">Dapatkan double points setiap pemesanan</div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="app-benefit-check">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="app-benefit-card" style="animation-delay: 0.3s">
                        <div class="app-benefit-icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none" class="text-white">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </div>
                        <div class="app-benefit-text">
                            <div class="app-benefit-title">Notifikasi Promo Spesial</div>
                            <div class="app-benefit-desc">Jadi yang pertama tahu promo terbaru</div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="app-benefit-check">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                </div>
                
                <a href="/downloads/culinaire-app.apk" download="Culinaire-App.apk" class="app-download-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <div class="text-start">
                        <div class="app-btn-title">Download Aplikasi</div>
                        <div class="app-btn-subtitle">File APK (Android)</div>
                    </div>
                </a>
                
                <div class="app-bonus-info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D4AF37" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 12 20 22 4 22 4 12"></polyline>
                        <rect x="2" y="7" width="20" height="5"></rect>
                        <line x1="12" y1="22" x2="12" y2="7"></line>
                        <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
                        <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
                    </svg>
                    <span>Bonus 5.000 poin untuk pengguna baru!</span>
                </div>
                
                <button class="app-dismiss-btn" onclick="closeAppPromoModal()">Nanti Saja</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* App Promo Floating Button & Modal */
    .app-promo-btn-container {
        position: fixed;
        bottom: 28px;
        right: 28px;
        z-index: 998;
        animation: slideInRight 0.5s cubic-bezier(0.4, 0, 0.2, 1) 2s both;
    }
    
    /* On menu page, position above cart button */
    body.menu-page .app-promo-btn-container {
        bottom: 100px;
    }
    
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(100px) scale(0); }
        to { opacity: 1; transform: translateX(0) scale(1); }
    }
    
    .app-promo-btn {
        position: relative;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, #D4AF37 0%, #B8941F 100%);
        box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .app-promo-btn.pulse-active {
        animation: promoPulse 2s ease-in-out infinite;
    }
    
    @keyframes promoPulse {
        0%, 100% { box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4); }
        50% { box-shadow: 0 10px 40px rgba(212, 175, 55, 0.7); }
    }
    
    .app-promo-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 15px 40px rgba(212, 175, 55, 0.6);
    }
    
    .app-promo-btn:active {
        transform: scale(0.95);
    }
    
    .app-promo-ripple {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: rgba(212, 175, 55, 0.3);
        animation: promoPing 2s infinite;
        pointer-events: none;
        z-index: -1;
    }
    
    @keyframes promoPing {
        0% { transform: scale(1); opacity: 0.3; }
        75%, 100% { transform: scale(2); opacity: 0; }
    }
    
    .app-promo-icon {
        color: white;
        z-index: 10;
        position: relative;
    }
    
    .app-promo-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        width: 24px;
        height: 24px;
        background: #EF4444;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        z-index: 11;
        animation: promoBounce 1s infinite;
    }
    
    @keyframes promoBounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    
    .app-promo-tooltip {
        position: absolute;
        right: 100%;
        top: 50%;
        transform: translateY(-50%);
        margin-right: 16px;
        background: white;
        padding: 12px 16px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s;
        white-space: nowrap;
        text-align: left;
        z-index: 100;
    }
    
    .app-promo-tooltip::after {
        content: '';
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        border: 6px solid transparent;
        border-left-color: white;
    }
    
    .app-promo-btn:hover .app-promo-tooltip {
        opacity: 1;
    }
    
    .app-promo-tooltip-title {
        font-size: 14px;
        font-weight: 600;
        color: #1F2937;
        margin-bottom: 4px;
    }
    
    .app-promo-tooltip-desc {
        font-size: 12px;
        color: #6B7280;
    }
    
    /* Modal Styles */
    .app-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
    }
    
    .app-modal-overlay.show {
        display: block;
    }
    
    .app-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        opacity: 0;
        transition: opacity 0.2s ease-out;
    }
    
    .app-modal-overlay.show .app-modal-backdrop {
        opacity: 1;
    }
    
    .app-modal-wrapper {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        max-width: 448px;
        padding: 16px;
        z-index: 10000;
        pointer-events: none;
    }
    
    .app-modal-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        overflow: hidden;
        opacity: 0;
        transform: translateY(20px) scale(0.95);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: auto;
    }
    
    .app-modal-overlay.show .app-modal-card {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
    
    .app-modal-header {
        background: linear-gradient(135deg, #8B1538 0%, #6B0F2A 50%, #4A0A1C 100%);
        padding: 32px 24px;
        position: relative;
        overflow: hidden;
        text-align: center;
    }
    
    .app-modal-circle-1 {
        position: absolute;
        top: -80px;
        right: -80px;
        width: 160px;
        height: 160px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }
    
    .app-modal-circle-2 {
        position: absolute;
        bottom: -64px;
        left: -64px;
        width: 128px;
        height: 128px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }
    
    .app-modal-close {
        position: absolute;
        top: 24px;
        right: 24px;
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.8);
        cursor: pointer;
        z-index: 10;
        transition: color 0.2s;
        padding: 4px;
    }
    
    .app-modal-close:hover {
        color: white;
    }
    
    .app-modal-icon-container {
        display: inline-flex;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(8px);
        padding: 16px;
        border-radius: 16px;
        margin-bottom: 16px;
    }
    
    .app-modal-icon-container svg {
        color: white;
    }
    
    .app-modal-title {
        font-family: Georgia, serif;
        font-size: 24px;
        font-weight: 700;
        color: white;
        margin-bottom: 8px;
    }
    
    .app-modal-subtitle {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
    }
    
    .app-modal-content {
        padding: 24px;
    }
    
    .app-modal-benefits {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .app-benefit-card {
        display: flex;
        gap: 12px;
        padding: 12px;
        border-radius: 12px;
        background: #F9FAFB;
        align-items: flex-start;
        opacity: 0;
        transform: translateX(-20px);
        transition: all 0.3s ease-out;
    }
    
    .app-modal-overlay.show .app-benefit-card {
        opacity: 1;
        transform: translateX(0);
    }
    
    .app-benefit-icon-wrapper {
        background: linear-gradient(135deg, #8B1538 0%, #6B0F2A 100%);
        padding: 8px;
        border-radius: 8px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .app-benefit-icon-wrapper svg {
        color: white;
    }
    
    .app-benefit-text {
        flex: 1;
    }
    
    .app-benefit-title {
        font-size: 14px;
        font-weight: 600;
        color: #1F2937;
        margin-bottom: 4px;
    }
    
    .app-benefit-desc {
        font-size: 12px;
        color: #6B7280;
        line-height: 1.4;
    }
    
    .app-benefit-check {
        color: #10B981;
        flex-shrink: 0;
    }
    
    .app-download-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        width: 100%;
        padding: 16px;
        background: linear-gradient(to right, #1F2937 0%, #374151 100%);
        border-radius: 12px;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .app-download-btn:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 15px rgba(0,0,0,0.2);
        color: white;
    }
    
    .app-btn-title {
        font-size: 14px;
        font-weight: 700;
        line-height: 1.2;
    }
    
    .app-btn-subtitle {
        font-size: 12px;
        opacity: 0.8;
        line-height: 1.2;
    }
    
    .app-bonus-info {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 24px;
        color: #6B7280;
        font-size: 12px;
        font-weight: 600;
    }
    
    .app-dismiss-btn {
        width: 100%;
        padding: 12px;
        margin-top: 16px;
        background: transparent;
        border: none;
        color: #6B7280;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: color 0.2s;
    }
    
    .app-dismiss-btn:hover {
        color: #1F2937;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .app-promo-btn-container {
            bottom: 80px;
            right: 16px;
        }
        body.menu-page .app-promo-btn-container {
            bottom: 150px;
        }
        .app-promo-tooltip {
            display: none;
        }
        .app-modal-wrapper {
            padding: 16px;
        }
    }
    
    @media (max-width: 480px) {
        .app-promo-btn-container {
            bottom: 16px;
            right: 16px;
        }
        body.menu-page .app-promo-btn-container {
            bottom: 80px;
        }
        .app-promo-btn {
            width: 50px;
            height: 50px;
        }
        .app-promo-btn svg {
            width: 22px;
            height: 22px;
        }
        .app-promo-badge {
            width: 20px;
            height: 20px;
        }
        .app-promo-badge svg {
            width: 12px;
            height: 12px;
        }
    }
    
    /* Utility to stop pulse */
    .pulse-stop {
        animation: none !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // App Promo Logic
        const promoBtn = document.getElementById('appPromoButtonContainer');
        const mainBtn = document.getElementById('appPromoBtn');
        const modal = document.getElementById('appPromoModal');
        
        if (!promoBtn || !mainBtn || !modal) return;
        
        // Step 1: Show button after 2s
        setTimeout(() => {
            promoBtn.style.display = 'block';
            
            // Check session logic
            if (!sessionStorage.getItem('appPromoShown')) {
                mainBtn.classList.add('pulse-active');
                
                // Step 2: Auto-show modal after 5s (total 7s)
                setTimeout(() => {
                    openAppPromoModal();
                }, 5000);
            }
        }, 2000);
        
        window.openAppPromoModal = function() {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            
            // Stop pulse
            mainBtn.classList.remove('pulse-active');
            mainBtn.classList.add('pulse-stop');
            
            // Set session storage
            sessionStorage.setItem('appPromoShown', 'true');
        };
        
        window.closeAppPromoModal = function() {
            modal.querySelector('.app-modal-backdrop').style.opacity = '0';
            modal.querySelector('.app-modal-card').style.opacity = '0';
            modal.querySelector('.app-modal-card').style.transform = 'translateY(20px) scale(0.95)';
            
            setTimeout(() => {
                modal.classList.remove('show');
                // Reset styles for next open
                modal.querySelector('.app-modal-backdrop').style.opacity = '';
                modal.querySelector('.app-modal-card').style.opacity = '';
                modal.querySelector('.app-modal-card').style.transform = '';
                document.body.style.overflow = '';
            }, 300);
        };
    });
</script>
