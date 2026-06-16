@extends('layouts.app')

@section('title', 'Lacak Pesanan')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 text-center">
                    <h4 class="mb-0 fw-bold">Status Pesanan</h4>
                    <p class="text-muted mb-0">No: {{ $order->order_number }}</p>
                    @if($order->guest_name)
                    <p class="text-muted small">Atas Nama: {{ $order->guest_name }}</p>
                    @endif
                </div>
                <div class="card-body p-4">
                    
                    <div class="tracking-container position-relative mb-5 mt-3">
                        <div class="progress position-absolute" style="height: 4px; top: 24px; left: 10%; right: 10%; z-index: 1;">
                            <div class="progress-bar bg-success transition-all" id="trackingProgress" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between position-relative" style="z-index: 2;">
                            <!-- Step 1: Diterima -->
                            <div class="tracking-step text-center" id="step-pending">
                                <div class="step-icon bg-white border border-success text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 shadow-sm" style="width: 50px; height: 50px;">
                                    <i class="bi bi-journal-check fs-4"></i>
                                </div>
                                <span class="d-block fw-bold small">Diterima</span>
                            </div>
                            
                            <!-- Step 2: Diproses -->
                            <div class="tracking-step text-center opacity-50" id="step-processing">
                                <div class="step-icon bg-white border border-secondary text-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 50px; height: 50px;">
                                    <i class="bi bi-fire fs-4"></i>
                                </div>
                                <span class="d-block fw-bold small">Dimasak</span>
                            </div>
                            
                            <!-- Step 3: Siap Diambil -->
                            <div class="tracking-step text-center opacity-50" id="step-ready">
                                <div class="step-icon bg-white border border-secondary text-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 50px; height: 50px;">
                                    <i class="bi bi-bag-check fs-4"></i>
                                </div>
                                <span class="d-block fw-bold small">Siap</span>
                            </div>
                            
                            <!-- Step 4: Selesai -->
                            <div class="tracking-step text-center opacity-50" id="step-completed">
                                <div class="step-icon bg-white border border-secondary text-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 50px; height: 50px;">
                                    <i class="bi bi-check2-all fs-4"></i>
                                </div>
                                <span class="d-block fw-bold small">Selesai</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center bg-light p-3 rounded mb-4">
                        <h5 class="text-primary mb-1" id="statusMessage">Pesanan Anda sedang diverifikasi...</h5>
                        <p class="text-muted small mb-0" id="statusDescription">Mohon tunggu sebentar.</p>
                    </div>

                    @if($order->payment_status !== 'paid')
                    <div class="alert alert-warning text-center" id="paymentAlert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Pesanan Anda belum dibayar.
                        <a href="{{ url('/customer/payment/' . $order->id . '/pay') }}" class="btn btn-sm btn-primary mt-2 d-block mx-auto" style="width: fit-content;">Bayar Sekarang</a>
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        @if(auth()->check())
                        <a href="{{ url('/customer/orders') }}" class="btn btn-outline-primary">Lihat Daftar Pesanan</a>
                        @else
                        <a href="{{ url('/customer/orders/create') }}" class="btn btn-outline-primary">Pesan Lagi</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const orderNumber = '{{ $order->order_number }}';
    
    function updateTrackingUI(status) {
        const steps = ['pending', 'processing', 'ready', 'completed'];
        let currentStepIndex = steps.indexOf(status);
        
        // Handle cancelled separately
        if (status === 'cancelled') {
            document.getElementById('statusMessage').textContent = 'Pesanan Dibatalkan';
            document.getElementById('statusMessage').className = 'text-danger mb-1';
            document.getElementById('statusDescription').textContent = 'Mohon maaf, pesanan Anda telah dibatalkan.';
            document.getElementById('trackingProgress').classList.replace('bg-success', 'bg-danger');
            document.getElementById('trackingProgress').style.width = '100%';
            
            steps.forEach(s => {
                let stepEl = document.getElementById('step-' + s);
                let iconEl = stepEl.querySelector('.step-icon');
                stepEl.classList.remove('opacity-50');
                iconEl.classList.replace('border-success', 'border-danger');
                iconEl.classList.replace('text-success', 'text-danger');
                iconEl.classList.replace('border-secondary', 'border-danger');
                iconEl.classList.replace('text-secondary', 'text-danger');
            });
            return;
        }
        
        // Update Progress Bar
        const progressPercentages = {
            'pending': 0,
            'processing': 33,
            'ready': 66,
            'completed': 100
        };
        
        document.getElementById('trackingProgress').style.width = progressPercentages[status] + '%';
        
        // Update text
        const messages = {
            'pending': { title: 'Pesanan Diterima', desc: 'Pesanan Anda telah kami terima dan sedang menunggu proses dapur.' },
            'processing': { title: 'Sedang Dimasak', desc: 'Koki kami sedang menyiapkan pesanan Anda dengan penuh cinta.' },
            'ready': { title: 'Siap Diambil / Diantar', desc: 'Pesanan Anda sudah siap! Silakan ambil atau tunggu pelayan kami mengantarkannya.' },
            'completed': { title: 'Pesanan Selesai', desc: 'Selamat menikmati hidangan kami! Jangan lupa berikan review.' }
        };
        
        if (messages[status]) {
            document.getElementById('statusMessage').textContent = messages[status].title;
            document.getElementById('statusDescription').textContent = messages[status].desc;
        }

        // Update step UI
        steps.forEach((s, idx) => {
            let stepEl = document.getElementById('step-' + s);
            let iconEl = stepEl.querySelector('.step-icon');
            
            if (idx <= currentStepIndex) {
                stepEl.classList.remove('opacity-50');
                iconEl.classList.add('border-success', 'text-success', 'shadow-sm');
                iconEl.classList.remove('border-secondary', 'text-secondary');
            } else {
                stepEl.classList.add('opacity-50');
                iconEl.classList.remove('border-success', 'text-success', 'shadow-sm');
                iconEl.classList.add('border-secondary', 'text-secondary');
            }
        });
    }

    // Initial load
    updateTrackingUI('{{ $order->status }}');

    // Poll every 10 seconds
    if ('{{ $order->status }}' !== 'completed' && '{{ $order->status }}' !== 'cancelled') {
        setInterval(() => {
            fetch(`/api/track/${orderNumber}`)
                .then(res => res.json())
                .then(data => {
                    updateTrackingUI(data.status);
                    if (data.status === 'completed' || data.status === 'cancelled') {
                        // Stop polling implicitly since it reached end state (or let it run if you want, but better to stop if we could)
                    }
                })
                .catch(err => console.error('Polling error', err));
        }, 10000);
    }
</script>
@endpush
