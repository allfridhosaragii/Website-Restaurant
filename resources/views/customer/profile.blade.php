@extends('layouts.guest')
@section('title', 'Profil Saya')
@section('content')
<section class="section bg-cream">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-1" data-i18n="my_profile_title">{{ __('messages.my_profile_title') }}</h3>
                <p class="text-muted mb-0" data-i18n="profile_desc">{{ __('messages.profile_desc') }}</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card text-center p-4">
                    <div class="mb-3">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center overflow-hidden" 
                             style="width: 100px; height: 100px; background-color: #f8f9fa;">
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" 
                                 class="w-100 h-100" style="object-fit: cover;">
                        </div>
                    </div>
                    <h5 class="mb-1">{{ Auth::user()->name ?? 'Customer' }}</h5>
                    <p class="text-muted mb-2">{{ Auth::user()->email ?? 'email@example.com' }}</p>
                    
                    @php
                        $user = Auth::user();
                        $currentTier = $user->tier;
                        $nextTier = $currentTier ? $currentTier->nextTier() : \App\Models\MembershipTier::where('sort_order', '>', 0)->orderBy('sort_order', 'asc')->first();
                        $spent = $user->total_spent ?? 0;
                        $progress = 100;
                        if ($nextTier && $currentTier) {
                            $range = $nextTier->min_spent - $currentTier->min_spent;
                            $current = $spent - $currentTier->min_spent;
                            $progress = min(100, max(0, ($current / $range) * 100));
                        } elseif ($nextTier && !$currentTier) {
                            $progress = min(100, max(0, ($spent / $nextTier->min_spent) * 100));
                        }
                    @endphp

                    <div class="mb-3">
                        <span class="badge" style="background-color: {{ $user->membership_tier == 'platinum' ? '#e5e4e2' : ($user->membership_tier == 'gold' ? '#ffd700' : ($user->membership_tier == 'silver' ? '#c0c0c0' : '#cd7f32')) }}; color: #000;">
                            <i class="bi bi-star-fill me-1"></i> {{ strtoupper($user->membership_tier ?? 'BRONZE') }} VIP
                        </span>
                    </div>

                    @if($nextTier)
                    <div class="text-start mb-3 px-2">
                        <div class="d-flex justify-content-between text-xs mb-1">
                            <span class="text-muted" style="font-size: 0.75rem;">Total Belanja: Rp {{ number_format($spent, 0, ',', '.') }}</span>
                            <span class="text-muted" style="font-size: 0.75rem;">Menuju {{ $nextTier->name }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="text-xs text-end mt-1 text-muted" style="font-size: 0.7rem;">
                            Sisa Rp {{ number_format(max(0, $nextTier->min_spent - $spent), 0, ',', '.') }}
                        </div>
                    </div>
                    @endif

                    <hr>
                    <div class="row text-center">
                        <div class="col-4">
                            <h5 class="mb-0">{{ $totalOrders }}</h5>
                            <small class="text-muted" data-i18n="profile_orders">{{ __('messages.profile_orders') }}</small>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">{{ number_format($points, 0, ',', '.') }}</h5>
                            <small class="text-muted" data-i18n="profile_points">{{ __('messages.profile_points') }}</small>
                        </div>
                        <div class="col-4">
                            <h5 class="mb-0">{{ $totalReservations }}</h5>
                            <small class="text-muted" data-i18n="profile_reservations">{{ __('messages.profile_reservations') }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-pencil-square text-primary me-2"></i><span data-i18n="edit_profile_title">{{ __('messages.edit_profile_title') }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="#" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label" data-i18n="full_name">{{ __('messages.full_name') }}</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ Auth::user()->name ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label" data-i18n="phone_number">{{ __('messages.phone_number') }}</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="{{ Auth::user()->phone ?? '' }}" placeholder="08123456789">
                                </div>
                                <div class="col-12">
                                    <label for="email" class="form-label" data-i18n="email">{{ __('messages.email') }}</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ Auth::user()->email ?? '' }}" readonly>
                                    <small class="text-muted" data-i18n="email_readonly">{{ __('messages.email_readonly') }}</small>
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label" data-i18n="address_label">{{ __('messages.address_label') }}</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" 
                                              placeholder="{{ __('messages.address_placeholder') }}" data-i18n="address_placeholder"></textarea>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-2"></i><span data-i18n="save_changes_btn">{{ __('messages.save_changes_btn') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-shield-lock text-warning me-2"></i><span data-i18n="change_password_title">{{ __('messages.change_password_title') }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(Auth::user()->google_id && !Auth::user()->password)
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                <span data-i18n="google_login_note">{{ __('messages.google_login_note') }}</span>
                            </div>
                        @else
                            <form action="#" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="current_password" class="form-label" data-i18n="current_password">{{ __('messages.current_password') }}</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="new_password" class="form-label" data-i18n="new_password">{{ __('messages.new_password') }}</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="confirm_password" class="form-label" data-i18n="confirm_new_password">{{ __('messages.confirm_new_password') }}</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-key me-2"></i><span data-i18n="change_password_btn">{{ __('messages.change_password_btn') }}</span>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ url('/customer/dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i><span data-i18n="back_to_dashboard">{{ __('messages.back_to_dashboard') }}</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection