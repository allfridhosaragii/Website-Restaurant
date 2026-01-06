@extends('layouts.guest')

@section('title', 'The Legacy • Culinaire')

@extends('layouts.guest')

@section('title', 'The Legacy • Culinaire')

@section('content')
<div class="about-luxury-wrapper smooth-wrapper">
    
    <!-- HERO SECTION: THE LEGACY -->
    <section class="about-hero-section" id="hero">
        <div class="hero-bg" style="background-image: url('https://images.unsplash.com/photo-1544148103-0773bf10d330?q=80&w=1920&auto=format&fit=crop');"></div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="display-super tracking-tighter" data-speed="0.2">LEGACY</h1>
            <div class="separator-line"></div>
            <p class="subtitle font-mono tracking-widest text-gold mt-4" data-speed="0.1">A JOURNEY OF TASTE • 2009 - 2025</p>
            <div class="scroll-indicator">
                <span class="text-xs font-mono tracking-widest text-white-50">SCROLL TO EXPLORE</span>
                <div class="line"></div>
            </div>
        </div>
    </section>

    <!-- ERA 1: THE FOUNDATIONS (Clean Central Layout) -->
    <section class="era-section vertical-era" id="era-1">
        <div class="container-fluid px-0">
            <div class="era-header text-center mb-5" data-aos="fade-up">
                <span class="era-label font-mono text-gold tracking-widest text-xs">CHAPTER I</span>
                <h2 class="display-3 text-white mt-3 mb-5">THE FOUNDATIONS</h2>
            </div>
            
            <div class="central-timeline">
                <div class="central-line"></div>
                
                <!-- 2009 -->
                <div class="timeline-node" data-year="2009">
                    <div class="node-year font-display text-gold">2009</div>
                    <div class="node-content">
                        <div class="node-img">
                            <img src="https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?w=800&q=80" alt="2009">
                        </div>
                        <div class="node-text">
                            <h4 class="text-white font-display">The Spark</h4>
                            <p class="text-muted font-serif">In a small rustic corner, the first flame involved. A wood-fired oven and a dream to bring authentic flavors back to life.</p>
                        </div>
                    </div>
                </div>

                <!-- 2010 -->
                <div class="timeline-node" data-year="2010">
                    <div class="node-year font-display text-gold">2010</div>
                    <div class="node-content">
                        <div class="node-img">
                            <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?w=800&q=80" alt="2010">
                        </div>
                        <div class="node-text">
                            <h4 class="text-white font-display">First Family</h4>
                            <p class="text-muted font-serif">We grew from 3 to 15. The kitchen became a symphony of passion, recruiting the finest local talent.</p>
                        </div>
                    </div>
                </div>

                <!-- 2011 -->
                <div class="timeline-node" data-year="2011">
                    <div class="node-year font-display text-gold">2011</div>
                    <div class="node-content">
                        <div class="node-img">
                            <img src="https://images.unsplash.com/photo-1550966871-3ed3c47e2ce2?w=800&q=80" alt="2011">
                        </div>
                        <div class="node-text">
                            <h4 class="text-white font-display">Local Recognition</h4>
                            <p class="text-muted font-serif">Named "Best Newcomer" by City Eats. Lines started forming around the block.</p>
                        </div>
                    </div>
                </div>

                 <!-- 2012 -->
                <div class="timeline-node" data-year="2012">
                     <div class="node-year font-display text-gold">2012</div>
                    <div class="node-content">
                        <div class="node-img">
                            <img src="https://images.unsplash.com/photo-1516455590571-18256e5bb9ff?w=800&q=80" alt="2012">
                        </div>
                        <div class="node-text">
                            <h4 class="text-white font-display">The Renovation</h4>
                            <p class="text-muted font-serif">We broke down walls. Expanding our dining hall to welcome the growing community of food lovers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ERA 2: THE GOLDEN ERA (Glassmorphism Horizontal) -->
    <section class="horizontal-wrapper">
        <div class="horizontal-sticky">
            <div class="horizontal-intro">
                <span class="era-label font-mono text-gold tracking-widest text-xs d-block mb-3">CHAPTER II</span>
                <h2 class="display-3 text-white">THE GOLDEN ERA</h2>
                <div class="h-line my-4"></div>
                <p class="text-white-50 font-serif lead">Expansion & Excellence</p>
            </div>
            <div class="horizontal-track">
                <!-- 2013 -->
                <div class="h-card glass-card">
                    <span class="h-year">2013</span>
                    <div class="h-content">
                        <h3>A Star is Born</h3>
                        <p>Our first major culinary award. The Gold Spoon.</p>
                    </div>
                </div>
                <!-- 2014 -->
                <div class="h-card glass-card">
                    <span class="h-year">2014</span>
                    <div class="h-content">
                        <h3>Second Location</h3>
                        <p>Opening our doors in the heart of the capital.</p>
                    </div>
                </div>
                 <!-- 2015 -->
                <div class="h-card glass-card">
                    <span class="h-year">2015</span>
                     <div class="h-content">
                        <h3>International Tour</h3>
                        <p>Our chefs traveled to Europe to master new techniques.</p>
                    </div>
                </div>
                 <!-- 2016 -->
                <div class="h-card glass-card">
                    <span class="h-year">2016</span>
                     <div class="h-content">
                        <h3>The Wine Cellar</h3>
                        <p>Introducing a collection of 500+ vintage wines.</p>
                    </div>
                </div>
                 <!-- 2017 -->
                <div class="h-card glass-card">
                    <span class="h-year">2017</span>
                     <div class="h-content">
                        <h3>Chef's Table</h3>
                        <p>Launching the exclusive private dining experience.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ERA 3: RESILIENCE (High Contrast Spotlight) -->
    <section class="spotlight-section">
        <div class="container small-container">
            <div class="text-center mb-5 fade-in">
                <span class="era-label font-mono text-gold tracking-widest text-xs">CHAPTER III</span>
                <h2 class="display-3 text-white mt-3">RESILIENCE</h2>
            </div>

            <div class="spotlight-list">
                <!-- 2018 -->
                <div class="spotlight-item" data-year="2018">
                    <div class="sl-year">2018</div>
                    <div class="sl-content">
                        <h3>Sustainable Shift</h3>
                        <p>We completely overhauled our supply chain to support local farmers and 100% organic produce.</p>
                    </div>
                </div>

                <!-- 2019 -->
                <div class="spotlight-item" data-year="2019">
                    <div class="sl-year">2019</div>
                    <div class="sl-content">
                        <h3>The Decade Mark</h3>
                        <p>Celebrating 10 years of culinary excellence with a gala that gathered chefs from around the world.</p>
                    </div>
                </div>

                <!-- 2020 -->
                <div class="spotlight-item" data-year="2020">
                    <div class="sl-year">2020</div>
                    <div class="sl-content">
                        <h3>Standing Strong</h3>
                        <p>When the world stopped, our ovens kept burning. We served 50,000 meals to frontline heroes.</p>
                    </div>
                </div>

                <!-- 2021 -->
                <div class="spotlight-item" data-year="2021">
                    <div class="sl-year">2021</div>
                    <div class="sl-content">
                        <h3>Digital Rebirth</h3>
                        <p>Launching Culinaire Home, bringing our signature dining experience to your dining table.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ERA 4: THE VISION (Massive Cards) -->
    <section class="stacking-section">
        <div class="sticky-stack-wrapper">
             <div class="stack-header text-center mb-5">
                <span class="era-label font-mono text-gold tracking-widest text-xs">CHAPTER IV</span>
                <h2 class="display-3 text-white mt-3">THE VISION</h2>
            </div>
            
            <div class="cards-container">
                <!-- 2022 -->
                <div class="stack-card" style="--index: 1;">
                    <div class="sc-content">
                        <span class="sc-year text-gold">2022</span>
                        <h2>Technological Harmony</h2>
                        <p>Integrating AI precision with human artistry in our kitchens.</p>
                    </div>
                    <div class="sc-image">
                        <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&q=80" alt="2022">
                    </div>
                </div>
                
                <!-- 2023 -->
                <div class="stack-card" style="--index: 2;">
                     <div class="sc-content">
                        <span class="sc-year text-white">2023</span>
                        <h2>Global Franchise</h2>
                        <p>Our vision expanded to Tokyo, London, and New York.</p>
                    </div>
                    <div class="sc-image">
                        <img src="https://images.unsplash.com/photo-1592861956120-e524fc739696?w=800&q=80" alt="2023">
                    </div>
                </div>

                <!-- 2024 -->
                <div class="stack-card" style="--index: 3;">
                     <div class="sc-content">
                        <span class="sc-year text-gold">2024</span>
                        <h2>Zero Waste</h2>
                        <p>Achieving our goal of becoming the first Zero Waste fine dining establishment.</p>
                    </div>
                    <div class="sc-image">
                        <img src="https://images.unsplash.com/photo-1600093463592-8e36ae95ef56?w=800&q=80" alt="2024">
                    </div>
                </div>

                <!-- 2025 -->
                <div class="stack-card last-card" style="--index: 4;">
                     <div class="sc-content">
                        <span class="sc-year text-dark">2025</span>
                        <h2 class="text-dark">The Next Chapter</h2>
                        <p class="text-dark">We are just getting started. The legacy continues with you.</p>
                    </div>
                    <div class="sc-image">
                        <img src="https://images.unsplash.com/photo-1550966871-3ed3c47e2ce2?w=800&q=80" alt="2025">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 text-center mt-5">
        <p class="font-mono text-white-50 text-xs tracking-widest">EST. 2009 • CULINAIRE</p>
    </footer>

</div>

<style>
/* CORE LUXURY SETTINGS - SCOPED */
.about-luxury-wrapper {
    --bg-primary: #050608;
    --bg-secondary: #0A0C10;
    --gold: #D4AF37;
    --text-white: #ffffff;
    --text-muted: rgba(255,255,255,0.5);
    --font-display: 'Playfair Display', serif;
    --font-serif: 'Lora', serif;
    --font-mono: 'Space Mono', monospace;

    /* Force Dark Theme */
    background-color: var(--bg-primary); 
    color: var(--text-white); 
    font-family: var(--font-serif);
    overflow-x: hidden; 
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 10;
}

/* Force text colors to be visible regardless of global theme */
.about-luxury-wrapper h1,
.about-luxury-wrapper h2,
.about-luxury-wrapper h3,
.about-luxury-wrapper h4,
.about-luxury-wrapper h5,
.about-luxury-wrapper h6,
.about-luxury-wrapper p,
.about-luxury-wrapper span,
.about-luxury-wrapper div {
    color: inherit;
}


/* TYPOGRAPHY */
.display-super { 
    font-family: var(--font-display); 
    font-size: clamp(4rem, 15vw, 12rem); 
    font-weight: 400; 
    line-height: 0.9;
    letter-spacing: -0.02em;
}
.display-3 {
    font-family: var(--font-display);
    font-weight: 400;
    letter-spacing: -1px;
}
.font-display { font-family: var(--font-display); }
.font-mono { font-family: var(--font-mono); }
.text-gold { color: var(--gold); }
.text-white-50 { color: var(--text-muted); }
.tracking-widest { letter-spacing: 0.2em; }

/* 1. HERO */
.about-hero-section {
    height: 100vh; width: 100%; position: relative; 
    display: flex; align-items: center; justify-content: center; 
}
.hero-bg {
    position: absolute; inset: 0; background-size: cover; background-position: center; 
    opacity: 0.4; filter: contrast(1.1) saturate(0.8);
}
.hero-content { 
    position: relative; z-index: 2; text-align: center; 
    display: flex; flex-direction: column; align-items: center; justify-content: center;
}
.hero-content h1 {
    opacity: 0; animation: fadeUp 1s ease forwards 0.5s;
}
.scroll-indicator {
    position: absolute; bottom: 50px; left: 50%; transform: translateX(-50%);
    display: flex; flex-direction: column; align-items: center; gap: 15px; opacity: 0;
    animation: fadeIn 1s ease forwards 1.5s;
}
.scroll-indicator .line {
    width: 1px; height: 60px; background: rgba(255,255,255,0.2);
    position: relative; overflow: hidden;
}
.scroll-indicator .line::after {
    content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    background: var(--gold); animation: scrollLine 2s infinite;
}

/* 2. ERA 1: CENTRAL TIMELINE */
.era-section { padding: 200px 0; background: var(--bg-primary); }
.central-timeline {
    position: relative; max-width: 1000px; margin: 0 auto;
}
.central-line {
    position: absolute; top: 0; bottom: 0; left: 50%; width: 1px; 
    background: rgba(255,255,255,0.1); transform: translateX(-50%);
}
.timeline-node {
    display: flex; justify-content: center; align-items: center;
    margin-bottom: 200px; position: relative;
}
.node-year {
    position: absolute; font-size: 12rem; opacity: 0.05; z-index: 0;
    font-weight: 700; top: 50%; left: 50%; transform: translate(-50%, -50%);
    white-space: nowrap; transition: opacity 0.5s;
}
.timeline-node.visible .node-year { opacity: 0.1; }
.node-content {
    width: 100%; display: flex; align-items: center; justify-content: center; gap: 80px;
    z-index: 1;
}
.node-img {
    width: 400px; height: 500px; overflow: hidden; opacity: 0; 
    transform: translateY(50px); transition: all 1s ease;
}
.node-text {
    width: 300px; opacity: 0; transform: translateY(50px); transition: all 1s ease 0.2s;
}
.timeline-node:nth-child(even) .node-content { flex-direction: row-reverse; }
.timeline-node.visible .node-img, .timeline-node.visible .node-text {
    opacity: 1; transform: translateY(0);
}
.node-img img { width: 100%; height: 100%; object-fit: cover; filter: grayscale(0.5); }

/* 3. ERA 2: GLASSMORPHISM HORIZONTAL */
.horizontal-wrapper { height: 350vh; position: relative; background: var(--bg-secondary); }
.horizontal-sticky {
    position: sticky; top: 0; height: 100vh; overflow: hidden;
    display: flex; align-items: center;
}
.horizontal-intro {
    position: absolute; left: 10vw; width: 400px; z-index: 10;
}
.h-line { width: 60px; height: 1px; background: var(--gold); }
.horizontal-track {
    display: flex; gap: 40px; position: absolute; left: 45%; top: 50%; transform: translateY(-50%);
}
.glass-card {
    width: 350px; height: 500px; padding: 40px;
    background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);
    backdrop-filter: blur(10px);
    display: flex; flex-direction: column; justify-content: space-between;
    transition: all 0.3s;
}
.glass-card:hover { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.1); }
.h-year { font-family: var(--font-display); font-size: 4rem; color: var(--gold); opacity: 0.8; }
.h-content h3 { font-family: var(--font-display); font-size: 1.5rem; margin-bottom: 10px; }
.h-content p { font-size: 0.9rem; color: var(--text-muted); line-height: 1.6; }

/* 4. ERA 3: HIGH CONTRAST SPOTLIGHT */
.spotlight-section { padding: 200px 0; background: #000; }
.spotlight-list { position: relative; max-width: 800px; margin: 0 auto; }
.spotlight-item {
    display: flex; gap: 50px; padding: 100px 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    opacity: 0.1; filter: blur(3px); transition: all 0.6s ease;
}
.spotlight-item.active { opacity: 1; filter: blur(0); }
.sl-year {
    font-family: var(--font-display); font-size: 5rem; color: var(--gold);
    line-height: 1; 
}
.sl-content h3 { font-size: 2rem; margin-bottom: 15px; }
.sl-content p { color: var(--text-muted); font-size: 1.1rem; line-height: 1.6; }

/* 5. ERA 4: MASSIVE STACKING */
.stacking-section { padding: 100px 0 200px; background: var(--bg-primary); }
.cards-container { max-width: 1100px; margin: 0 auto; position: relative; padding-top: 50px; }
.stack-card {
    position: sticky; top: calc(10vh + var(--index) * 30px);
    height: 70vh; margin-bottom: 50px;
    background: #12141a; border-radius: 2px;
    display: flex; overflow: hidden;
    box-shadow: 0 -20px 50px rgba(0,0,0,0.5);
}
.sc-content { width: 40%; padding: 60px; display: flex; flex-direction: column; justify-content: center; }
.sc-year { font-family: var(--font-mono); font-size: 1rem; letter-spacing: 0.2em; margin-bottom: 30px; display: block; }
.sc-content h2 { font-family: var(--font-display); font-size: 3.5rem; line-height: 1.1; margin-bottom: 20px; }
.sc-image { width: 60%; height: 100%; }
.sc-image img { width: 100%; height: 100%; object-fit: cover; }
.last-card { background: var(--gold); }

/* ANIMATIONS */
@keyframes fadeUp { to { opacity: 1; transform: translateY(0); } from { opacity: 0; transform: translateY(30px); } }
@keyframes fadeIn { to { opacity: 1; } from { opacity: 0; } }
@keyframes scrollLine { 0% { top: -100%; } 100% { top: 100%; } }

/* RESPONSIVE */
@media(max-width: 768px) {
    .node-content { flex-direction: column !important; gap: 30px; text-align: center; }
    .node-img { width: 100%; height: 400px; }
    .node-text { width: 100%; }
    .node-year { font-size: 6rem; }
    
    .horizontal-intro { position: relative; width: 100%; left: 0; padding: 40px; }
    .horizontal-sticky { position: relative; height: auto; display: block; overflow-x: scroll; }
    .horizontal-track { position: relative; left: 0; top: 0; transform: none; padding: 20px; gap: 20px; }
    .horizontal-wrapper { height: auto; }
    
    .stack-card { flex-direction: column; height: auto; position: relative; top: 0 !important; margin-bottom: 30px; }
    .sc-content, .sc-image { width: 100%; }
    .sc-image { height: 300px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Vertical Era Animations
    const nodes = document.querySelectorAll('.timeline-node');
    const nodeObs = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) entry.target.classList.add('visible');
        });
    }, { threshold: 0.3 });
    nodes.forEach(n => nodeObs.observe(n));

    // 2. Horizontal Scroll
    const hWrapper = document.querySelector('.horizontal-wrapper');
    const hTrack = document.querySelector('.horizontal-track');
    window.addEventListener('scroll', () => {
        if(window.innerWidth > 768 && hWrapper) {
            const rect = hWrapper.getBoundingClientRect();
            const top = rect.top;
            const dist = hWrapper.offsetHeight - window.innerHeight;
            if(top <= 0 && -top < dist) {
                const percent = -top / dist;
                const move = percent * (hTrack.scrollWidth - window.innerWidth * 0.5);
                hTrack.style.transform = `translateY(-50%) translateX(-${move}px)`;
                document.querySelector('.horizontal-intro').style.opacity = 1 - (percent * 3);
            }
        }
    });

    // 3. Spotlight
    const spots = document.querySelectorAll('.spotlight-item');
    const spotObs = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                spots.forEach(s => s.classList.remove('active'));
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.5, rootMargin: "-20% 0px -20% 0px" });
    spots.forEach(s => spotObs.observe(s));
});
</script>
@endsection