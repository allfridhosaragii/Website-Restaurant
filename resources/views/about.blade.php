@extends('layouts.guest')

@section('title', 'The Legacy • Culinaire')

@section('content')
<div class="smooth-wrapper">
    
    <!-- HERO SECTION: THE LEGACY (Zoom Out Effect) -->
    <section class="about-hero-section" id="hero">
        <div class="hero-bg" style="background-image: url('https://images.unsplash.com/photo-1544148103-0773bf10d330?q=80&w=1920&auto=format&fit=crop');"></div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="display-super" data-speed="0.2">THE LEGACY</h1>
            <p class="subtitle font-mono" data-speed="0.1">A JOURNEY OF TASTE (2009 - 2025)</p>
            <div class="scroll-indicator">
                <div class="mouse"></div>
                <span>SCROLL TO BEGIN</span>
            </div>
        </div>
    </section>

    <!-- ERA 1: THE FOUNDATIONS (2009-2012) - Vertical Parallax -->
    <section class="era-section vertical-era" id="era-1">
        <div class="container-fluid px-5">
            <div class="era-header text-center mb-5" data-aos="fade-up">
                <span class="era-label font-mono text-gold">CHAPTER I</span>
                <h2 class="display-3 text-white">THE FOUNDATIONS</h2>
            </div>
            
            <!-- 2009 -->
            <div class="year-row" data-year="2009">
                <div class="year-content left">
                    <h3 class="year-title text-gold font-display">2009</h3>
                    <h4 class="text-white">The Spark</h4>
                    <p class="text-muted">In a small rustic corner, the first flame involved. A wood-fired oven and a dream to bring authentic flavors back to life.</p>
                </div>
                <div class="year-image right parallax-img">
                    <img src="https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?w=600&q=80" alt="2009">
                </div>
            </div>

            <!-- 2010 -->
            <div class="year-row reverse" data-year="2010">
                <div class="year-content right">
                    <h3 class="year-title text-gold font-display">2010</h3>
                    <h4 class="text-white">First Family</h4>
                    <p class="text-muted">We grew from 3 to 15. The kitchen became a symphony of passion, recruiting the finest local talent.</p>
                </div>
                <div class="year-image left parallax-img">
                    <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?w=600&q=80" alt="2010">
                </div>
            </div>

            <!-- 2011 -->
            <div class="year-row" data-year="2011">
                <div class="year-content left">
                    <h3 class="year-title text-gold font-display">2011</h3>
                    <h4 class="text-white">Local Recognition</h4>
                    <p class="text-muted">Named "Best Newcomer" by City Eats. Lines started forming around the block.</p>
                </div>
                <div class="year-image right parallax-img">
                    <img src="https://images.unsplash.com/photo-1550966871-3ed3c47e2ce2?w=600&q=80" alt="2011">
                </div>
            </div>

            <!-- 2012 -->
            <div class="year-row reverse" data-year="2012">
                <div class="year-content right">
                    <h3 class="year-title text-gold font-display">2012</h3>
                    <h4 class="text-white">The Renovation</h4>
                    <p class="text-muted">We broke down walls. Expanding our dining hall to welcome the growing community of food lovers.</p>
                </div>
                <div class="year-image left parallax-img">
                    <img src="https://images.unsplash.com/photo-1516455590571-18256e5bb9ff?w=600&q=80" alt="2012">
                </div>
            </div>
        </div>
    </section>

    <!-- ERA 2: THE GOLDEN ERA (2013-2017) - Horizontal Sticky Scroll -->
    <section class="horizontal-wrapper">
        <div class="horizontal-sticky">
            <div class="horizontal-intro">
                <span class="era-label font-mono text-gold d-block mb-2">CHAPTER II</span>
                <h2 class="display-3 text-white">THE GOLDEN ERA</h2>
                <p class="text-muted">Expansion & Excellence</p>
                <div class="scroll-arrow">→</div>
            </div>
            <div class="horizontal-track">
                <!-- 2013 -->
                <div class="h-card">
                    <div class="h-year">2013</div>
                    <div class="h-img"><img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&q=80" alt="2013"></div>
                    <div class="h-desc">
                        <h5>A Star is Born</h5>
                        <p>Our first major culinary award. The Gold Spoon.</p>
                    </div>
                </div>
                <!-- 2014 -->
                <div class="h-card">
                    <div class="h-year">2014</div>
                    <div class="h-img"><img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?w=600&q=80" alt="2014"></div>
                    <div class="h-desc">
                        <h5>Second Location</h5>
                        <p>Opening our doors in the heart of the capital.</p>
                    </div>
                </div>
                 <!-- 2015 -->
                <div class="h-card">
                    <div class="h-year">2015</div>
                    <div class="h-img"><img src="https://images.unsplash.com/photo-1544025162-d76690b67f11?w=600&q=80" alt="2015"></div>
                    <div class="h-desc">
                        <h5>International Tour</h5>
                        <p>Our chefs traveled to Europe to master new techniques.</p>
                    </div>
                </div>
                 <!-- 2016 -->
                <div class="h-card">
                    <div class="h-year">2016</div>
                    <div class="h-img"><img src="https://images.unsplash.com/photo-1552566626-52f8b828add9?w=600&q=80" alt="2016"></div>
                    <div class="h-desc">
                        <h5>The Wine Cellar</h5>
                        <p>Introducing a collection of 500+ vintage wines.</p>
                    </div>
                </div>
                 <!-- 2017 -->
                <div class="h-card">
                    <div class="h-year">2017</div>
                    <div class="h-img"><img src="https://images.unsplash.com/photo-1590846406792-0adc7f938f1d?w=600&q=80" alt="2017"></div>
                    <div class="h-desc">
                        <h5>Chef's Table</h5>
                        <p>Launching the exclusive private dining experience.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ERA 3: RESILIENCE (2018-2021) - Dark Spotlight Reveal -->
    <section class="spotlight-section">
        <div class="container small-container">
            <div class="text-center mb-5 fade-in">
                <span class="era-label font-mono text-gold">CHAPTER III</span>
                <h2 class="display-3 text-white">RESILIENCE</h2>
            </div>

            <!-- 2018 -->
            <div class="spotlight-item" data-year="2018">
                <div class="spotlight-year">2018</div>
                <div class="spotlight-content">
                    <h3>Sustainable Shift</h3>
                    <p>We completely overhauled our supply chain to support local farmers and 100% organic produce.</p>
                </div>
            </div>

            <!-- 2019 -->
             <div class="spotlight-item" data-year="2019">
                <div class="spotlight-year">2019</div>
                <div class="spotlight-content">
                    <h3>The Decade Mark</h3>
                    <p>Celebrating 10 years of culinary excellence with a gala that gathered chefs from around the world.</p>
                </div>
            </div>

            <!-- 2020 -->
             <div class="spotlight-item highlight" data-year="2020">
                <div class="spotlight-year">2020</div>
                <div class="spotlight-content">
                    <h3>Standing Strong</h3>
                    <p>When the world stopped, our ovens kept burning. We served 50,000 meals to frontline heroes.</p>
                </div>
            </div>

            <!-- 2021 -->
             <div class="spotlight-item" data-year="2021">
                <div class="spotlight-year">2021</div>
                <div class="spotlight-content">
                    <h3>Digital Rebirth</h3>
                    <p>Launching Culinaire Home, bringing our signature dining experience to your dining table.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ERA 4: THE VISION (2022-2025) - Stacking Cards -->
    <section class="stacking-section">
        <div class="sticky-stack-wrapper">
            <div class="stack-header text-center mb-5">
                <span class="era-label font-mono text-gold">CHAPTER IV</span>
                <h2 class="display-3 text-white">THE VISION</h2>
            </div>
            
            <div class="cards-container">
                <!-- 2022 -->
                <div class="stack-card bg-darker" style="--index: 1;">
                    <div class="card-inner">
                        <div class="card-left">
                            <h2 class="text-gold">2022</h2>
                            <h3>Technological Harmony</h3>
                            <p>Integrating AI precision with human artistry in our kitchens.</p>
                        </div>
                        <div class="card-right">
                             <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&q=80" alt="2022">
                        </div>
                    </div>
                </div>
                
                <!-- 2023 -->
                <div class="stack-card bg-darker" style="--index: 2;">
                    <div class="card-inner">
                        <div class="card-left">
                            <h2 class="text-white">2023</h2>
                            <h3>Global Franchise</h3>
                            <p>Our vision expanded to Tokyo, London, and New York.</p>
                        </div>
                         <div class="card-right">
                             <img src="https://images.unsplash.com/photo-1592861956120-e524fc739696?w=600&q=80" alt="2023">
                        </div>
                    </div>
                </div>

                <!-- 2024 -->
                <div class="stack-card bg-darker" style="--index: 3;">
                    <div class="card-inner">
                        <div class="card-left">
                            <h2 class="text-gold">2024</h2>
                            <h3>Zero Waste</h3>
                            <p>Achieving our goal of becoming the first Zero Waste fine dining establishment.</p>
                        </div>
                         <div class="card-right">
                             <img src="https://images.unsplash.com/photo-1600093463592-8e36ae95ef56?w=600&q=80" alt="2024">
                        </div>
                    </div>
                </div>

                <!-- 2025 -->
                <div class="stack-card bg-gold text-dark last-card" style="--index: 4;">
                    <div class="card-inner">
                        <div class="card-left">
                            <h2 class="text-dark">2025</h2>
                            <h3>The Next Chapter</h3>
                            <p class="lead">We are just getting started. The legacy continues with you.</p>
                        </div>
                         <div class="card-right">
                             <img src="https://images.unsplash.com/photo-1550966871-3ed3c47e2ce2?w=600&q=80" alt="2025" style="filter: none;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="footer-spacer py-5 text-center">
        <h3 class="font-display text-muted">EST. 2009</h3>
    </section>

</div>

<style>
/* CORE SETTINGS */
:root {
    --bg-primary: #0B0E14;
    --bg-secondary: #0F1219;
    --gold: #C89B3A;
    --text-main: #ffffff;
    --text-muted: rgba(255,255,255,0.6);
}
body { background: var(--bg-primary); color: var(--text-main); font-family: 'Inter', sans-serif; overflow-x: hidden; }
.font-display { font-family: 'Playfair Display', serif; }
.font-mono { font-family: 'Space Mono', monospace; }
.display-super { font-size: clamp(3rem, 12vw, 10rem); font-weight: 700; line-height: 0.9; letter-spacing: -2px; }
.smooth-wrapper { width: 100%; overflow: hidden; }

/* 1. HERO */
.about-hero-section {
    height: 100vh; width: 100%; position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden;
}
.hero-bg {
    position: absolute; inset: 0; background-size: cover; background-position: center; z-index: 0;
    transition: transform 0.1s linear;
}
.hero-overlay { position: absolute; inset: 0; background: rgba(11,14,20,0.5); z-index: 1; }
.hero-content { position: relative; z-index: 2; text-align: center; mix-blend-mode: overlay; opacity: 1; transition: opacity 0.5s; }
.hero-content h1 { color: #fff; text-shadow: 0 10px 40px rgba(0,0,0,0.5); }
.scroll-indicator {
    position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%); z-index: 3;
    display: flex; flex-direction: column; align-items: center; gap: 10px; opacity: 0.7;
}

/* 2. ERA 1: VERTICAL */
.era-section { padding: 150px 0; background: var(--bg-primary); }
.year-row {
    display: flex; align-items: center; justify-content: space-between; margin-bottom: 200px;
    opacity: 0; transform: translateY(50px); transition: all 1s ease;
}
.year-row.visible { opacity: 1; transform: translateY(0); }
.year-row.reverse { flex-direction: row-reverse; }
.year-content { width: 40%; }
.year-image { width: 50%; position: relative; overflow: hidden; border-radius: 8px; }
.year-image img { width: 100%; transition: transform 0.1s linear; }
.year-title { font-size: 5rem; margin-bottom: 1rem; line-height: 1; }

/* 3. ERA 2: HORIZONTAL STICKY */
.horizontal-wrapper { height: 400vh; position: relative; background: var(--bg-secondary); }
.horizontal-sticky {
    position: sticky; top: 0; height: 100vh; width: 100%; overflow: hidden;
    display: flex; align-items: center;
}
.horizontal-intro {
    position: absolute; left: 10vw; width: 300px; z-index: 10;
    opacity: 1; transition: opacity 0.5s;
}
.horizontal-track {
    display: flex; gap: 50px; position: absolute; left: 40vw; top: 50%; transform: translateY(-50%);
    will-change: transform;
}
.h-card {
    width: 400px; height: 60vh; background: #151A23; border: 1px solid rgba(255,255,255,0.1);
    display: flex; flex-direction: column; padding: 20px; border-radius: 4px;
    flex-shrink: 0; transition: transform 0.3s;
}
.h-card:hover { transform: translateY(-10px); border-color: var(--gold); }
.h-year { font-size: 1.5rem; color: var(--gold); font-family: 'Space Mono'; margin-bottom: 20px; }
.h-img { height: 60%; width: 100%; overflow: hidden; margin-bottom: 20px; }
.h-img img { width: 100%; height: 100%; object-fit: cover; }

/* 4. ERA 3: SPOTLIGHT */
.spotlight-section { padding: 150px 0; background: #000; color: #fff; }
.small-container { max-width: 800px; margin: 0 auto; }
.spotlight-item {
    padding: 50px 0; border-bottom: 1px solid rgba(255,255,255,0.1);
    display: flex; align-items: baseline; gap: 40px;
    opacity: 0.4; transition: all 0.5s; /* Increased base opacity */
}
.spotlight-item.active { opacity: 1; padding: 80px 0; transform: scale(1.05); }
.spotlight-year { font-size: 4rem; font-family: 'Playfair Display'; color: transparent; -webkit-text-stroke: 1px #777; transition: all 0.5s; }
.spotlight-item.active .spotlight-year { color: var(--gold); -webkit-text-stroke: 1px var(--gold); }
.spotlight-content h3 { font-size: 2rem; margin-bottom: 10px; }

/* 5. ERA 4: STACKING CARDS CSS-ONLY */
.stacking-section { background: var(--bg-primary); padding-bottom: 15vh; }
.sticky-stack-wrapper { position: relative; }
.cards-container {
    max-width: 1000px; margin: 0 auto; padding-top: 50px; position: relative;
}
.stack-card {
    position: sticky; top: calc(15vh + var(--index) * 40px);
    height: 60vh; margin-bottom: 50px;
    border-radius: 20px; box-shadow: 0 -10px 40px rgba(0,0,0,0.5);
    background: #1a1f2b; border: 1px solid rgba(255,255,255,0.05);
    display: flex; flex-direction: column; overflow: hidden;
}
.card-inner { display: flex; width: 100%; height: 100%; padding: 40px; gap: 40px; align-items: center; }
.card-left { flex: 1; }
.card-left h2 { font-size: 4rem; font-family: 'Playfair Display'; margin-bottom: 20px; line-height: 1; }
.card-right { flex: 1; height: 100%; overflow: hidden; border-radius: 10px; }
.card-right img { width: 100%; height: 100%; object-fit: cover; }
.last-card { background: var(--gold); color: #000; box-shadow: 0 -30px 80px rgba(200, 155, 58, 0.3); }

/* MOBILE */
@media(max-width: 768px) {
    .display-super { font-size: 4rem; }
    .year-row { flex-direction: column !important; margin-bottom: 100px; text-align: center; }
    .year-content, .year-image { width: 100%; }
    .year-image { margin-top: 30px; }
    .horizontal-intro { position: relative; left: 0; width: 100%; padding: 40px; }
    .horizontal-sticky { position: relative; height: auto; display: block; overflow-x: scroll; }
    .horizontal-track { position: relative; left: 0; top: 0; transform: none; padding: 20px 40px; }
    .spotlight-item { flex-direction: column; gap: 10px; text-align: center; }
    .card-inner { flex-direction: column; padding: 30px; text-align: center; }
    .stack-card { position: relative; top: 0 !important; margin-bottom: 30px; height: auto; min-height: 60vh; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. HERO PARALLAX
    const heroBg = document.querySelector('.hero-bg');
    const heroContent = document.querySelector('.hero-content');
    
    // 2. VERTICAL ERA OBSERVER
    const years = document.querySelectorAll('.year-row');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.2 });
    years.forEach(y => observer.observe(y));

    // 3. HORIZONTAL SCROLL
    const horizWrapper = document.querySelector('.horizontal-wrapper');
    const horizTrack = document.querySelector('.horizontal-track');
    
    // 4. SPOTLIGHT OBSERVER
    const highlights = document.querySelectorAll('.spotlight-item');
    const spotlightObs = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                highlights.forEach(h => h.classList.remove('active'));
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.6, rootMargin: "-10% 0px -10% 0px" });
    highlights.forEach(h => spotlightObs.observe(h));

    // 5. STACKING CARDS SCALE
    const cards = document.querySelectorAll('.stack-card');

    // MAIN SCROLL LISTENER
    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;
        const winH = window.innerHeight;

        // Hero Logic
        if(scrollY < winH) {
            heroBg.style.transform = `scale(${1 + scrollY * 0.0005})`;
            heroContent.style.opacity = 1 - (scrollY / (winH * 0.8));
            heroContent.style.transform = `translateY(${scrollY * 0.3}px)`;
        }

        // Horizontal Logic - Corrected
        if(horizWrapper && window.innerWidth > 768) {
            const rect = horizWrapper.getBoundingClientRect();
            const top = rect.top;
            const dist = horizWrapper.offsetHeight - winH;
            
            if(top <= 0 && -top < dist) {
                const percent = -top / dist;
                // Scroll width - viewport width + buffer
                const scrollWidth = horizTrack.scrollWidth - window.innerWidth + 200;
                const offset = percent * scrollWidth;
                
                horizTrack.style.transform = `translateY(-50%) translateX(-${offset}px)`;
                
                // Fade out intro
                document.querySelector('.horizontal-intro').style.opacity = 1 - (percent * 3);
            }
        }

        // Stacking Cards Logic (Removed JS for CSS-only stacking)
    });
});
</script>
@endsection