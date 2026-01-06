@extends('layouts.guest')

@section('title', 'The Legacy • Culinaire')

@section('content')
<!-- WRAPPER: Forced Dark Theme & CSS Isolation -->
<div class="about-luxury-wrapper smooth-wrapper" id="aboutPageRoot">
    
    <!-- HERO SECTION -->
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

    <!-- CHAPTER I: THE FOUNDATIONS (Central Timeline) -->
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
                            <p class="text-gray-300 font-serif">In a small rustic corner, the first flame involved. A wood-fired oven and a dream to bring authentic flavors back to life.</p>
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
                            <p class="text-gray-300 font-serif">We grew from 3 to 15. The kitchen became a symphony of passion, recruiting the finest local talent.</p>
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
                            <p class="text-gray-300 font-serif">Named "Best Newcomer" by City Eats. Lines started forming around the block.</p>
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
                            <p class="text-gray-300 font-serif">We broke down walls. Expanding our dining hall to welcome the growing community of food lovers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CHAPTER II: THE GOLDEN ERA (Horizontal Scroll - Simplified) -->
    <section class="horizontal-wrapper">
        <div class="horizontal-header text-center mb-5">
            <span class="era-label font-mono text-gold tracking-widest text-xs">CHAPTER II</span>
            <h2 class="display-3 text-white mt-3">THE GOLDEN ERA</h2>
            <p class="text-white-50 font-serif lead">Expansion & Excellence (Swipe to Explore)</p>
        </div>
        
        <div class="horizontal-scroll-container">
            <div class="horizontal-track">
                <!-- 2013 -->
                <div class="h-card glass-card">
                    <span class="h-year text-gold">2013</span>
                    <div class="h-content">
                        <h3 class="text-white">A Star is Born</h3>
                        <p class="text-gray-300">Our first major culinary award. The Gold Spoon.</p>
                    </div>
                </div>
                <!-- 2014 -->
                <div class="h-card glass-card">
                    <span class="h-year text-gold">2014</span>
                    <div class="h-content">
                        <h3 class="text-white">Second Location</h3>
                        <p class="text-gray-300">Opening our doors in the heart of the capital.</p>
                    </div>
                </div>
                 <!-- 2015 -->
                <div class="h-card glass-card">
                    <span class="h-year text-gold">2015</span>
                     <div class="h-content">
                        <h3 class="text-white">International Tour</h3>
                        <p class="text-gray-300">Our chefs traveled to Europe to master new techniques.</p>
                    </div>
                </div>
                 <!-- 2016 -->
                <div class="h-card glass-card">
                    <span class="h-year text-gold">2016</span>
                     <div class="h-content">
                        <h3 class="text-white">The Wine Cellar</h3>
                        <p class="text-gray-300">Introducing a collection of 500+ vintage wines.</p>
                    </div>
                </div>
                 <!-- 2017 -->
                <div class="h-card glass-card">
                    <span class="h-year text-gold">2017</span>
                     <div class="h-content">
                        <h3 class="text-white">Chef's Table</h3>
                        <p class="text-gray-300">Launching the exclusive private dining experience.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CHAPTER III: RESILIENCE (Spotlight) -->
    <section class="spotlight-section">
        <div class="container small-container">
            <div class="text-center mb-5">
                <span class="era-label font-mono text-gold tracking-widest text-xs">CHAPTER III</span>
                <h2 class="display-3 text-white mt-3">RESILIENCE</h2>
            </div>

            <div class="spotlight-list">
                <!-- 2018 -->
                <div class="spotlight-item" data-year="2018">
                    <div class="sl-year text-gold">2018</div>
                    <div class="sl-content">
                        <h3 class="text-white">Sustainable Shift</h3>
                        <p class="text-gray-400">We completely overhauled our supply chain to support local farmers and 100% organic produce.</p>
                    </div>
                </div>

                <!-- 2019 -->
                <div class="spotlight-item" data-year="2019">
                    <div class="sl-year text-gold">2019</div>
                    <div class="sl-content">
                        <h3 class="text-white">The Decade Mark</h3>
                        <p class="text-gray-400">Celebrating 10 years of culinary excellence with a gala that gathered chefs from around the world.</p>
                    </div>
                </div>

                <!-- 2020 -->
                <div class="spotlight-item" data-year="2020">
                    <div class="sl-year text-gold">2020</div>
                    <div class="sl-content">
                        <h3 class="text-white">Standing Strong</h3>
                        <p class="text-gray-400">When the world stopped, our ovens kept burning. We served 50,000 meals to frontline heroes.</p>
                    </div>
                </div>

                <!-- 2021 -->
                <div class="spotlight-item" data-year="2021">
                    <div class="sl-year text-gold">2021</div>
                    <div class="sl-content">
                        <h3 class="text-white">Digital Rebirth</h3>
                        <p class="text-gray-400">Launching Culinaire Home, bringing our signature dining experience to your dining table.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CHAPTER IV: THE VISION (Stacking Cards) -->
    <section class="stacking-section">
        <div class="container pb-5">
             <div class="text-center mb-5">
                <span class="era-label font-mono text-gold tracking-widest text-xs">CHAPTER IV</span>
                <h2 class="display-3 text-white mt-3">THE VISION</h2>
            </div>
            
            <div class="cards-stack-wrapper">
                <!-- 2022 -->
                <div class="stack-card" style="top: 100px;">
                    <div class="sc-content">
                        <span class="sc-year text-gold">2022</span>
                        <h2 class="text-white">Technological Harmony</h2>
                        <p class="text-gray-300">Integrating AI precision with human artistry in our kitchens.</p>
                    </div>
                    <div class="sc-image">
                        <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&q=80" alt="2022">
                    </div>
                </div>
                
                <!-- 2023 -->
                <div class="stack-card" style="top: 140px;">
                     <div class="sc-content">
                        <span class="sc-year text-gold">2023</span>
                        <h2 class="text-white">Global Franchise</h2>
                        <p class="text-gray-300">Our vision expanded to Tokyo, London, and New York.</p>
                    </div>
                    <div class="sc-image">
                        <img src="https://images.unsplash.com/photo-1592861956120-e524fc739696?w=800&q=80" alt="2023">
                    </div>
                </div>

                <!-- 2024 -->
                <div class="stack-card" style="top: 180px;">
                     <div class="sc-content">
                        <span class="sc-year text-gold">2024</span>
                        <h2 class="text-white">Zero Waste</h2>
                        <p class="text-gray-300">Achieving our goal of becoming the first Zero Waste fine dining establishment.</p>
                    </div>
                    <div class="sc-image">
                        <img src="https://images.unsplash.com/photo-1600093463592-8e36ae95ef56?w=800&q=80" alt="2024">
                    </div>
                </div>

                <!-- 2025 -->
                <div class="stack-card" style="top: 220px;">
                     <div class="sc-content">
                        <span class="sc-year text-gold">2025</span>
                        <h2 class="text-white">The Next Chapter</h2>
                        <p class="text-gray-300">We are just getting started. The legacy continues with you.</p>
                    </div>
                    <div class="sc-image">
                        <img src="https://images.unsplash.com/photo-1550966871-3ed3c47e2ce2?w=800&q=80" alt="2025">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 text-center mt-5 border-top border-secondary">
        <p class="font-mono text-white-50 text-xs tracking-widest mb-0">EST. 2009 • CULINAIRE</p>
    </footer>

</div>

<style>
/* --- SCOPED LUXURY THEME --- */
.about-luxury-wrapper {
    background-color: #050608 !important;
    color: #ffffff !important;
    font-family: 'Lora', serif;
    width: 100%;
    min-height: 100vh;
    position: relative;
    z-index: 50;
    overflow-x: hidden;
    padding-bottom: 0;
}

/* Force Text Colors */
.about-luxury-wrapper h1, 
.about-luxury-wrapper h2, 
.about-luxury-wrapper h3, 
.about-luxury-wrapper h4 {
    color: #ffffff !important;
    font-family: 'Playfair Display', serif;
}
.about-luxury-wrapper p,
.about-luxury-wrapper li,
.about-luxury-wrapper span {
    color: #cccccc;
}
.about-luxury-wrapper .text-gold { color: #D4AF37 !important; }
.about-luxury-wrapper .text-white { color: #ffffff !important; }
.about-luxury-wrapper .text-white-50 { color: rgba(255,255,255,0.5) !important; }
.about-luxury-wrapper .text-gray-300 { color: #d1d5db !important; }
.about-luxury-wrapper .text-gray-400 { color: #9ca3af !important; }

/* Disable strikethrough if present globally */
.nav-link { text-decoration: none !important; }

/* Hero */
.about-hero-section {
    height: 100vh;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    overflow: hidden;
}
.hero-bg {
    position: absolute;
    top:0; left:0; width:100%; height:100%;
    background-size: cover;
    background-position: center;
    filter: brightness(0.4);
    z-index: 1;
}
.hero-content {
    position: relative;
    z-index: 3;
}
.display-super {
    font-size: 8rem;
    font-weight: 700;
    line-height: 1;
    letter-spacing: -2px;
}

/* Central Timeline */
.vertical-era { padding: 100px 0; background: #050608; }
.central-timeline {
    position: relative;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px 0;
}
.central-line {
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 1px;
    background: rgba(212, 175, 55, 0.3);
    transform: translateX(-50%);
}
.timeline-node {
    display: flex;
    align-items: center;
    margin-bottom: 150px;
    position: relative;
    opacity: 0;
    transform: translateY(50px);
    transition: all 0.8s ease-out;
}
.timeline-node.visible { opacity: 1; transform: translateY(0); }
.timeline-node:nth-child(odd) { flex-direction: row-reverse; }
.timeline-node:nth-child(odd) .node-text { text-align: right; }
.timeline-node:nth-child(even) .node-text { text-align: left; }
.node-year {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    font-size: 4rem;
    font-weight: 700;
    color: #D4AF37;
    background: #050608;
    padding: 10px 0;
    z-index: 2;
}
.node-content {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 50px;
}
.node-img { width: 40%; height: 300px; overflow: hidden; border-radius: 4px; }
.node-img img { width: 100%; height: 100%; object-fit: cover; filter: grayscale(1); transition: 0.5s; }
.timeline-node:hover .node-img img { filter: grayscale(0); transform: scale(1.05); }
.node-text { width: 45%; }


/* Horizontal Scroll (Simplified) */
.horizontal-wrapper {
    background: #0A0C10;
    padding: 100px 0;
    overflow: hidden;
}
.horizontal-scroll-container {
    overflow-x: auto;
    overflow-y: hidden;
    white-space: nowrap;
    padding-bottom: 30px;
    scrollbar-width: none;
    -ms-overflow-style: none;
    cursor: grab;
    padding-left: 10%;
    padding-right: 10%;
}
.horizontal-scroll-container::-webkit-scrollbar { display: none; }
.horizontal-track { display: inline-flex; gap: 40px; }
.h-card {
    width: 350px;
    height: 450px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    padding: 40px;
    white-space: normal;
    display: flex;
    flex-direction: column;
    justify-content: center;
    transition: 0.3s;
}
.h-card:hover { transform: translateY(-10px); background: rgba(255,255,255,0.05); border-color: #D4AF37; }
.h-year { font-size: 3rem; font-weight: 700; display: block; margin-bottom: 20px; }


/* Spotlight Section */
.spotlight-section { padding: 100px 0; background: #050608; }
.spotlight-item {
    padding: 50px;
    margin-bottom: 20px;
    border-left: 2px solid rgba(255,255,255,0.1);
    transition: 0.3s;
    opacity: 0.3;
}
.spotlight-item:hover, .spotlight-item.active {
    opacity: 1;
    border-left-color: #D4AF37;
    background: linear-gradient(90deg, rgba(212,175,55,0.05), transparent);
}


/* Stacking Cards */
.stacking-section { padding: 100px 0; background: #0A0C10; }
.cards-stack-wrapper {
    position: relative;
    max-width: 900px;
    margin: 0 auto;
    height: 1200px;
}
.stack-card {
    position: sticky;
    top: 100px;
    height: 450px;
    width: 100%;
    background: #111;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px;
    margin-bottom: 50px;
    display: flex;
    overflow: hidden;
    box-shadow: 0 -10px 40px rgba(0,0,0,0.5);
    transform-origin: top center;
    transition: transform 0.5s ease, opacity 0.5s ease;
}
.sc-content { padding: 60px; width: 50%; display: flex; flex-direction: column; justify-content: center; }
.sc-image { width: 50%; height: 100%; }
.sc-image img { width: 100%; height: 100%; object-fit: cover; }
.sc-year { font-size: 4rem; font-family: 'Playfair Display', serif; font-weight: 700; line-height: 1; margin-bottom: 20px; display: block; }

/* Responsive */
@media(max-width: 768px) {
    .display-super { font-size: 4rem; }
    .node-content { flex-direction: column !important; gap: 30px; text-align: center; padding: 0 20px; }
    .node-img { width: 100%; height: 250px; }
    .node-text { width: 100%; text-align: center !important; }
    .node-year { font-size: 2.5rem; }
    .central-line { display: none; }
    
    .stack-card { flex-direction: column; height: auto; position: relative; top: 0 !important; margin-bottom: 30px; }
    .sc-content, .sc-image { width: 100%; }
    .sc-image { height: 250px; }
    .sc-content { padding: 30px; }
    .sc-year { font-size: 2.5rem; }
    .cards-stack-wrapper { height: auto; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Simple Scroll Observer for Timeline
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.2 });

    document.querySelectorAll('.timeline-node').forEach(node => observer.observe(node));

    // 2. Spotlight Active State
    const spotlightObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                document.querySelectorAll('.spotlight-item').forEach(i => i.classList.remove('active'));
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.spotlight-item').forEach(item => spotlightObserver.observe(item));
});
</script>
@endsection