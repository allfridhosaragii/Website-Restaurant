@extends('layouts.guest')

@section('title', 'Our Journey • Culinaire')

@section('content')
<div class="smooth-scroll-wrapper">
    
    <!-- 1. Hero Section: Sticky & Fade -->
    <section class="about-hero" id="hero">
        <div class="hero-sticky-content">
            <div class="hero-bg" style="background-image: url('https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?q=80&w=1920&auto=format&fit=crop');"></div>
            <div class="hero-overlay"></div>
            <div class="hero-text">
                <h1 class="font-display display-giant" data-speed="0.5">THE LEGACY</h1>
                <p class="font-mono tracking-widest text-gold mt-3" data-speed="0.3">EST. 2009 - 2025</p>
                <div class="scroll-hint">
                    <span>SCROLL TO EXPLORE</span>
                    <div class="line"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. The Timeline -->
    <div class="timeline-container">
        <div class="timeline-line">
            <div class="timeline-progress"></div>
        </div>

        <!-- Year 2009: The Beginning -->
        <section class="timeline-section" data-year="2009">
            <div class="year-marker">
                <span class="year-text font-display">2009</span>
            </div>
            <div class="section-content">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="text-block reveal-text">
                            <h2 class="font-display text-gold mb-3">The Foundation</h2>
                            <p class="text-white-50 lead">
                                It started with a single flame. In a small, rustic corner of the city, Culinaire was born not as a restaurant, but as a promise to honor traditional flavors. We had 5 tables, a wood-fired oven, and a dream.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="image-block reveal-image right-tilt">
                            <img src="https://images.unsplash.com/photo-1516455590571-18256e5bb9ff?w=800&q=80" alt="Rustic Beginning">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Year 2014: The Expansion (Horizontal Scroll Trigger) -->
        <section class="timeline-section" data-year="2014">
            <div class="year-marker left">
                <span class="year-text font-display">2014</span>
            </div>
            <div class="section-content">
                <div class="half-scroll-intro mb-5 text-center reveal-text">
                    <h2 class="font-display text-white">The Expansion</h2>
                    <p class="text-gold font-mono">Exploring new horizons</p>
                </div>
                <div class="horizontal-scroll-container">
                    <div class="horizontal-track">
                        <div class="h-item">
                            <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?w=600&q=80" alt="Expansion 1">
                            <span class="caption font-mono">Opened 2nd Hall</span>
                        </div>
                        <div class="h-item">
                            <img src="https://images.unsplash.com/photo-1550966871-3ed3c47e2ce2?w=600&q=80" alt="Expansion 2">
                            <span class="caption font-mono">Michelin Star</span>
                        </div>
                        <div class="h-item">
                            <img src="https://images.unsplash.com/photo-1544025162-d76690b67f11?w=600&q=80" alt="Expansion 3">
                            <span class="caption font-mono">European Tour</span>
                        </div>
                         <div class="h-item text-card">
                            <h3 class="font-display text-gold">Going Global</h3>
                            <p>We crossed borders, bringing our signature taste to the world stage.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Year 2019: The Refinement -->
        <section class="timeline-section" data-year="2019">
             <div class="year-marker">
                <span class="year-text font-display">2019</span>
            </div>
            <div class="section-content">
                 <div class="row flex-row-reverse align-items-center">
                    <div class="col-lg-6">
                        <div class="text-block reveal-text text-lg-end">
                            <h2 class="font-display text-gold mb-3">The Refinement</h2>
                            <p class="text-white-50 lead">
                                Precision became our obsession. We redefined our menu, sourcing ingredients from exclusive local farms. Every dish became a canvas, every meal a masterpiece of culinary architecture.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                         <div class="image-block reveal-image left-tilt">
                            <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&q=80" alt="Fine Dining">
                        </div>
                    </div>
                </div>
            </div>
        </section>

         <!-- Year 2025: The Future -->
        <section class="timeline-section last-chapter" data-year="2025">
             <div class="year-marker">
                <span class="year-text font-display text-gold">2025</span>
            </div>
            <div class="section-content text-center">
                <div class="future-block reveal-scale">
                    <h2 class="font-display display-3 text-white mb-4">The Future is Here</h2>
                    <p class="text-white-50 lead w-75 mx-auto mb-5">
                        Integrating sustainability with technology. We are building the dining experience of tomorrow, today. Join us in the next chapter of our legacy.
                    </p>
                    <div class="image-grid-future">
                         <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&q=80" class="f-img-1" alt="Future 1">
                         <img src="https://images.unsplash.com/photo-1592861956120-e524fc739696?w=600&q=80" class="f-img-2" alt="Future 2">
                         <img src="https://images.unsplash.com/photo-1552566626-52f8b828add9?w=600&q=80" class="f-img-3" alt="Future 3">
                    </div>
                </div>
            </div>
        </section>

        <section class="footer-quote text-center py-5 my-5">
             <h3 class="font-display text-white-50">"Taste the History"</h3>
        </section>

    </div>
</div>

<style>
    /* VARIABLES */
    :root {
        --bg-color: #0B0E14;
        --card-bg: #151A23;
        --gold: #C89B3A;
        --text-muted: rgba(255,255,255,0.6);
        --ease-out-expo: cubic-bezier(0.19, 1, 0.22, 1);
    }

    html.lenis {
        height: auto;
    }

    body {
        background-color: var(--bg-color);
        overflow-x: hidden;
        color: white;
    }

    /* TYPOGRAPHY */
    .display-giant {
        font-size: clamp(4rem, 15vw, 12rem);
        font-weight: 700;
        letter-spacing: -0.03em;
        line-height: 0.9;
        color: #fff;
    }
    .font-display { font-family: 'Playfair Display', serif; }
    .font-mono { font-family: 'Space Mono', monospace; }

    /* UTILS */
    .text-gold { color: var(--gold); }
    .text-white-50 { color: var(--text-muted); }
    
    /* ANIMATIONS */
    .reveal-text {
        opacity: 0;
        transform: translateY(40px);
        transition: all 1s var(--ease-out-expo);
    }
    .active-section .reveal-text {
        opacity: 1;
        transform: translateY(0);
    }

    /* Curtain Image Reveal */
    .reveal-image-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 4px;
        opacity: 0;
        transform: scale(0.95);
        transition: all 1s var(--ease-out-expo);
    }
    .reveal-image-wrapper::after {
        content: '';
        position: absolute;
        inset: 0;
        background: var(--gold);
        transform: scaleY(1);
        transform-origin: bottom;
        transition: transform 0.8s var(--ease-out-expo) 0.2s;
        z-index: 2;
    }
    .active-section .reveal-image-wrapper {
        opacity: 1;
        transform: scale(1);
    }
    .active-section .reveal-image-wrapper::after {
        transform: scaleY(0);
        transform-origin: top;
    }
    .reveal-image-wrapper img {
        width: 100%;
        display: block;
        transform: scale(1.2);
        transition: transform 1.5s var(--ease-out-expo);
    }
    .active-section .reveal-image-wrapper img {
        transform: scale(1);
    }

    /* HERO */
    .about-hero {
        height: 100vh;
        width: 100%;
        position: relative;
        z-index: 1;
    }
    .hero-sticky-content {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100vh;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        z-index: -1;
    }
    .hero-bg {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background-size: cover; background-position: center;
        opacity: 0.4;
    }
    
    /* TIMELINE */
    .timeline-container {
        position: relative;
        z-index: 10;
        background: var(--bg-color);
        padding-top: 100px;
        margin-top: 100vh;
        box-shadow: 0 -50px 100px rgba(0,0,0,0.8);
    }
    .timeline-line-center {
        position: absolute;
        top: 0; bottom: 0; left: 50%;
        width: 1px; background: rgba(255,255,255,0.1);
    }
    .timeline-progress {
        position: fixed;
        top: 50%; right: 40px;
        width: 2px; height: 0;
        max-height: 100px;
        background: var(--gold);
        z-index: 50;
    }

    /* SECTIONS */
    .timeline-section {
        min-height: 100vh;
        display: flex;
        align-items: center;
        position: relative;
        padding: 100px 0;
    }

    /* HORIZONTAL SCROLL SECTION */
    .sticky-wrapper {
        height: 400vh; /* Make it tall to allow scrolling */
        position: relative;
    }
    .sticky-content {
        position: sticky;
        top: 0;
        height: 100vh;
        display: flex;
        align-items: center;
        overflow: hidden;
        background: #0f1219;
    }
    .horiz-track {
        display: flex;
        gap: 8vh;
        padding-left: 10vw;
        will-change: transform;
    }
    .horiz-item {
        width: 60vw;
        max-width: 600px;
        flex-shrink: 0;
        transform: scale(0.9);
        transition: transform 0.5s;
        opacity: 0.3;
    }
    .horiz-item.active {
        transform: scale(1);
        opacity: 1;
    }
    .horiz-item img {
        width: 100%;
        height: 500px;
        object-fit: cover;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    /* YEAR MARKER */
    .year-floating {
        position: absolute;
        left: 50%; top: 50%;
        transform: translate(-50%, -50%);
        font-size: 20vw;
        font-weight: 700;
        color: rgba(255,255,255,0.03);
        z-index: -1;
        font-family: 'Playfair Display', serif;
        pointer-events: none;
        transition: transform 0.2s ease-out;
    }

    @media (max-width: 768px) {
        .timeline-line-center { display: none; }
        .display-giant { font-size: 18vw; }
        .sticky-wrapper { height: auto; }
        .sticky-content { position: relative; height: auto; display: block; overflow-x: auto; padding: 50px 0; }
        .horiz-track { padding-left: 20px; flex-direction: column; gap: 50px; }
        .horiz-item { width: 100%; opacity: 1; transform: none; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. HERO PARALLAX
    const heroContent = document.querySelector('.hero-sticky-content');
    const heroText = document.querySelector('.hero-text');
    
    // 2. TIMELINE SECTIONS
    const sections = document.querySelectorAll('.timeline-section');
    const years = document.querySelectorAll('.year-floating');

    // 3. HORIZONTAL SECTION
    const stickyWrapper = document.querySelector('.sticky-wrapper');
    const stickyContent = document.querySelector('.sticky-content');
    const horizTrack = document.querySelector('.horiz-track');
    const horizItems = document.querySelectorAll('.horiz-item');

    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;
        const winH = window.innerHeight;

        // HERO LOGIC
        if(scrollY < winH) {
            heroContent.style.opacity = 1 - (scrollY / winH);
            heroContent.style.transform = `translateY(${scrollY * 0.3}px)`;
            heroText.style.transform = `scale(${1 + scrollY * 0.0005})`;
        }

        // TEXT REVEAL LOGIC & PARALLAX YEAR
        sections.forEach((sec, i) => {
            const rect = sec.getBoundingClientRect();
            // Reveal trigger
            if(rect.top < winH * 0.75) {
                sec.classList.add('active-section');
            }
            // Parallax Year
            const year = sec.querySelector('.year-floating');
            if(year) {
                const speed = 0.1;
                const offset = (rect.top - winH/2) * speed;
                year.style.transform = `translate(-50%, calc(-50% + ${offset}px))`;
            }
        });

        // HORIZONTAL SCROLL LOGIC
        if(stickyWrapper && window.innerWidth > 768) {
            const stickyRect = stickyWrapper.getBoundingClientRect();
            const stickyTop = stickyRect.top;
            const stickyHeight = stickyWrapper.offsetHeight;
            const scrollDist = stickyHeight - winH;
            
            // Calculate progress (0 to 1)
            let progress = -stickyTop / scrollDist;
            
            // Limit progress between 0 and 1 for strict clamping inside the section
            // Use slightly extended range to catch start/end smoothly
            
            if(stickyTop <= 0 && -stickyTop < scrollDist) {
               // We are scrolling inside the sticky area
               const translateX = progress * (horizTrack.scrollWidth - window.innerWidth + 200); // 200px buffer
               horizTrack.style.transform = `translateX(-${translateX}px)`;
               
               // Highlight active item based on progress
               horizItems.forEach((item, idx) => {
                   const itemProgress = idx / (horizItems.length - 1);
                   if (Math.abs(progress - itemProgress) < 0.2) {
                       item.classList.add('active');
                   } else {
                       item.classList.remove('active');
                   }
               });
            } else if (stickyTop > 0) {
                horizTrack.style.transform = `translateX(0)`;
            }
        }
    });

    // Trigger explicit fade in for hero text on load
    setTimeout(() => {
        document.querySelector('.hero-text').style.opacity = '1';
    }, 100);
});
</script>
@endsection