<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Project Recreation Timeline | Culinaire</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold-primary: #D4AF37;
            --gold-light: #F4E4BC;
            --gold-bright: #FFD700;
            --gold-dark: #AA8C2C;
            --gold-gradient: linear-gradient(135deg, #D4AF37 0%, #F4E4BC 25%, #D4AF37 50%, #AA8C2C 75%, #D4AF37 100%);
            --dark-void: #050508;
            --dark-deep: #0a0a10;
            --dark-surface: #101018;
            --dark-card: #16161f;
            --dark-elevated: #1c1c28;
            --dark-border: #2a2a3a;
            --text-white: #ffffff;
            --text-light: #e8e8f0;
            --text-secondary: #9898a8;
            --text-muted: #58586a;
            --success: #00D26A;
            --danger: #FF3B5C;
            --warning: #FFB800;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--dark-void);
            color: var(--text-light);
            min-height: 100vh;
            line-height: 1.7;
            overflow-x: hidden;
        }
        .font-serif { font-family: 'Cormorant Garamond', serif; }
        .bg-mesh {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(ellipse 80% 50% at 20% 40%, rgba(212, 175, 55, 0.03) 0%, transparent 50%),
                radial-gradient(ellipse 60% 40% at 80% 60%, rgba(212, 175, 55, 0.02) 0%, transparent 50%),
                radial-gradient(ellipse 100% 100% at 50% 0%, rgba(212, 175, 55, 0.02) 0%, transparent 40%);
            pointer-events: none;
            z-index: 0;
        }
        .gold-text {
            background: var(--gold-gradient);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 3s linear infinite;
        }
        @keyframes shimmer {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
        .glass-ultra {
            background: linear-gradient(135deg, rgba(22, 22, 31, 0.9) 0%, rgba(16, 16, 24, 0.95) 100%);
            backdrop-filter: blur(40px) saturate(180%);
            -webkit-backdrop-filter: blur(40px) saturate(180%);
            border: 1px solid rgba(212, 175, 55, 0.08);
            box-shadow: 
                0 0 0 1px rgba(255, 255, 255, 0.02) inset,
                0 20px 60px rgba(0, 0, 0, 0.5),
                0 0 100px rgba(212, 175, 55, 0.03);
        }
        .glass-ultra:hover {
            border-color: rgba(212, 175, 55, 0.2);
            box-shadow: 
                0 0 0 1px rgba(255, 255, 255, 0.05) inset,
                0 30px 80px rgba(0, 0, 0, 0.6),
                0 0 120px rgba(212, 175, 55, 0.08);
        }
        .glow-line {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold-primary), transparent);
            opacity: 0.3;
        }
        .btn-luxury {
            background: var(--gold-gradient);
            background-size: 200% auto;
            color: var(--dark-void);
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 30px rgba(212, 175, 55, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 12px;
        }
        .btn-luxury:hover {
            background-position: right center;
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(212, 175, 55, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.2) inset;
        }
        .btn-ghost-lux {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--dark-border);
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 12px;
        }
        .btn-ghost-lux:hover {
            background: rgba(212, 175, 55, 0.1);
            border-color: var(--gold-primary);
            color: var(--gold-primary);
        }
        .scroll-luxury::-webkit-scrollbar { width: 3px; height: 3px; }
        .scroll-luxury::-webkit-scrollbar-track { background: transparent; }
        .scroll-luxury::-webkit-scrollbar-thumb { background: var(--gold-dark); border-radius: 10px; }
        .container-main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            position: relative;
            z-index: 1;
        }
        @media (max-width: 768px) {
            .container-main { padding: 24px 16px; }
        }
        .header-section {
            text-align: center;
            margin-bottom: 60px;
            padding: 40px 20px;
        }
        @media (max-width: 768px) {
            .header-section { margin-bottom: 40px; padding: 20px 16px; }
        }
        .header-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: var(--gold-primary);
            font-size: 11px;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 24px;
        }
        .header-eyebrow::before,
        .header-eyebrow::after {
            content: '';
            width: 40px;
            height: 1px;
            background: var(--gold-primary);
            opacity: 0.5;
        }
        @media (max-width: 768px) {
            .header-eyebrow::before,
            .header-eyebrow::after { width: 20px; }
        }
        .header-title {
            font-size: clamp(32px, 8vw, 72px);
            font-weight: 300;
            letter-spacing: -2px;
            margin-bottom: 20px;
            line-height: 1.1;
        }
        .header-desc {
            color: var(--text-secondary);
            font-size: 15px;
            max-width: 500px;
            margin: 0 auto 32px;
            font-weight: 300;
        }
        @media (max-width: 768px) {
            .header-desc { font-size: 14px; }
        }
        .legend-row {
            display: flex;
            justify-content: center;
            gap: 32px;
            flex-wrap: wrap;
        }
        @media (max-width: 768px) {
            .legend-row { gap: 16px; }
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
        }
        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        .legend-dot.completed { background: var(--success); box-shadow: 0 0 10px var(--success); }
        .legend-dot.current { background: var(--gold-primary); animation: pulse-gold 2s infinite; }
        .legend-dot.locked { background: var(--dark-border); }
        @keyframes pulse-gold {
            0%, 100% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.4); }
            50% { box-shadow: 0 0 0 8px rgba(212, 175, 55, 0); }
        }
        .members-grid { display: flex; flex-direction: column; gap: 20px; }
        .member-card {
            border-radius: 24px;
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .member-card:hover { transform: translateY(-4px); }
        .member-header {
            padding: 28px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }
        @media (max-width: 768px) {
            .member-header { padding: 20px 16px; gap: 16px; }
        }
        .member-info { display: flex; align-items: center; gap: 20px; }
        @media (max-width: 768px) {
            .member-info { gap: 14px; }
        }
        .avatar-luxury {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--gold-gradient);
            padding: 3px;
            flex-shrink: 0;
        }
        @media (max-width: 768px) {
            .avatar-luxury { width: 52px; height: 52px; }
        }
        .avatar-inner {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: var(--dark-surface);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 600;
            color: var(--gold-primary);
        }
        @media (max-width: 768px) {
            .avatar-inner { font-size: 18px; }
        }
        .member-details h2 {
            font-size: 20px;
            font-weight: 500;
            color: var(--text-white);
            margin-bottom: 4px;
        }
        @media (max-width: 768px) {
            .member-details h2 { font-size: 16px; }
        }
        .member-details p {
            font-size: 12px;
            color: var(--text-muted);
        }
        @media (max-width: 768px) {
            .member-details p { font-size: 11px; }
        }
        .member-stats {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        @media (max-width: 768px) {
            .member-stats { gap: 16px; width: 100%; justify-content: flex-end; }
        }
        .stat-box { text-align: center; }
        .stat-box .label { font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        .stat-box .value { font-size: 14px; color: var(--text-light); font-weight: 500; margin-top: 2px; }
        .progress-circle-wrap {
            width: 56px;
            height: 56px;
            position: relative;
        }
        @media (max-width: 768px) {
            .progress-circle-wrap { width: 48px; height: 48px; }
        }
        .progress-circle-wrap svg { transform: rotate(-90deg); }
        .progress-circle-wrap circle { fill: none; stroke-width: 3; }
        .progress-circle-wrap .track { stroke: var(--dark-border); }
        .progress-circle-wrap .fill { stroke: url(#goldGrad); stroke-linecap: round; transition: stroke-dashoffset 0.8s cubic-bezier(0.4, 0, 0.2, 1); }
        .progress-text { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 11px; font-weight: 600; color: var(--gold-primary); }
        .arrow-toggle {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--dark-elevated);
            transition: all 0.3s ease;
        }
        .arrow-toggle svg { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); color: var(--text-muted); }
        .arrow-toggle.open svg { transform: rotate(180deg); }
        .arrow-toggle:hover { background: rgba(212, 175, 55, 0.1); }
        .arrow-toggle:hover svg { color: var(--gold-primary); }
        .member-content {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.6s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease;
        }
        .member-content.open { max-height: 10000px; opacity: 1; }
        .content-inner { padding: 0 28px 28px; }
        @media (max-width: 768px) {
            .content-inner { padding: 0 16px 20px; }
        }
        .steps-list {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 8px;
        }
        @media (max-width: 768px) {
            .steps-list { max-height: 400px; padding-right: 4px; }
        }
        .step-row {
            padding: 16px 20px;
            margin-bottom: 8px;
            border-radius: 12px;
            background: rgba(16, 16, 24, 0.6);
            border-left: 2px solid var(--dark-border);
            transition: all 0.3s ease;
        }
        @media (max-width: 768px) {
            .step-row { padding: 14px 12px; }
        }
        .step-row.completed { border-left-color: var(--success); }
        .step-row.completed .step-num { background: var(--success); box-shadow: 0 0 15px rgba(0, 210, 106, 0.3); }
        .step-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            cursor: pointer;
        }
        .step-left { display: flex; align-items: center; gap: 14px; flex: 1; min-width: 0; }
        @media (max-width: 768px) {
            .step-left { gap: 10px; }
        }
        .step-num {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--dark-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-light);
            flex-shrink: 0;
            transition: all 0.3s ease;
        }
        @media (max-width: 768px) {
            .step-num { width: 32px; height: 32px; font-size: 10px; }
        }
        .step-info { flex: 1; min-width: 0; }
        .step-info .action { font-size: 13px; font-weight: 500; color: var(--text-light); margin-bottom: 2px; }
        .step-info .file { font-size: 11px; color: var(--text-muted); font-family: 'JetBrains Mono', monospace; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        @media (max-width: 768px) {
            .step-info .action { font-size: 12px; }
            .step-info .file { font-size: 10px; }
        }
        .step-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .step-lines { font-size: 10px; color: var(--text-muted); font-family: monospace; }
        @media (max-width: 768px) {
            .step-lines { display: none; }
        }
        .step-code {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, opacity 0.3s ease;
        }
        .step-code.open { max-height: 500px; opacity: 1; margin-top: 16px; }
        .code-container {
            background: var(--dark-void);
            border-radius: 12px;
            border: 1px solid var(--dark-border);
            position: relative;
            overflow: hidden;
        }
        .code-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.3), transparent);
        }
        .code-container pre {
            padding: 16px;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-size: 12px;
            line-height: 1.7;
            overflow-x: auto;
            color: #c8d0e0;
            margin: 0;
        }
        @media (max-width: 768px) {
            .code-container pre { font-size: 10px; padding: 12px; }
        }
        .copy-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background: var(--dark-elevated);
            border: 1px solid var(--dark-border);
            color: var(--text-secondary);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .copy-btn:hover { background: var(--gold-primary); color: var(--dark-void); border-color: var(--gold-primary); }
        .submit-section {
            margin-top: 24px;
            padding: 24px;
            background: rgba(16, 16, 24, 0.6);
            border-radius: 16px;
            border: 1px solid var(--dark-border);
        }
        @media (max-width: 768px) {
            .submit-section { padding: 16px; }
        }
        .submit-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .submit-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-white);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
        }
        .badge-default { background: rgba(88, 88, 106, 0.2); color: var(--text-secondary); border: 1px solid var(--dark-border); }
        .badge-pending { background: rgba(255, 184, 0, 0.15); color: var(--warning); border: 1px solid rgba(255, 184, 0, 0.3); }
        .badge-approved { background: rgba(0, 210, 106, 0.15); color: var(--success); border: 1px solid rgba(0, 210, 106, 0.3); }
        .badge-rejected { background: rgba(255, 59, 92, 0.15); color: var(--danger); border: 1px solid rgba(255, 59, 92, 0.3); }
        .input-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .input-luxury {
            flex: 1;
            min-width: 200px;
            background: var(--dark-void);
            border: 1px solid var(--dark-border);
            border-radius: 12px;
            padding: 14px 18px;
            color: var(--text-light);
            font-size: 13px;
            transition: all 0.3s ease;
        }
        @media (max-width: 768px) {
            .input-luxury { padding: 12px 14px; font-size: 12px; }
        }
        .input-luxury:focus { outline: none; border-color: var(--gold-primary); box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1); }
        .input-luxury::placeholder { color: var(--text-muted); }
        .submit-note { font-size: 11px; color: var(--text-muted); margin-top: 12px; }
        .admin-panel {
            margin-top: 16px;
            padding: 16px;
            background: rgba(212, 175, 55, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(212, 175, 55, 0.15);
        }
        .admin-label { font-size: 11px; color: var(--gold-primary); margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
        .admin-btns { display: flex; gap: 8px; margin-bottom: 12px; }
        .btn-approve {
            flex: 1;
            background: rgba(0, 210, 106, 0.15);
            border: 1px solid var(--success);
            color: var(--success);
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        .btn-approve:hover { background: var(--success); color: var(--dark-void); }
        .btn-reject {
            flex: 1;
            background: rgba(255, 59, 92, 0.15);
            border: 1px solid var(--danger);
            color: var(--danger);
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        .btn-reject:hover { background: var(--danger); color: var(--dark-void); }
        .feedback-box {
            width: 100%;
            background: var(--dark-void);
            border: 1px solid var(--dark-border);
            border-radius: 8px;
            padding: 10px;
            color: var(--text-light);
            font-size: 11px;
            resize: none;
            height: 60px;
        }
        .feedback-box:focus { outline: none; border-color: var(--gold-primary); }
        .alert-box {
            margin-top: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 12px;
            display: none;
        }
        .alert-warning { background: rgba(255, 59, 92, 0.1); border: 1px solid rgba(255, 59, 92, 0.3); color: #ff6b7f; }
        .alert-success { background: rgba(0, 210, 106, 0.1); border: 1px solid rgba(0, 210, 106, 0.3); color: #4ade80; }
        .footer-section {
            text-align: center;
            padding: 60px 20px 40px;
        }
        .reset-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 11px;
            cursor: pointer;
            opacity: 0.5;
            transition: all 0.3s ease;
        }
        .reset-btn:hover { opacity: 1; color: var(--danger); }
        .decorative-line {
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 1px;
            height: 100%;
            background: linear-gradient(180deg, transparent, rgba(212, 175, 55, 0.1) 20%, rgba(212, 175, 55, 0.1) 80%, transparent);
            pointer-events: none;
            z-index: 0;
        }
        @media (max-width: 768px) {
            .decorative-line { display: none; }
        }
        .member-card.locked { opacity: 0.6; pointer-events: none; }
        .member-card.locked .avatar-luxury { background: var(--dark-border); }
        .member-card.locked .avatar-inner { color: var(--text-muted); }
        .member-card.locked .member-details h2 { color: var(--text-muted); }
        .member-card.locked:hover { transform: none; }
        .lock-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 20px;
            color: var(--text-muted);
        }
        .avatar-wrap {
            position: relative;
            width: 64px;
            height: 64px;
            flex-shrink: 0;
        }
        @media (max-width: 768px) {
            .avatar-wrap { width: 52px; height: 52px; }
            .lock-icon { font-size: 16px; }
        }
        .locked-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(5, 5, 8, 0.7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .locked-message {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }
        .locked-message svg {
            width: 48px;
            height: 48px;
            margin-bottom: 16px;
            color: var(--gold-dark);
            opacity: 0.5;
        }
        .locked-message p {
            font-size: 14px;
            margin-bottom: 8px;
        }
        .locked-message span {
            font-size: 12px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>
    <div class="decorative-line"></div>
    <svg style="position:absolute;width:0;height:0;">
        <defs>
            <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#D4AF37"/>
                <stop offset="50%" stop-color="#F4E4BC"/>
                <stop offset="100%" stop-color="#D4AF37"/>
            </linearGradient>
        </defs>
    </svg>
    <div class="container-main">
        <header class="header-section">
            <div class="header-eyebrow">Culinaire Development</div>
            <h1 class="header-title font-serif">
                <span class="gold-text">Project Timeline</span>
            </h1>
            <p class="header-desc">
                800 langkah pengerjaan proyek. Setiap anggota tim bertanggung jawab atas 200 langkah.
            </p>
            <div class="legend-row">
                <div class="legend-item"><div class="legend-dot completed"></div><span>Completed</span></div>
                <div class="legend-item"><div class="legend-dot current"></div><span>Current</span></div>
                <div class="legend-item"><div class="legend-dot locked"></div><span>Locked</span></div>
            </div>
        </header>
        <div class="members-grid">
            @foreach($members as $index => $member)
            <div class="member-card glass-ultra" id="member-{{ $member['id'] }}" data-member-id="{{ $member['id'] }}">
                <div class="member-header" onclick="toggleMember({{ $member['id'] }})">
                    <div class="member-info">
                        <div class="avatar-wrap">
                            <div class="avatar-luxury">
                                <div class="avatar-inner" id="avatar-text-{{ $member['id'] }}">{{ substr($member['name'], 0, 1) }}</div>
                            </div>
                            <div class="locked-overlay" id="lock-overlay-{{ $member['id'] }}" style="display: none;">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M12 1C8.676 1 6 3.676 6 7v2H4v14h16V9h-2V7c0-3.324-2.676-6-6-6zm0 2c2.276 0 4 1.724 4 4v2H8V7c0-2.276 1.724-4 4-4zm0 10c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z"/></svg>
                            </div>
                        </div>
                        <div class="member-details">
                            <h2>{{ $member['name'] }}</h2>
                            <p>{{ $member['role'] }} • {{ $member['email'] }}</p>
                        </div>
                    </div>
                    <div class="member-stats">
                        <div class="stat-box">
                            <div class="label">Steps</div>
                            <div class="value">{{ count($member['steps']) }}</div>
                        </div>
                        <div class="progress-circle-wrap" id="ring-{{ $member['id'] }}">
                            <svg viewBox="0 0 56 56" width="100%" height="100%">
                                <circle class="track" cx="28" cy="28" r="24"></circle>
                                <circle class="fill" cx="28" cy="28" r="24" stroke-dasharray="150.8" stroke-dashoffset="150.8"></circle>
                            </svg>
                            <span class="progress-text" id="progress-{{ $member['id'] }}">0%</span>
                        </div>
                        <div class="arrow-toggle" id="arrow-{{ $member['id'] }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                        </div>
                    </div>
                </div>
                <div class="member-content" id="content-{{ $member['id'] }}">
                    <div class="content-inner">
                        <div class="glow-line" style="margin-bottom: 24px;"></div>
                        <div class="steps-list scroll-luxury">
                            @foreach($member['steps'] as $step)
                            <div class="step-row" id="step-{{ $step['step'] }}" data-step="{{ $step['step'] }}">
                                <div class="step-top" onclick="toggleStep({{ $step['step'] }})">
                                    <div class="step-left">
                                        <div class="step-num">{{ $step['step'] }}</div>
                                        <div class="step-info">
                                            <div class="action">{{ $step['action'] }}</div>
                                            <div class="file">{{ $step['file'] }}</div>
                                        </div>
                                    </div>
                                    <div class="step-right">
                                        <span class="step-lines">L{{ $step['line_start'] }}-{{ $step['line_end'] }}</span>
                                        <button class="btn-ghost-lux" onclick="event.stopPropagation(); markComplete({{ $step['step'] }})">✓</button>
                                    </div>
                                </div>
                                <div class="step-code" id="code-{{ $step['step'] }}">
                                    <div class="code-container">
                                        <pre class="scroll-luxury"><code>{{ $step['code'] }}</code></pre>
                                        <button class="copy-btn" onclick="copyCode({{ $step['step'] }})">Copy</button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="submit-section">
                            <div class="submit-header">
                                <div class="submit-title">📦 Submit Repository</div>
                                <span class="badge badge-default" id="status-{{ $member['id'] }}">Not Submitted</span>
                            </div>
                            <div class="input-row">
                                <input type="text" class="input-luxury" id="repo-{{ $member['id'] }}" placeholder="https://github.com/username/repo">
                                <button class="btn-luxury" onclick="submitRepo({{ $member['id'] }})">Submit</button>
                            </div>
                            <p class="submit-note">Pastikan 100% langkah selesai sebelum submit.</p>
                            @if(Auth::check() && Auth::user()->email === 'pedoprimasaragi@gmail.com')
                            <div class="admin-panel" id="admin-{{ $member['id'] }}">
                                <div class="admin-label">🔒 Admin Review</div>
                                <div class="admin-btns">
                                    <button class="btn-approve" onclick="approveRepo({{ $member['id'] }})">✓ Approve</button>
                                    <button class="btn-reject" onclick="rejectRepo({{ $member['id'] }})">✗ Reject</button>
                                </div>
                                <textarea class="feedback-box" id="feedback-{{ $member['id'] }}" placeholder="Feedback (jika reject)"></textarea>
                            </div>
                            @endif
                            <div class="alert-box alert-warning" id="warning-{{ $member['id'] }}"></div>
                            <div class="alert-box alert-success" id="success-{{ $member['id'] }}"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="updates-section glass-ultra" id="updates-section" style="margin-top: 24px; border-radius: 24px; overflow: hidden;">
            <div class="member-header" onclick="toggleUpdates()" style="cursor: pointer;">
                <div class="member-info">
                    <div class="avatar-wrap">
                        <div class="avatar-luxury" style="background: linear-gradient(135deg, #FF6B6B 0%, #FFE66D 100%);">
                            <div class="avatar-inner" style="color: #FF6B6B;" id="updates-avatar">⚡</div>
                        </div>
                        <div class="locked-overlay" id="updates-lock-overlay" style="display: none;">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M12 1C8.676 1 6 3.676 6 7v2H4v14h16V9h-2V7c0-3.324-2.676-6-6-6zm0 2c2.276 0 4 1.724 4 4v2H8V7c0-2.276 1.724-4 4-4zm0 10c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z"/></svg>
                        </div>
                    </div>
                    <div class="member-details">
                        <h2 style="color: #FF6B6B;">Code Updates</h2>
                        <p>Perubahan code setelah 800 langkah selesai</p>
                    </div>
                </div>
                <div class="member-stats">
                    <div class="stat-box">
                        <div class="label">Updates</div>
                        <div class="value">0</div>
                    </div>
                    <div class="arrow-toggle" id="arrow-updates">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                    </div>
                </div>
            </div>
            <div class="member-content" id="content-updates">
                <div class="content-inner">
                    <div class="glow-line" style="margin-bottom: 24px; background: linear-gradient(90deg, transparent, #FF6B6B, transparent);"></div>
                    <div id="updates-list-container">
                        <div class="steps-list scroll-luxury" id="updates-list">
                            <div style="text-align: center; padding: 20px; color: var(--text-muted);">Loading updates...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer-section">
            @if(Auth::check() && Auth::user()->email === 'pedoprimasaragi@gmail.com')
            <button class="reset-btn" onclick="resetProgress()">Reset All Progress</button>
            @endif
        </footer>
    </div>
    <script>
        let completedSteps = new Set();
        let repoSubmissions = {};
        let lastUpdated = null;
        const POLL_INTERVAL = 3000;
        const isAdmin = {{ Auth::check() && Auth::user()->email === 'admin@super.admin' ? 'true' : 'false' }};
        document.addEventListener('DOMContentLoaded', () => {
            fetchProgress();
            setInterval(pollForUpdates, POLL_INTERVAL);
        });
        async function fetchProgress() {
            try {
                const res = await fetch('/project/progress');
                const data = await res.json();
                completedSteps = new Set(data.completed_steps || []);
                repoSubmissions = data.repo_submissions || {};
                lastUpdated = data.updated_at;
                applyProgressUI();
                applySubmissionsUI();
                renderUpdates(data.updates || []);
            } catch (e) { console.error('fetchProgress error:', e); }
        }
        async function pollForUpdates() {
            try {
                const res = await fetch('/project/progress');
                const data = await res.json();
                if (data.updated_at !== lastUpdated) {
                    completedSteps = new Set(data.completed_steps || []);
                    repoSubmissions = data.repo_submissions || {};
                    lastUpdated = data.updated_at;
                    applyProgressUI();
                    applySubmissionsUI();
                }
                renderUpdates(data.updates || []);
            } catch (e) { console.error('pollForUpdates error:', e); }
        }
        function renderUpdates(updates) {
            const container = document.getElementById('updates-list');
            if (!container) return;
            const updatesCount = document.querySelector('#updates-section .stat-box .value');
            if (updatesCount) updatesCount.textContent = updates.length;
            if (updates.length === 0) {
                container.innerHTML = '<div style="text-align: center; padding: 40px; color: var(--text-muted);">Belum ada update code</div>';
                return;
            }
            let html = '';
            updates.forEach(update => {
                html += `
                    <div class="step-row" id="update-${update.id}" style="border-left-color: #FF6B6B;">
                        <div class="step-top" onclick="toggleUpdate(${update.id})">
                            <div class="step-left">
                                <div class="step-num" style="background: linear-gradient(135deg, #FF6B6B 0%, #FFE66D 100%); color: #000;">${update.id}</div>
                                <div class="step-info">
                                    <div class="action" style="color: #FF6B6B;">${update.title}</div>
                                    <div class="file">${update.file_path}</div>
                                </div>
                            </div>
                            <div class="step-right">
                                <span class="step-lines" style="color: #FFE66D;">${update.update_date}</span>
                                <span class="badge" style="background: rgba(255, 107, 107, 0.2); color: #FF6B6B; border: 1px solid rgba(255, 107, 107, 0.3); font-size: 10px; padding: 4px 8px;">${update.update_type.toUpperCase()}</span>
                                ${isAdmin ? `<button class="btn-ghost-lux" onclick="event.stopPropagation(); deleteUpdate(${update.id}, '${update.title.replace(/'/g, "\\'")}')" style="color: #FF3B5C; border-color: #FF3B5C;" title="Delete">🗑️</button>` : ''}
                            </div>
                        </div>
                        <div class="step-code" id="update-code-${update.id}">
                            <p style="font-size: 13px; color: var(--text-light); padding: 16px; background: rgba(16, 16, 24, 0.6); border-radius: 12px; line-height: 1.6;">${update.description}</p>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }
        async function syncToServer() {
            try {
                await fetch('/project/progress', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ completed_steps: [...completedSteps], repo_submissions: repoSubmissions })
                });
            } catch (e) {}
        }
        function toggleMember(id) {
            const content = document.getElementById(`content-${id}`);
            const arrow = document.getElementById(`arrow-${id}`);
            content.classList.toggle('open');
            arrow.classList.toggle('open');
        }
        function toggleStep(stepNum) {
            document.getElementById(`code-${stepNum}`).classList.toggle('open');
        }
        function copyCode(stepNum) {
            const code = document.querySelector(`#code-${stepNum} code`).textContent;
            navigator.clipboard.writeText(code).then(() => {
                const btn = document.querySelector(`#code-${stepNum} .copy-btn`);
                btn.textContent = 'Copied!';
                setTimeout(() => btn.textContent = 'Copy', 1500);
            });
        }
        function markComplete(stepNum) {
            completedSteps.add(stepNum);
            updateStepUI(stepNum);
            updateAllProgress();
            syncToServer();
        }
        function updateStepUI(stepNum) {
            const el = document.getElementById(`step-${stepNum}`);
            if (el && completedSteps.has(stepNum)) el.classList.add('completed');
        }
        function applyProgressUI() {
            document.querySelectorAll('.step-row').forEach(el => el.classList.remove('completed'));
            completedSteps.forEach(s => updateStepUI(s));
            updateAllProgress();
        }
        function getMemberProgress(id) {
            const start = (id - 1) * 200 + 1, end = id * 200;
            let done = 0;
            for (let i = start; i <= end; i++) if (completedSteps.has(i)) done++;
            return Math.round((done / 200) * 100);
        }
        function updateAllProgress() {
            const progressData = {};
            [1, 2, 3, 4].forEach(id => {
                const pct = getMemberProgress(id);
                progressData[id] = pct;
                const txt = document.getElementById(`progress-${id}`);
                if (txt) txt.textContent = `${pct}%`;
                const circle = document.querySelector(`#ring-${id} .fill`);
                if (circle) circle.style.strokeDashoffset = 150.8 - (pct / 100) * 150.8;
            });
            applyLockState(progressData);
        }
        function applyLockState(progressData) {
            const memberNames = { 1: 'Edo', 2: 'Haidar', 3: 'Dimas', 4: 'Bernard' };
            [1, 2, 3, 4].forEach(id => {
                const card = document.getElementById(`member-${id}`);
                const lockOverlay = document.getElementById(`lock-overlay-${id}`);
                const content = document.getElementById(`content-${id}`);
                const stepsContainer = content?.querySelector('.steps-list');
                let isLocked = false;
                let prevMemberName = '';
                if (id > 1) {
                    const prevProgress = progressData[id - 1];
                    if (prevProgress < 100) {
                        isLocked = true;
                        prevMemberName = memberNames[id - 1];
                    }
                }
                if (isLocked) {
                    card.classList.add('locked');
                    card.style.pointerEvents = 'none';
                    if (lockOverlay) lockOverlay.style.display = 'flex';
                    if (stepsContainer) stepsContainer.innerHTML = `<div class="locked-message"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 1C8.676 1 6 3.676 6 7v2H4v14h16V9h-2V7c0-3.324-2.676-6-6-6zm0 2c2.276 0 4 1.724 4 4v2H8V7c0-2.276 1.724-4 4-4zm0 10c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z"/></svg><p>Section Terkunci</p><span>Tunggu ${prevMemberName} menyelesaikan 100% steps</span></div>`;
                } else {
                    card.classList.remove('locked');
                    card.style.pointerEvents = 'auto';
                    if (lockOverlay) lockOverlay.style.display = 'none';
                }
            });
            applyUpdatesLock(progressData);
        } 
        function applyUpdatesLock(progressData) {
            const updatesSection = document.getElementById('updates-section');
            const updatesLockOverlay = document.getElementById('updates-lock-overlay');
            updatesSection.classList.remove('locked');
            updatesSection.style.pointerEvents = 'auto';
            if (updatesLockOverlay) updatesLockOverlay.style.display = 'none';
        }
        function toggleUpdates() {
            const content = document.getElementById('content-updates');
            const arrow = document.getElementById('arrow-updates');
            content.classList.toggle('open');
            arrow.classList.toggle('open');
        }
        function toggleUpdate(id) {
            document.getElementById(`update-code-${id}`).classList.toggle('open');
        }
        async function deleteUpdate(id, title) {
            if (!isAdmin) {
                alert('Only admin@super.admin can delete updates.');
                return;
            }
            if (!confirm(`Hapus update "${title}"?`)) return;
            try {
                const res = await fetch(`/project/updates/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                if (res.ok) {
                    const el = document.getElementById(`update-${id}`);
                    if (el) el.remove();
                    const updatesCount = document.querySelector('#updates-section .stat-box .value');
                    if (updatesCount) updatesCount.textContent = parseInt(updatesCount.textContent) - 1;
                    alert('Update berhasil dihapus!');
                } else {
                    alert('Gagal menghapus update.');
                }
            } catch (e) {
                alert('Error: ' + e.message);
            }
        }
        function copyUpdateCode(id) {
            const code = document.querySelector(`#update-code-${id} code`).textContent;
            navigator.clipboard.writeText(code).then(() => {
                const btn = document.querySelector(`#update-code-${id} .copy-btn`);
                btn.textContent = 'Copied!';
                setTimeout(() => btn.textContent = 'Copy', 1500);
            });
        }
        function applySubmissionsUI() {
            [1, 2, 3, 4].forEach(id => {
                const data = repoSubmissions[id];
                const input = document.getElementById(`repo-${id}`);
                const status = document.getElementById(`status-${id}`);
                if (input) input.value = data?.url || '';
                if (status) {
                    if (data?.status === 'approved') { status.textContent = '✓ Approved'; status.className = 'badge badge-approved'; showSuccess(id, 'Repository approved!'); }
                    else if (data?.status === 'rejected') { status.textContent = '✗ Rejected'; status.className = 'badge badge-rejected'; showWarning(id, data.feedback || 'Please fix issues.'); }
                    else if (data?.status === 'submitted') { status.textContent = '⏳ Pending'; status.className = 'badge badge-pending'; }
                    else { status.textContent = 'Not Submitted'; status.className = 'badge badge-default'; }
                }
            });
        }
        function resetProgress() {
            if (confirm('Reset all progress?')) {
                completedSteps = new Set();
                repoSubmissions = {};
                syncToServer();
                location.reload();
            }
        }
        function submitRepo(id) {
            const url = document.getElementById(`repo-${id}`).value.trim();
            if (!url.includes('github.com') || url.length < 20) { showWarning(id, 'Enter a valid GitHub URL.'); return; }
            const start = (id - 1) * 200 + 1, end = id * 200;
            let done = 0;
            for (let i = start; i <= end; i++) if (completedSteps.has(i)) done++;
            if (done < 200) { showWarning(id, `Complete all steps first. (${done}/200)`); return; }
            repoSubmissions[id] = { url, status: 'submitted', feedback: '' };
            syncToServer();
            const status = document.getElementById(`status-${id}`);
            status.textContent = '⏳ Pending';
            status.className = 'badge badge-pending';
            hideWarning(id);
            alert('Submitted! Waiting for review.');
        }
        function approveRepo(id) {
            repoSubmissions[id] = { ...repoSubmissions[id], status: 'approved', feedback: '' };
            syncToServer();
            const status = document.getElementById(`status-${id}`);
            status.textContent = '✓ Approved';
            status.className = 'badge badge-approved';
            hideWarning(id);
            showSuccess(id, 'Approved!');
        }
        function rejectRepo(id) {
            const fb = document.getElementById(`feedback-${id}`)?.value || 'Fix issues.';
            repoSubmissions[id] = { ...repoSubmissions[id], status: 'rejected', feedback: fb };
            syncToServer();
            const status = document.getElementById(`status-${id}`);
            status.textContent = '✗ Rejected';
            status.className = 'badge badge-rejected';
            showWarning(id, fb);
        }
        function showWarning(id, msg) {
            const el = document.getElementById(`warning-${id}`);
            if (el) { el.innerHTML = '⚠️ ' + msg; el.style.display = 'block'; }
            const suc = document.getElementById(`success-${id}`);
            if (suc) suc.style.display = 'none';
        }
        function hideWarning(id) {
            const el = document.getElementById(`warning-${id}`);
            if (el) el.style.display = 'none';
        }
        function showSuccess(id, msg) {
            const el = document.getElementById(`success-${id}`);
            if (el) { el.innerHTML = '✓ ' + msg; el.style.display = 'block'; }
            const warn = document.getElementById(`warning-${id}`);
            if (warn) warn.style.display = 'none';
        }
    </script>
</body>
</html>