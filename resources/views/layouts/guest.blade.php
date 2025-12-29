<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ __('messages.meta_desc') }}">
    <meta name="keywords" content="restaurant, culinary, Indonesian food, fine dining, reservasi, kuliner">
    <title>@hasSection('title') @yield('title') - @endif{{ config('app.name', __('messages.premium_restaurant')) }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"></noscript>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
    
    <!-- View Transitions API -->
    <meta name="view-transition" content="same-origin">
    <style>
        /* View Transitions - Smooth page navigation */
        @view-transition {
            navigation: auto;
        }
        
        ::view-transition-old(root) {
            animation: fade-out 0.25s ease-out forwards;
        }
        
        ::view-transition-new(root) {
            animation: fade-in 0.25s ease-in forwards;
        }
        
        @keyframes fade-out {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.98); }
        }
        
        @keyframes fade-in {
            from { opacity: 0; transform: scale(1.02); }
            to { opacity: 1; transform: scale(1); }
        }
        

    </style>
    @stack('styles')
    <script>
        window.translations = {
            en: @json(include(base_path('lang/en/messages.php'))),
            id: @json(include(base_path('lang/id/messages.php')))
        };
        window.currentLocale = "{{ app()->getLocale() }}";
    </script>
</head>
<body>
    
    @include('components.navbar')
    <main>
        @if(session('warning') && auth()->check())
            <div class="modal fade" id="warningModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" style="max-width: 650px;">
                    <div class="modal-content border-0 bg-transparent" style="overflow: visible !important; box-shadow: none !important;">
                        <div class="position-relative text-center d-flex justify-content-center">
                            <img src="https://res.cloudinary.com/dh9ysyfit/image/upload/v1765978302/IMG_7839_loero4.png" 
                                 alt="Peringatan Akun" 
                                 class="img-fluid drop-shadow-xl" 
                                 style="border-radius: 0; max-height: 90vh; object-fit: contain; pointer-events: none; -webkit-user-drag: none; user-select: none;"
                                 draggable="false"
                                 oncontextmenu="return false;">
                            <button type="button" class="btn-royal-click-area" data-bs-dismiss="modal" aria-label="Saya Mengerti"></button>
                        </div>
                    </div>
                </div>
            </div>
            <style>
               .btn-royal-click-area {
                   position: absolute;
                   bottom: 10%; 
                   left: 50%;
                   transform: translateX(-50%);
                   width: 35%;
                   height: 12%;
                   background: transparent;
                   border: none;
                   cursor: pointer;
                   border-radius: 50px;
                   z-index: 10;
               }
               .btn-royal-click-area:hover {
                   background: rgba(255, 255, 255, 0.1);
                   box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
               }
               .drop-shadow-xl {
                   filter: drop-shadow(0 20px 30px rgba(0,0,0,0.5));
               }
            </style>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var warningModal = new bootstrap.Modal(document.getElementById('warningModal'));
                    var backdrop = document.createElement('div');
                    warningModal.show();
                });
            </script>
        @endif
        @yield('content')
    </main>
    @include('components.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const htmlElement = document.documentElement;
        const savedTheme = localStorage.getItem('theme') || 'dark';
        htmlElement.setAttribute('data-theme', savedTheme);
        
        // Global toggle function for sync
        window.toggleTheme = function() {
            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        };
        
        // Setup all theme toggles after DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggles = document.querySelectorAll('.theme-toggle');
            themeToggles.forEach(toggle => {
                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    window.toggleTheme();
                });
            });
        });
        
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar-culinaire');
            if (navbar) {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            }
        });
    </script>
    <script src="{{ asset('js/cursor.js') }}"></script>
    <script src="{{ asset('js/performance-core.js') }}"></script>
    <script>
        // Ultimate UI Protection
        document.addEventListener('dragstart', e => e.preventDefault());
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('selectstart', e => e.preventDefault());
        document.addEventListener('keydown', function(e) {
            if (
                e.key === 'F12' ||
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) ||
                (e.ctrlKey && e.key === 'U') ||
                (e.ctrlKey && e.key === 'S')
            ) {
                e.preventDefault();
                return false;
            }
        });
        // --- SMART RESOURCE PRELOADER INTEGRATION ---
        window.addEventListener('load', () => {
             // Let the Optimizer handle these heavy assets intelligently
            const heavyAssets = [
                'https://res.cloudinary.com/dh9ysyfit/image/fetch/w_1920,c_fill,f_auto,q_auto/https://images.unsplash.com/photo-1505935428862-770b6f24f629',
                'https://res.cloudinary.com/dh9ysyfit/image/fetch/w_800,c_fill,f_auto,q_80/https://images.unsplash.com/photo-1516455590571-18256e5bb9ff',
                'https://res.cloudinary.com/dh9ysyfit/image/fetch/w_800,c_fill,f_auto,q_80/https://images.unsplash.com/photo-1559339352-11d035aa65de',
                'https://res.cloudinary.com/dh9ysyfit/image/fetch/w_800,c_fill,f_auto,q_80/https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b',
                // Common Placeholders
                'https://res.cloudinary.com/dh9ysyfit/image/fetch/w_400,h_300,c_fill,f_auto,q_auto/https://images.unsplash.com/photo-1546069901-ba9599a7e63c'
            ];
            // Helper to fetch image in background
            const prefetchImage = (url) => {
                return new Promise((resolve) => {
                    const img = new Image();
                    img.src = url;
                    // Low priority decode
                    img.decoding = 'async'; 
                    img.onload = resolve;
                    img.onerror = resolve; // Continue even if fail
                });
            };
            const runSmartPreload = async () => {
                // Wait 2 seconds for main page to settle interactive state
                await new Promise(r => setTimeout(r, 2000));
                // Fetch sequentially (One by one) to prevent CPU spikes/Heat
                for(const url of heavyAssets) {
                    await prefetchImage(url);
                    // Small breathing room between fetches
                    await new Promise(r => setTimeout(r, 200)); 
                }
                console.log('Site assets preloaded in background.');
            };
            // Use requestIdleCallback if available for maximum efficiency
            if ('requestIdleCallback' in window) {
                requestIdleCallback(runSmartPreload);
            } else {
                runSmartPreload();
            }
        });
    </script>
    @stack('scripts')
    @if(request()->has('cms_mode') || session('cms_mode'))
        <script src="{{ asset('js/cms-iframe.js') }}"></script>
    @endif
    
    <!-- Real-time Maintenance Mode Detection -->
    @if(!request()->is('maintenance*') && !request()->is('project*') && !request()->is('login') && !request()->is('register') && !request()->is('auth/*'))
    <script>
        (function() {
            let maintenanceCheckInterval;
            const currentPath = window.location.pathname;
            
            // Don't run on root or auth pages
            if (currentPath === '/' || currentPath.startsWith('/login') || currentPath.startsWith('/register') || currentPath.startsWith('/auth')) return;
            
            // Check maintenance status
            async function checkMaintenanceStatus() {
                try {
                    const response = await fetch('/api/maintenance-status', {
                        headers: { 'Accept': 'application/json' },
                        cache: 'no-store'
                    });
                    const data = await response.json();
                    
                    if (data.maintenance) {
                        // Stop polling
                        clearInterval(maintenanceCheckInterval);
                        
                        // Redirect to landing page (maintenance will be shown there)
                        window.location.href = '/';
                    }
                } catch (error) {
                    console.log('Maintenance check failed:', error);
                }
            }
            
            // Start polling every 5 seconds
            maintenanceCheckInterval = setInterval(checkMaintenanceStatus, 5000);
            
            // Also check immediately on page load
            checkMaintenanceStatus();
        })();
    </script>
    @endif

    <!-- Global Site Visitor Tracking -->
    @if(!request()->is('maintenance') && !request()->is('maintenance/*'))
    <script>
        (function() {
            // Generate unique session ID per page
            const pageSessionId = 'sv_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            const pageUrl = window.location.pathname;
            const pageTitle = document.title;

            // Detect browser info
            const ua = navigator.userAgent;
            let browser = 'Unknown';
            let browserVersion = '';
            
            if (ua.includes('Firefox/')) {
                browser = 'Firefox';
                browserVersion = ua.match(/Firefox\/(\d+)/)?.[1] || '';
            } else if (ua.includes('Chrome/') && !ua.includes('Edg/')) {
                browser = 'Chrome';
                browserVersion = ua.match(/Chrome\/(\d+)/)?.[1] || '';
            } else if (ua.includes('Edg/')) {
                browser = 'Edge';
                browserVersion = ua.match(/Edg\/(\d+)/)?.[1] || '';
            } else if (ua.includes('Safari/') && !ua.includes('Chrome')) {
                browser = 'Safari';
                browserVersion = ua.match(/Version\/(\d+)/)?.[1] || '';
            } else if (ua.includes('Opera') || ua.includes('OPR/')) {
                browser = 'Opera';
                browserVersion = ua.match(/(?:Opera|OPR)\/(\d+)/)?.[1] || '';
            }

            // Detect device type
            let deviceType = 'Desktop';
            if (/Mobi|Android/i.test(ua)) {
                deviceType = 'Mobile';
            } else if (/Tablet|iPad/i.test(ua)) {
                deviceType = 'Tablet';
            }

            // Detect OS
            let os = 'Unknown';
            if (ua.includes('Windows')) os = 'Windows';
            else if (ua.includes('Mac')) os = 'macOS';
            else if (ua.includes('Linux')) os = 'Linux';
            else if (ua.includes('Android')) os = 'Android';
            else if (ua.includes('iOS') || ua.includes('iPhone') || ua.includes('iPad')) os = 'iOS';

            // Screen resolution
            const resolution = window.screen.width + 'x' + window.screen.height;

            // Send entry data
            fetch('/api/site-visitor/enter', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    session_id: pageSessionId,
                    page_url: pageUrl,
                    page_title: pageTitle,
                    browser: browser,
                    browser_version: browserVersion,
                    device_type: deviceType,
                    operating_system: os,
                    screen_resolution: resolution
                })
            });

            // Heartbeat every 1 second
            setInterval(() => {
                fetch('/api/site-visitor/heartbeat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ session_id: pageSessionId, page_url: pageUrl })
                });
            }, 1000);

            // Send exit on page unload
            window.addEventListener('beforeunload', () => {
                navigator.sendBeacon('/api/site-visitor/exit', JSON.stringify({
                    session_id: pageSessionId,
                    page_url: pageUrl
                }));
            });

            // Also send exit on visibility change (for mobile)
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'hidden') {
                    navigator.sendBeacon('/api/site-visitor/exit', JSON.stringify({
                        session_id: pageSessionId,
                        page_url: pageUrl
                    }));
                }
            });
        })();
    </script>
    @endif
</body>
</html>