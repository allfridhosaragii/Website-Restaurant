@extends('layouts.guest')
@section('title', __('messages.reservation_details_title'))
@section('content')
<section class="bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3"><span data-i18n="reservation_details_title">{{ __('messages.reservation_details_title') }}</span> #{{ $reservation->id }}</h1>
                <p class="lead opacity-75 mb-0" data-i18n="reservation_details_desc">{{ __('messages.reservation_details_desc') }}</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ url('/customer/reservations') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i><span data-i18n="back_btn">{{ __('messages.back_btn') }}</span>
                </a>
            </div>
        </div>
    </div>
</section>
<section class="section bg-cream">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" data-i18n="reservation_info">{{ __('messages.reservation_info') }}</h5>
                        @if($reservation->status === 'pending')
                            <span class="badge bg-warning text-dark fs-6" data-i18n="status_waiting">{{ __('messages.status_waiting') }}</span>
                        @elseif($reservation->status === 'accepted')
                            <span class="badge bg-success fs-6" data-i18n="status_accepted">{{ __('messages.status_accepted') }}</span>
                        @else
                            <span class="badge bg-danger fs-6" data-i18n="status_rejected">{{ __('messages.status_rejected') }}</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small" data-i18n="reservation_name">{{ __('messages.reservation_name') }}</label>
                                <p class="fw-bold mb-0">{{ $reservation->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small" data-i18n="reservation_email">{{ __('messages.reservation_email') }}</label>
                                <p class="fw-bold mb-0">{{ $reservation->email }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small" data-i18n="reservation_phone">{{ __('messages.reservation_phone') }}</label>
                                <p class="fw-bold mb-0">{{ $reservation->phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small" data-i18n="reservation_guests">{{ __('messages.reservation_guests') }}</label>
                                <p class="fw-bold mb-0">{{ $reservation->guests }} <span data-i18n="people">{{ __('messages.people') }}</span></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small" data-i18n="reservation_date">{{ __('messages.reservation_date') }}</label>
                                <p class="fw-bold mb-0">
                                    <i class="bi bi-calendar-event text-gold me-1"></i>
                                    {{ \Carbon\Carbon::parse($reservation->date)->translatedFormat('l, d F Y') }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small" data-i18n="reservation_time">{{ __('messages.reservation_time') }}</label>
                                <p class="fw-bold mb-0">
                                    <i class="bi bi-clock text-gold me-1"></i>
                                    {{ $reservation->time }}
                                </p>
                            </div>
                            @if($reservation->table_id)
                            <div class="col-md-6">
                                <label class="form-label text-muted small" data-i18n="table">{{ __('messages.table') }}</label>
                                <p class="fw-bold mb-0">
                                    <i class="bi bi-grid-3x3 text-gold me-1"></i>
                                    <span data-i18n="table">{{ __('messages.table') }}</span> {{ $reservation->table_id }}
                                </p>
                            </div>
                            @endif
                            @if($reservation->notes)
                            <div class="col-12">
                                <label class="form-label text-muted small" data-i18n="special_request">{{ __('messages.special_request') }}</label>
                                <p class="mb-0">{{ $reservation->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @if($reservation->admin_notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0" data-i18n="admin_notes_title">{{ __('messages.admin_notes_title') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-{{ $reservation->status === 'accepted' ? 'success' : ($reservation->status === 'rejected' ? 'danger' : 'info') }} mb-0">
                            {{ $reservation->admin_notes }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i><span data-i18n="payment_proof_title">{{ __('messages.payment_proof_title') }}</span></h5>
                    </div>
                    <div class="card-body">
                        @php
                            $proofImage = $reservation->deposit_proof ?: $reservation->payment_proof;
                        @endphp
                        @if($proofImage)
                            <img src="{{ $proofImage }}" 
                                 alt="{{ __('messages.payment_proof_title') }}" 
                                 class="img-fluid rounded"
                                 style="cursor: pointer;"
                                 onclick="window.open('{{ $proofImage }}', '_blank')">
                            @if($reservation->deposit_status)
                                <div class="mt-2 text-center">
                                    <span class="badge {{ $reservation->deposit_status === 'paid' ? 'bg-success' : ($reservation->deposit_status === 'refunded' ? 'bg-info' : 'bg-warning') }}">
                                        Deposit: {{ ucfirst($reservation->deposit_status) }}
                                    </span>
                                    @if($reservation->deposit_amount)
                                        <span class="badge bg-secondary ms-1">
                                            Rp {{ number_format($reservation->deposit_amount, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                            <p class="text-muted small mt-2 mb-0 text-center">
                                <i class="bi bi-zoom-in me-1"></i><span data-i18n="click_to_zoom">{{ __('messages.click_to_zoom') }}</span>
                            </p>
                        @else
                            <p class="text-muted text-center mb-0" data-i18n="no_payment_proof">{{ __('messages.no_payment_proof') }}</p>
                        @endif
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i><span data-i18n="timeline_title">{{ __('messages.timeline_title') }}</span></h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex gap-3 mb-3">
                                <div class="shrink-0">
                                    <span class="badge bg-primary rounded-circle p-2">
                                        <i class="bi bi-plus-lg"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-bold" data-i18n="reservation_created">{{ __('messages.reservation_created') }}</p>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($reservation->created_at)->format('d/m/Y H:i') }}</small>
                                </div>
                            </li>
                            @if($reservation->status !== 'pending')
                            <li class="d-flex gap-3">
                                <div class="shrink-0">
                                    <span class="badge bg-{{ $reservation->status === 'accepted' ? 'success' : 'danger' }} rounded-circle p-2">
                                        <i class="bi bi-{{ $reservation->status === 'accepted' ? 'check' : 'x' }}-lg"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-bold">
                                        @if($reservation->status === 'accepted')
                                            <span data-i18n="reservation_status_accepted">{{ __('messages.reservation_status_accepted') }}</span>
                                        @else
                                            <span data-i18n="reservation_status_rejected">{{ __('messages.reservation_status_rejected') }}</span>
                                        @endif
                                    </p>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($reservation->updated_at)->format('d/m/Y H:i') }}</small>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection