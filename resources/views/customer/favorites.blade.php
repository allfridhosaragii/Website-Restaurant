@extends('layouts.guest')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-0 shadow-lg mb-5 overflow-hidden position-relative" style="border-radius: 20px; background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);">
                <div class="card-body p-5 text-center text-white position-relative">
                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at 50% 50%, rgba(220, 53, 69, 0.15) 0%, transparent 70%); pointer-events: none;"></div>
                    <h2 class="mb-4 fw-light text-uppercase tracking-wider" style="letter-spacing: 2px;">Your Favorite Menus</h2>
                    <div class="mb-4">
                        <img src="https://res.cloudinary.com/dh9ysyfit/image/upload/v1766509327/IMG_8024_tcrsza.png" 
                             alt="Favorites" 
                             class="animate-pulse" 
                             style="width: 120px; height: 120px; object-fit: contain; filter: drop-shadow(0 10px 20px rgba(220, 53, 69, 0.3));">
                    </div>
                </div>
            </div>
            @if($favorites->count() > 0)
                <div class="row g-4">
                    @foreach($favorites as $fav)
                    <div class="col-md-4 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm-hover transition-all overflow-hidden" style="border-radius: 15px;">
                            <div class="position-relative">
                                <img src="{{ $fav->menu->image_url ?? 'https://via.placeholder.com/300' }}" class="card-img-top" alt="{{ $fav->menu->name }}" style="height: 200px; object-fit: cover;">
                                <div class="position-absolute bottom-0 end-0 p-2">
                                    <form action="{{ url('/customer/favorite/' . $fav->menu->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-light rounded-circle shadow-sm p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Remove from favorites">
                                            <i class="bi bi-heart-fill text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title fw-bold mb-1">{{ $fav->menu->name }}</h5>
                                <p class="text-muted small mb-2">{{ $fav->menu->category->name ?? '-' }}</p>
                                <h6 class="text-primary fw-bold mb-3">Rp {{ number_format($fav->menu->price, 0, ',', '.') }}</h6>
                                <a href="{{ url('/customer/orders/create') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4">Order Now</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-heartbreak text-muted opacity-25" style="font-size: 5rem;"></i>
                    </div>
                    <h4 class="text-muted fw-light">No favorites yet</h4>
                    <p class="text-muted mb-4">Start exploring our menu and save your favorite dishes!</p>
                    <a href="{{ url('/menu') }}" class="btn btn-primary rounded-pill px-5 py-2">Explore Menu</a>
                </div>
            @endif
        </div>
    </div>
</div>
<style>
    @keyframes pulse-red {
        0% { transform: scale(1); filter: drop-shadow(0 0 0 rgba(220, 53, 69, 0.4)); }
        50% { transform: scale(1.05); filter: drop-shadow(0 0 20px rgba(220, 53, 69, 0.7)); }
        100% { transform: scale(1); filter: drop-shadow(0 0 0 rgba(220, 53, 69, 0.4)); }
    }
    .animate-pulse {
        animation: pulse-red 3s infinite ease-in-out;
    }
    .shadow-sm-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .transition-all {
        transition: all 0.3s ease;
    }
</style>
@endsection