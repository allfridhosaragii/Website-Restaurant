<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Website Maintenance - CULINAIRE</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        body, html {
            height: 100%;
            width: 100%;
            font-family: 'Poppins', sans-serif;
            background-color: #0B0E10;
            overflow: hidden;
            touch-action: manipulation;
            -webkit-touch-callout: none;
            -webkit-tap-highlight-color: transparent;
        }
        /* Hide scrollbars */
        ::-webkit-scrollbar {
            display: none;
        }
        body {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .maintenance-container {
            display: flex;
            height: 100vh;
            width: 100vw;
        }
        .left-panel {
            flex: 1;
            background-image: url('https://res.cloudinary.com/dh9ysyfit/image/upload/v1766046687/IMG_7856_esb0xz.jpg'); 
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(11, 14, 16, 0) 0%, rgba(11, 14, 16, 1) 100%);
        }
        .right-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 4rem;
            text-align: center;
            color: #f5f5f5;
            background: #0B0E10;
        }
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: #D4AF37;
            margin-bottom: 3rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo i {
            font-size: 1.5rem;
        }
        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: #ffffff;
        }
        .highlight-text {
            color: #D4AF37;
        }
        p.description {
            font-size: 1.1rem;
            line-height: 1.8;
            max-width: 550px;
            margin-bottom: 3rem;
            color: rgba(255,255,255,0.7);
        }
        .status-box {
            display: inline-flex;
            align-items: center;
            background-color: rgba(212, 175, 55, 0.15);
            padding: 1rem 2rem;
            border-radius: 50px;
            margin-bottom: 3rem;
            font-weight: 600;
            color: #D4AF37;
            border: 1px solid rgba(212, 175, 55, 0.3);
        }
        .status-box i {
            margin-right: 12px;
            animation: pulse 2s infinite;
        }
        .contact-section h3 {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 1.5rem;
            color: rgba(255,255,255,0.5);
        }
        .social-icons {
            display: flex;
            gap: 2.5rem;
            justify-content: center;
        }
        .social-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: rgba(255,255,255,0.7);
            transition: all 0.3s ease;
        }
        .social-link i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #D4AF37;
            transition: transform 0.3s ease;
        }
        .social-link span {
            font-size: 0.85rem;
            font-weight: 500;
        }
        .social-link:hover {
            color: #D4AF37;
        }
        .social-link:hover i {
            transform: translateY(-5px);
        }
        @keyframes pulse {
            0% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.15); }
            100% { opacity: 0.6; transform: scale(1); }
        }
        @media (max-width: 1024px) {
            .maintenance-container {
                flex-direction: column;
                height: auto;
                overflow: auto;
            }
            .left-panel {
                height: 35vh;
                flex: none;
            }
            .left-panel::after {
                background: linear-gradient(to bottom, rgba(11, 14, 16, 0) 0%, rgba(11, 14, 16, 1) 100%);
            }
            .right-panel {
                flex: none;
                padding: 2.5rem 1.5rem;
            }
            h1 { 
                font-size: 2rem; 
            }
            p.description {
                font-size: 1rem;
            }
            .social-icons {
                gap: 1.5rem;
            }
            body, html { 
                overflow: auto; 
            }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="left-panel"></div>
        <div class="right-panel">
            <div class="logo">
                <i class="fas fa-utensils"></i> CULINAIRE
            </div>
            <h1>Kami Sedang Meracik <br><span class="highlight-text">Cita Rasa Baru</span></h1>
            <p class="description">
                Mohon maaf atas ketidaknyamanan ini. Website kami sedang menjalani renovasi untuk menyajikan pengalaman menjelajah kekayaan kuliner Indonesia yang lebih nikmat. Kami akan segera kembali dengan tampilan segar dan menu yang lebih lengkap.
            </p>
            <div class="status-box">
                <i class="fas fa-fire-burner"></i> Status: Dapur Sedang Panas (Segera Kembali)
            </div>
            <div class="contact-section">
                <h3>Sementara itu, hubungi kami di:</h3>
                <div class="social-icons">
                    <a href="https://instagram.com/allfridhosaragi" class="social-link" target="_blank">
                        <i class="fab fa-instagram"></i>
                        <span>@culinaire.id</span>
                    </a>
                    <a href="https://wa.me/6282359586295" class="social-link" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                        <span>Reservasi WA</span>
                    </a>
                    <a href="mailto:pedoprimasaragi@gmail.com" class="social-link">
                        <i class="fas fa-envelope"></i>
                        <span>info@culinaire.id</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Visitor Tracking Script
        (function() {
            // Generate unique session ID
            let sessionId = sessionStorage.getItem('maintenance_session_id');
            if (!sessionId) {
                sessionId = 'mv_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                sessionStorage.setItem('maintenance_session_id', sessionId);
            }
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
            fetch('/api/maintenance-visitor/enter', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    browser: browser,
                    browser_version: browserVersion,
                    device_type: deviceType,
                    operating_system: os,
                    screen_resolution: resolution
                })
            });
            // Heartbeat every 30 seconds (optimized from 1 second to reduce server load)
            setInterval(() => {
                fetch('/api/maintenance-visitor/heartbeat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ session_id: sessionId })
                });
            }, 30000);
            // Send exit on page unload
            window.addEventListener('beforeunload', () => {
                navigator.sendBeacon('/api/maintenance-visitor/exit', JSON.stringify({
                    session_id: sessionId
                }));
            });
            // Also send exit on visibility change (for mobile)
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'hidden') {
                    navigator.sendBeacon('/api/maintenance-visitor/exit', JSON.stringify({
                        session_id: sessionId
                    }));
                }
            });
        })();
    </script>
</body>
</html>