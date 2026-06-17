@extends('layouts.guest')

@section('title', 'Contact Us - Culinaire')

@section('content')
<!-- Hero Section with Video Background -->
<div class="contact-hero d-flex align-items-center justify-content-center position-relative">
    <div class="video-background">
        <video autoplay loop muted playsinline>
            <source src="https://res.cloudinary.com/dh9ysyfit/video/upload/v1766045650/IMG_7855_dv47s8.mov" type="video/mp4">
        </video>
        <div class="overlay"></div>
    </div>
    
    <div class="container position-relative z-index-2 text-center mt-5">
        <h6 class="text-gold text-uppercase letter-spacing-3 mb-3" data-aos="fade-down" data-aos-duration="1000">Get in Touch</h6>
        <h1 class="display-3 font-heading text-white mb-4" data-aos="fade-up" data-aos-duration="1200">Let's Start a Conversation</h1>
        <p class="text-white-50 font-light lead mx-auto" style="max-width: 600px;" data-aos="fade-up" data-aos-duration="1400">
            Whether you wish to make a reservation, inquire about a private event, or simply share your experience, we invite you to reach out.
        </p>
        
        <a href="#contact-details" class="scroll-down mt-5" data-aos="fade-in" data-aos-delay="1000" data-aos-duration="2000">
            <span class="d-block text-white-50 small text-uppercase mb-2">Scroll Down</span>
            <i class="bi bi-chevron-down text-gold fs-4"></i>
        </a>
    </div>
</div>

<!-- Main Contact Section -->
<section id="contact-details" class="contact-main-section py-5">
    <div class="container py-5">
        <div class="row g-0 luxury-glass-panel rounded-4 overflow-hidden shadow-lg">
            
            <!-- Left Side: Contact Form -->
            <div class="col-lg-7 p-5 p-md-5 bg-dark-glass position-relative">
                <div class="form-wrapper z-index-2 position-relative">
                    <h3 class="font-heading text-white mb-4">Send a Message</h3>
                    <p class="text-white-50 mb-5">Your feedback and enquiries are paramount to us. Please fill out the form below and our team will get back to you shortly.</p>
                    
                    <form action="#" method="POST" class="luxury-form">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group floating-label">
                                    <input type="text" class="form-control" id="name" placeholder=" " required>
                                    <label for="name">Your Name</label>
                                    <span class="line-focus"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group floating-label">
                                    <input type="email" class="form-control" id="email" placeholder=" " required>
                                    <label for="email">Email Address</label>
                                    <span class="line-focus"></span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group floating-label">
                                    <input type="text" class="form-control" id="subject" placeholder=" " required>
                                    <label for="subject">Subject / Inquiry Type</label>
                                    <span class="line-focus"></span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group floating-label">
                                    <textarea class="form-control" id="message" rows="4" placeholder=" " required></textarea>
                                    <label for="message">Your Message</label>
                                    <span class="line-focus"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mt-5 gap-3">
                            <div class="privacy-note text-white-50 small">
                                <i class="bi bi-shield-lock me-1 text-gold"></i> Your details are kept strictly confidential.
                            </div>
                            <button type="button" class="btn-luxury">
                                <span class="btn-text">Send Message</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Side: Contact Information -->
            <div class="col-lg-5 p-5 p-md-5 bg-gold-dark text-white d-flex flex-column justify-content-between">
                <div>
                    <h3 class="font-heading mb-5">Information</h3>
                    
                    <div class="contact-info-item d-flex mb-4">
                        <div class="icon-wrapper text-gold me-4">
                            <i class="bi bi-geo-alt fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase letter-spacing-1 mb-2 opacity-75">Visit Us</h6>
                            <p class="mb-0 fw-light">
                                Jl. Ketintang No.156, Surabaya<br>
                                East Java, Indonesia 60231
                            </p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item d-flex mb-4">
                        <div class="icon-wrapper text-gold me-4">
                            <i class="bi bi-telephone fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase letter-spacing-1 mb-2 opacity-75">Call Us</h6>
                            <p class="mb-0 fw-light">
                                +62 31 828 6500<br>
                                <span class="opacity-75 small">Mon - Sun, 08:00 - 20:00</span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item d-flex mb-4">
                        <div class="icon-wrapper text-gold me-4">
                            <i class="bi bi-envelope fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase letter-spacing-1 mb-2 opacity-75">Write Us</h6>
                            <p class="mb-0 fw-light">
                                info@surabaya.telkomuniversity.ac.id<br>
                                <span class="opacity-75 small">We reply within 24 hours</span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-5">
                    <h6 class="text-uppercase letter-spacing-1 mb-3 opacity-75">Follow Us</h6>
                    <div class="social-links d-flex gap-3">
                        <a href="#" class="social-btn"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-btn"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-btn"><i class="bi bi-twitter-x"></i></a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- Full Width Map Section -->
<section class="map-section pb-0">
    <div class="container-fluid p-0">
        <div class="map-container position-relative">
            <iframe 
                src="https://maps.google.com/maps?q=Universitas%20Telkom%20Surabaya,%20Jl.%20Ketintang%20No.156,%20Surabaya&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                width="100%" 
                height="450" 
                style="border:0; filter: grayscale(100%) invert(92%) contrast(83%);" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
            <div class="map-overlay-pointer d-none d-md-flex align-items-center justify-content-center">
                <div class="pulse-pointer"></div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<!-- AOS Animation CSS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<style>
    :root {
        --gold: #D4AF37;
        --gold-dark: #b5952f;
        --dark-bg: #0a0a0a;
        --glass-bg: rgba(20, 20, 20, 0.85);
        --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    
    body { 
        background-color: var(--dark-bg); 
    }
    
    /* Typography Utilities */
    .text-gold { color: var(--gold) !important; }
    .bg-gold-dark { background-color: #1a1610; border-left: 1px solid rgba(212, 175, 55, 0.2); }
    .bg-dark-glass { background-color: var(--glass-bg); }
    .letter-spacing-1 { letter-spacing: 1px; }
    .letter-spacing-3 { letter-spacing: 3px; }
    .font-heading { font-family: 'Playfair Display', serif; }
    .font-light { font-weight: 300; }
    .z-index-2 { z-index: 2; }
    
    /* Hero Section */
    .contact-hero {
        min-height: 100vh;
        width: 100%;
        overflow: hidden;
    }
    
    .video-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
    }
    
    .video-background video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .video-background .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0.6) 0%, rgba(10,10,10,1) 100%);
    }

    /* Scroll Down Indicator */
    .scroll-down {
        display: inline-block;
        text-decoration: none;
        transition: var(--transition);
        animation: bounce 2s infinite;
    }
    
    .scroll-down:hover {
        opacity: 0.7;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-15px); }
        60% { transform: translateY(-7px); }
    }

    /* Form Layout */
    .luxury-glass-panel {
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .form-group { 
        position: relative; 
        margin-bottom: 1.5rem; 
    }
    
    .form-control {
        border: none;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 0;
        padding: 0.8rem 0;
        font-family: inherit;
        background: transparent !important;
        transition: var(--transition);
        font-size: 0.95rem;
        color: #fff !important;
    }
    
    .form-control:focus {
        box-shadow: none;
        border-bottom-color: var(--gold);
    }
    
    .form-control::placeholder { 
        color: transparent; 
    }
    
    .floating-label label {
        position: absolute;
        top: 0.8rem;
        left: 0;
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.95rem;
        pointer-events: none;
        transition: var(--transition);
    }
    
    .form-control:focus ~ label,
    .form-control:not(:placeholder-shown) ~ label {
        top: -1.2rem;
        font-size: 0.75rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
    }
    
    /* Remove autofill background in webkit */
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus, 
    textarea:-webkit-autofill,
    textarea:-webkit-autofill:hover,
    textarea:-webkit-autofill:focus {
        -webkit-text-fill-color: white;
        -webkit-box-shadow: 0 0 0px 1000px transparent inset;
        transition: background-color 5000s ease-in-out 0s;
    }
    
    .line-focus {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 1px;
        background-color: var(--gold);
        transition: var(--transition);
    }
    
    .form-control:focus ~ .line-focus { 
        width: 100%; 
    }

    /* Buttons & Socials */
    .btn-luxury {
        background: transparent;
        border: 1px solid var(--gold);
        padding: 12px 35px;
        font-family: 'Playfair Display', serif;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 0.85rem;
        position: relative;
        overflow: hidden;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        color: var(--gold);
    }
    
    .btn-luxury .btn-text {
        position: relative; 
        z-index: 2; 
        transition: color 0.3s; 
    }
    
    .btn-luxury::after {
        content: ''; 
        position: absolute; 
        bottom: 0; 
        left: 0; 
        width: 100%; 
        height: 0; 
        background-color: var(--gold); 
        transition: var(--transition); 
        z-index: 1;
    }
    
    .btn-luxury:hover::after { 
        height: 100%; 
    }
    
    .btn-luxury:hover .btn-text, .btn-luxury:hover { 
        color: #000 !important; 
    }
    
    .social-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1px solid rgba(255,255,255,0.2);
        color: #fff;
        transition: var(--transition);
        text-decoration: none;
    }
    
    .social-btn:hover {
        background: var(--gold);
        border-color: var(--gold);
        color: #000;
        transform: translateY(-3px);
    }

    /* Map Specific */
    .map-container {
        overflow: hidden;
    }
    
    .map-overlay-pointer {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 991px) {
        .bg-gold-dark { border-left: none; border-top: 1px solid rgba(212, 175, 55, 0.2); }
        .contact-hero { min-height: 80vh; }
    }
</style>
@endpush

@push('scripts')
<!-- AOS Animation JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize AOS animations
        AOS.init({
            once: true,
            offset: 50,
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const navbarHeight = document.querySelector('.navbar-culinaire') ? document.querySelector('.navbar-culinaire').offsetHeight : 0;
                    window.scrollTo({
                        top: target.offsetTop - navbarHeight,
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
</script>
@endpush