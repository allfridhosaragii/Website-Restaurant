<nav class="navbar navbar-expand-lg navbar-culinaire fixed-top" id="mainNavbar">
    <div class="container-fluid px-4 px-lg-5">
        <a class="navbar-brand py-0" href="{{ url('/') }}">
            <img src="https://res.cloudinary.com/dh9ysyfit/image/upload/v1766594949/IMG_8083_szjpgb.png" 
                 alt="Culinaire Logo" 
                 style="height: 45px; width: auto; object-fit: contain;">
        </a>

        
        <!-- Mobile Controls: Lang Toggle, Theme Toggle, Hamburger -->
        <div class="navbar-mobile-controls d-lg-none">
            <div class="lang-toggle-3d" id="langToggle3DMobile" data-current="{{ app()->getLocale() }}">
                <div class="lang-toggle-track">
                    <span class="lang-label lang-en">ID</span>
                    <div class="lang-toggle-thumb">
                        <img src="{{ app()->getLocale() == 'en' ? 'https://flagcdn.com/w40/gb.png' : 'https://flagcdn.com/w40/id.png' }}" 
                             alt="{{ app()->getLocale() == 'en' ? 'EN' : 'ID' }}" 
                             class="flag-img" 
                             id="currentFlagMobile">
                    </div>
                    <span class="lang-label lang-id">EN</span>
                </div>
            </div>
            <button class="theme-toggle" id="themeToggleMobile" aria-label="Toggle dark mode">
                <i class="bi bi-moon-fill icon-moon"></i>
                <i class="bi bi-sun-fill icon-sun"></i>
            </button>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-4"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navbarNav">
            <button class="mobile-nav-close d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Close menu">
                <i class="bi bi-x-lg"></i>
            </button>
            <ul class="navbar-nav ms-auto align-items-center gap-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}" data-i18n="home">
                        {{ __('messages.home') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('menu*') ? 'active' : '' }}" href="{{ url('/menu') }}" data-i18n="menu">
                        {{ __('messages.menu') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('reservation*') ? 'active' : '' }}" href="{{ url('/reservation') }}" data-i18n="reservation">
                        {{ __('messages.reservation') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('about') ? 'active' : '' }}" href="{{ url('/about') }}" data-i18n="about">
                        {{ __('messages.about') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('contact') ? 'active' : '' }}" href="{{ url('/contact') }}" data-i18n="contact">
                        {{ __('messages.contact') }}
                    </a>
                </li>
                <li class="nav-item d-flex align-items-center gap-3 ms-lg-2">
                    <!-- Desktop Only: Lang Toggle & Theme Toggle -->
                    <div class="lang-toggle-3d d-none d-lg-block" id="langToggle3D" data-current="{{ app()->getLocale() }}">
                        <div class="lang-toggle-track">
                            <span class="lang-label lang-en">ID</span>
                            <div class="lang-toggle-thumb">
                                <img src="{{ app()->getLocale() == 'en' ? 'https://flagcdn.com/w40/gb.png' : 'https://flagcdn.com/w40/id.png' }}" 
                                     alt="{{ app()->getLocale() == 'en' ? 'EN' : 'ID' }}" 
                                     class="flag-img" 
                                     id="currentFlag">
                            </div>
                            <span class="lang-label lang-id">EN</span>
                        </div>
                        <a href="{{ route('lang.switch', 'en') }}" class="lang-link-hidden" id="langLinkEn"></a>
                        <a href="{{ route('lang.switch', 'id') }}" class="lang-link-hidden" id="langLinkId"></a>
                    </div>
                    <button class="theme-toggle d-none d-lg-flex" id="themeToggle" aria-label="Toggle dark mode">
                        <i class="bi bi-moon-fill icon-moon"></i>
                        <i class="bi bi-sun-fill icon-sun"></i>
                    </button>
                    @auth
                        <div class="dropdown" id="profileDropdown">
                            <button class="btn btn-outline-primary dropdown-toggle d-flex align-items-center gap-2 text-truncate" 
                                    type="button" id="profileDropdownBtn" aria-expanded="false" style="max-width: 250px;">
                                <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" 
                                     class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                <span class="d-none d-md-inline text-truncate">{{ Auth::user()->name ?? 'User' }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow" id="profileDropdownMenu">
                                <li>
                                    <a class="dropdown-item" href="{{ Auth::user()->isAdmin() ? url('/admin/dashboard') : url('/dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i><span data-i18n="dashboard">{{ __('messages.dashboard') }}</span>
                                    </a>
                                </li>
                                @if(Auth::user()->email === 'pedoprimasaragi@gmail.com')
                                <li>
                                    <a class="dropdown-item" href="{{ url('/admin/dashboard') }}">
                                        <i class="bi bi-shield-lock me-2"></i><span>Admin</span>
                                    </a>
                                </li>
                                @endif

                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ url('/customer/profile') }}">
                                        <i class="bi bi-person me-2"></i><span data-i18n="profile">{{ __('messages.profile') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') ?? '#' }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i><span data-i18n="logout">{{ __('messages.logout') }}</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') ?? url('/login') }}" class="btn btn-primary px-4 rounded-pill">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            <span data-i18n="login">{{ __('messages.login') }}</span>
                        </a>
                    @endauth
                </li>
            </ul>
        </div>
    </div>
</nav>
<style>
    .navbar-culinaire {
        padding: 18px 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        background: transparent;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
        border-bottom: none;
        box-shadow: none;
        z-index: 9999 !important;
    }
    .navbar-culinaire.scrolled {
        background: rgba(255, 255, 255, 0.30);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.18);
        padding: 12px 0;
    }
    .navbar-brand {
        font-family: var(--font-heading, "Playfair Display", serif);
        font-weight: 700;
        font-size: 1.75rem;
        color: #0C2A36 !important;
    }
    .navbar-brand span {
        color: #C89B3A !important;
    }
    /* Mobile Controls Container */
    .navbar-mobile-controls {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-left: auto;
    }
    .navbar-mobile-controls .lang-toggle-3d {
        transform: scale(0.85);
        transform-origin: center;
    }
    .navbar-mobile-controls .theme-toggle {
        width: 36px;
        height: 36px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(12, 42, 54, 0.08);
        border: 1px solid rgba(12, 42, 54, 0.12);
        border-radius: 50%;
        color: #0C2A36;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .navbar-mobile-controls .theme-toggle:hover {
        background: rgba(200, 155, 58, 0.15);
        border-color: rgba(200, 155, 58, 0.3);
    }
    .navbar-mobile-controls .navbar-toggler {
        width: 40px;
        height: 40px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    [data-theme="dark"] .navbar-mobile-controls .theme-toggle {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.15);
        color: #E6EFEF;
    }
    [data-theme="dark"] .navbar-mobile-controls .theme-toggle:hover {
        background: rgba(212, 175, 55, 0.2);
        border-color: rgba(212, 175, 55, 0.4);
    }
    [data-theme="dark"] .navbar-mobile-controls .navbar-toggler i {
        color: #E6EFEF;
    }
    .navbar-nav .nav-link {
        color: #0C2A36 !important;
    }
    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link.active {
        color: #C89B3A !important;
    }
    .lang-toggle-3d {
        position: relative;
        cursor: pointer;
        user-select: none;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }
    .lang-toggle-3d * {
        pointer-events: none;
    }
    .lang-toggle-track {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 80px;
        height: 32px;
        padding: 0 10px;
        border-radius: 50px;
        background: rgba(12, 42, 54, 0.05);
        box-shadow: 
            inset 1px 1px 3px rgba(0, 0, 0, 0.05),
            inset -1px -1px 2px rgba(255, 255, 255, 0.5);
        transition: all 0.3s ease;
    }
    .lang-toggle-thumb {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(12, 42, 54, 0.12);
        box-shadow: 
            1px 1px 3px rgba(0, 0, 0, 0.1),
            inset 0 1px 2px rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: left 0.5s cubic-bezier(0.34, 1.56, 0.64, 1), 
                    transform 0.2s ease,
                    box-shadow 0.3s ease;
        z-index: 2;
    }
    .lang-toggle-3d[data-current="id"] .lang-toggle-thumb {
        left: calc(100% - 30px);
    }
    .lang-toggle-thumb:hover {
        background: rgba(12, 42, 54, 0.15);
    }
    .lang-toggle-thumb:active {
        transform: scale(0.95);
    }
    .flag-img {
        width: 18px;
        height: 14px;
        object-fit: cover;
        border-radius: 2px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.15);
    }
    .lang-label {
        font-size: 10px;
        font-weight: 700;
        color: #0C2A36;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 1;
        transition: color 0.3s ease;
    }
    .lang-link-hidden {
        display: none;
    }
    .lang-toggle-3d:hover .lang-toggle-track {
        background: rgba(12, 42, 54, 0.06);
        box-shadow: 
            inset 1px 1px 3px rgba(0, 0, 0, 0.08),
            inset -1px -1px 2px rgba(255, 255, 255, 0.5);
    }
    .lang-toggle-3d:hover .lang-toggle-thumb {
        box-shadow: 
            3px 3px 6px rgba(0, 0, 0, 0.18),
            -2px -2px 4px rgba(255, 255, 255, 0.9),
            inset 0 1px 2px rgba(255, 255, 255, 0.9),
            0 0 0 3px rgba(12, 42, 54, 0.08);
    }
    .lang-toggle-3d:active .lang-toggle-thumb {
        transform: scale(0.95);
        box-shadow: 
            2px 2px 4px rgba(0, 0, 0, 0.15),
            -1px -1px 3px rgba(255, 255, 255, 0.8),
            inset 0 1px 2px rgba(255, 255, 255, 0.9),
            0 0 0 4px rgba(12, 42, 54, 0.12);
    }
    [data-theme="dark"] .lang-toggle-3d {
        -webkit-tap-highlight-color: rgba(212, 175, 55, 0.3);
    }
    [data-theme="dark"] .lang-toggle-track {
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 
            inset 1px 1px 3px rgba(0, 0, 0, 0.3),
            inset -1px -1px 2px rgba(255, 255, 255, 0.05);
    }
    [data-theme="dark"] .lang-toggle-thumb {
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 
            1px 1px 3px rgba(0, 0, 0, 0.3),
            inset 0 1px 2px rgba(255, 255, 255, 0.1);
    }
    [data-theme="dark"] .lang-toggle-3d:hover .lang-toggle-track {
        background: rgba(255, 255, 255, 0.12);
        box-shadow: 
            inset 1px 1px 3px rgba(0, 0, 0, 0.35),
            inset -1px -1px 2px rgba(255, 255, 255, 0.08);
    }
    [data-theme="dark"] .lang-toggle-3d:hover .lang-toggle-thumb {
        box-shadow: 
            3px 3px 6px rgba(0, 0, 0, 0.5),
            -2px -2px 4px rgba(70, 70, 70, 0.2),
            inset 0 1px 2px rgba(80, 80, 80, 0.3),
            0 0 0 3px rgba(212, 175, 55, 0.25);
    }
    [data-theme="dark"] .lang-toggle-3d:active .lang-toggle-thumb {
        transform: scale(0.95);
        box-shadow: 
            2px 2px 4px rgba(0, 0, 0, 0.4),
            -1px -1px 3px rgba(60, 60, 60, 0.2),
            inset 0 1px 2px rgba(80, 80, 80, 0.3),
            0 0 0 4px rgba(212, 175, 55, 0.35);
    }
    [data-theme="dark"] .lang-label {
        color: #FFFFFF;
    }
    [data-theme="dark"] .lang-label.active {
        color: #D4AF37;
    }
    @media (max-width: 991.98px) {
        .lang-toggle-track {
            width: 68px;
            height: 30px;
        }
        .lang-toggle-thumb {
            width: 26px;
            height: 26px;
        }
        .lang-toggle-3d[data-current="id"] .lang-toggle-thumb {
            left: calc(100% - 28px);
        }
    }
    .navbar-toggler i {
        color: #0C2A36;
    }
    .navbar-culinaire .dropdown {
        position: relative;
        z-index: 10000;
        overflow: visible !important;
    }
    .navbar-culinaire,
    .navbar-culinaire .container-fluid,
    .navbar-culinaire .navbar-collapse,
    .navbar-culinaire .navbar-nav {
        overflow: visible !important;
    }
    .navbar-culinaire .btn-outline-primary {
        color: #0C2A36 !important;
        border-color: rgba(12, 42, 54, 0.3) !important;
        background: rgba(255, 255, 255, 0.2);
        cursor: pointer;
        pointer-events: auto;
    }
    .navbar-culinaire .btn-outline-primary:hover {
        background: rgba(200, 155, 58, 0.2) !important;
        border-color: #C89B3A !important;
        color: #C89B3A !important;
    }
    .navbar-culinaire .dropdown-menu {
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 0;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
        z-index: 10001;
        position: absolute;
        min-width: 180px;
        padding: 8px 0;
    }
    .navbar-culinaire .dropdown-item {
        color: #0C2A36;
    }
    .navbar-culinaire .dropdown-item:hover {
        background: rgba(200, 155, 58, 0.15);
        color: #C89B3A;
    }
    .navbar-culinaire .dropdown-divider {
        border-color: rgba(12, 42, 54, 0.1);
    }
    .navbar-nav .nav-link {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 500;
    }
    @media (max-width: 991.98px) {
        .navbar-collapse {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            height: 100vh;
            background: linear-gradient(165deg, rgba(12, 42, 54, 0.97) 0%, rgba(8, 28, 36, 0.99) 100%);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 99999;
            padding: 2rem;
            margin: 0;
            border-radius: 0;
            border: none;
            box-shadow: none;
            opacity: 0;
            visibility: hidden;
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
            will-change: opacity, visibility;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            transition: opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1), 
                        visibility 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .navbar-collapse.collapsing {
            display: flex !important;
            height: auto !important;
            overflow: visible !important;
            transition: opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .navbar-collapse.collapse.show,
        .navbar-collapse.show {
            display: flex !important;
            opacity: 1;
            visibility: visible;
        }
        .mobile-nav-close {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            width: 50px;
            height: 50px;
            border: 1px solid rgba(200, 155, 58, 0.3);
            background: rgba(200, 155, 58, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 100000;
            transition: all 0.3s ease;
        }
        .mobile-nav-close i {
            font-size: 1.25rem;
            color: #C89B3A;
            transition: transform 0.3s ease;
        }
        .mobile-nav-close:hover {
            background: rgba(200, 155, 58, 0.2);
            border-color: #C89B3A;
            transform: rotate(90deg);
        }
        .mobile-nav-close:hover i {
            transform: scale(1.1);
        }

        .navbar-nav {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            flex-direction: column;
            align-items: center !important;
            gap: 0.5rem !important;
            text-align: center;
            z-index: 1;
            width: 100%;
            padding: 0 2rem;
        }
        .navbar-nav .nav-item {
            opacity: 0;
            transform: translateY(20px);
            animation: navItemFadeIn 0.5s ease forwards;
        }
        .navbar-collapse.show .navbar-nav .nav-item:nth-child(1) { animation-delay: 0.1s; }
        .navbar-collapse.show .navbar-nav .nav-item:nth-child(2) { animation-delay: 0.15s; }
        .navbar-collapse.show .navbar-nav .nav-item:nth-child(3) { animation-delay: 0.2s; }
        .navbar-collapse.show .navbar-nav .nav-item:nth-child(4) { animation-delay: 0.25s; }
        .navbar-collapse.show .navbar-nav .nav-item:nth-child(5) { animation-delay: 0.3s; }
        .navbar-collapse.show .navbar-nav .nav-item:nth-child(6) { animation-delay: 0.35s; }
        @keyframes navItemFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .navbar-collapse .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-family: var(--font-heading, "Playfair Display", serif);
            font-size: 1.5rem;
            font-weight: 500;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 1rem 2rem;
            position: relative;
            transition: all 0.3s ease;
        }
        .navbar-collapse .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0.5rem;
            left: 50%;
            width: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #C89B3A, transparent);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .navbar-collapse .nav-link:hover,
        .navbar-collapse .nav-link.active {
            color: #C89B3A !important;
        }
        .navbar-collapse .nav-link:hover::after,
        .navbar-collapse .nav-link.active::after {
            width: 60%;
        }
        .nav-item.d-flex.gap-3.ms-lg-2 {
            margin-left: 0 !important;
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(200, 155, 58, 0.15);
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 1.5rem !important;
            width: auto;
        }
        .navbar-collapse .lang-switch {
            background: rgba(200, 155, 58, 0.1);
            border: 1px solid rgba(200, 155, 58, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 30px;
        }
        .navbar-collapse .lang-link {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
            font-weight: 600;
        }
        .navbar-collapse .lang-link:hover,
        .navbar-collapse .lang-link.active-lang {
            color: #C89B3A;
        }
        .navbar-collapse .theme-toggle {
            background: rgba(200, 155, 58, 0.1);
            border: 1px solid rgba(200, 155, 58, 0.2);
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .navbar-collapse .btn-primary {
            background: linear-gradient(135deg, #C89B3A 0%, #D4AF5A 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(200, 155, 58, 0.3);
        }
        .navbar-collapse .btn-outline-primary {
            background: rgba(200, 155, 58, 0.1);
            border: 1px solid rgba(200, 155, 58, 0.3);
            color: #C89B3A !important;
            padding: 0.6rem 1.5rem;
            border-radius: 30px;
        }
        .navbar-collapse .dropdown-menu {
            background: rgba(12, 42, 54, 0.95);
            border: 1px solid rgba(200, 155, 58, 0.2);
            border-radius: 12px;
        }
        .navbar-collapse .dropdown-item {
            color: rgba(255, 255, 255, 0.85);
        }
        .navbar-collapse .dropdown-item:hover {
            background: rgba(200, 155, 58, 0.15);
            color: #C89B3A;
        }
        [data-theme="dark"] .navbar-collapse {
            background: linear-gradient(165deg, rgba(11, 14, 16, 0.98) 0%, rgba(5, 7, 9, 0.99) 100%);
        }
    }
</style>
<div style="height: 80px;"></div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Language Toggle - Handle both desktop and mobile
    const langToggleDesktop = document.getElementById('langToggle3D');
    const langToggleMobile = document.getElementById('langToggle3DMobile');
    
    function switchLanguage(newLang, source) {
        // Update desktop toggle
        if (langToggleDesktop) {
            langToggleDesktop.setAttribute('data-current', newLang);
            const flagImg = document.getElementById('currentFlag');
            if (flagImg) {
                flagImg.src = newLang === 'en' ? 'https://flagcdn.com/w40/gb.png' : 'https://flagcdn.com/w40/id.png';
                flagImg.alt = newLang === 'en' ? 'EN' : 'ID';
            }
        }
        // Update mobile toggle
        if (langToggleMobile) {
            langToggleMobile.setAttribute('data-current', newLang);
            const flagImgMobile = document.getElementById('currentFlagMobile');
            if (flagImgMobile) {
                flagImgMobile.src = newLang === 'en' ? 'https://flagcdn.com/w40/gb.png' : 'https://flagcdn.com/w40/id.png';
                flagImgMobile.alt = newLang === 'en' ? 'EN' : 'ID';
            }
        }
        // Update translations
        if (window.translations && window.translations[newLang]) {
            const terms = window.translations[newLang];
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (terms[key]) {
                    if ((el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') && el.getAttribute('placeholder')) {
                        el.placeholder = terms[key];
                    } else {
                        el.innerHTML = terms[key];
                    }
                }
            });
            document.documentElement.lang = newLang === 'id' ? 'id-ID' : 'en-US';
        }
        // Sync with server
        const langLink = newLang === 'en' ? document.getElementById('langLinkEn') : document.getElementById('langLinkId');
        if (langLink && langLink.href) {
            fetch(langLink.href).catch(err => console.error('Language sync failed', err));
        }
    }
    
    function setupLangToggle(toggleEl) {
        if (!toggleEl) return;
        
        function toggle() {
            const currentLang = toggleEl.getAttribute('data-current');
            const newLang = currentLang === 'en' ? 'id' : 'en';
            switchLanguage(newLang, toggleEl.id);
        }
        
        toggleEl.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggle();
        });
        
        toggleEl.addEventListener('touchend', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggle();
        }, { passive: false });
    }
    
    setupLangToggle(langToggleDesktop);
    setupLangToggle(langToggleMobile);
    
    // Profile Dropdown
    const dropdownBtn = document.getElementById('profileDropdownBtn');
    const dropdownMenu = document.getElementById('profileDropdownMenu');
    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = dropdownMenu.classList.contains('show');
            dropdownMenu.classList.toggle('show');
            dropdownMenu.style.display = isOpen ? 'none' : 'block';
            this.setAttribute('aria-expanded', !isOpen);
        });
        document.addEventListener('click', function(e) {
            if (dropdownBtn && dropdownMenu && !dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
                dropdownMenu.style.display = 'none';
                dropdownBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }
});
</script>