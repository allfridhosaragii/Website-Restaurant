@extends('layouts.guest')
@section('title', 'System Status')
@section('content')
<section class="min-vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #0B0E10 0%, #1a1f25 100%); padding-top: 100px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-lg" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(200,155,58,0.2) !important;">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <i class="bi bi-shield-lock-fill" style="font-size: 4rem; color: #D4AF37;"></i>
                        </div>
                        <h2 class="text-white mb-3 fw-bold">System Control</h2>
                        <p class="text-white-50 mb-4">Secret admin panel for maintenance control</p>
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        <div class="p-4 rounded-3 mb-4" style="background: rgba(0,0,0,0.3);">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-start">
                                    <h5 class="text-white mb-1">Maintenance Mode</h5>
                                    <small class="text-white-50">All pages except /project will show maintenance</small>
                                </div>
                                <form action="{{ url('/status/toggle') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-lg {{ $isMaintenanceMode ? 'btn-danger' : 'btn-success' }} rounded-pill px-4">
                                        @if($isMaintenanceMode)
                                            <i class="bi bi-toggle-on me-2"></i> ON
                                        @else
                                            <i class="bi bi-toggle-off me-2"></i> OFF
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="p-3 rounded-3" style="background: rgba(200,155,58,0.1); border: 1px solid rgba(200,155,58,0.3);">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle text-warning me-2"></i>
                                <small class="text-white-50 text-start">
                                    Status: <strong class="{{ $isMaintenanceMode ? 'text-danger' : 'text-success' }}">
                                        {{ $isMaintenanceMode ? 'MAINTENANCE ACTIVE' : 'NORMAL OPERATION' }}
                                    </strong>
                                </small>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
                            <small class="text-white-50">
                                <i class="bi bi-person-check me-1"></i> 
                                Logged in as: {{ auth()->user()->email }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection