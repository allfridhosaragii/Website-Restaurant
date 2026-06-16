@extends('layouts.admin')

@section('title', 'Absensi Karyawan')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Absensi Hari Ini</h1>
        <small class="text-muted">{{ now()->format('l, d F Y') }}</small>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow mb-4" x-data="attendanceApp()" x-init="init()">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-camera-fill"></i> Check-In / Check-Out
                    </h6>
                </div>
                <div class="card-body text-center">
                    @if($attendance && $attendance->check_out)
                        {{-- Both done --}}
                        <div class="py-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            <h4 class="mt-3 text-success">Absensi Selesai</h4>
                            <p class="text-muted">Check-in: <strong>{{ $attendance->check_in }}</strong> &nbsp;|&nbsp; Check-out: <strong>{{ $attendance->check_out }}</strong></p>
                            <span class="badge badge-{{ $attendance->status_badge }} px-3 py-2">{{ ucfirst($attendance->status) }}</span>
                        </div>
                    @else
                        {{-- Camera preview --}}
                        <div class="mb-3">
                            <video id="camera" autoplay playsinline class="rounded border" style="width: 100%; max-height: 300px; object-fit: cover; display: none;"></video>
                            <canvas id="canvas" style="display: none;"></canvas>
                            <div id="photoPreview" style="display: none; text-align: center;">
                                <img id="snapshot" class="rounded border mb-2" style="max-width: 100%; max-height: 300px;">
                                <br>
                                <button class="btn btn-sm btn-secondary" @click="retake()"><i class="bi bi-arrow-repeat"></i> Foto Ulang</button>
                            </div>
                            <div id="placeholder" class="py-5 text-muted">
                                <i class="bi bi-camera fs-1 d-block mb-2"></i>
                                Klik tombol di bawah untuk mengaktifkan kamera
                            </div>
                        </div>

                        @if(!$attendance)
                            {{-- Check-In --}}
                            <button class="btn btn-success btn-lg btn-block mb-2" @click="toggleCamera()" x-show="!cameraOn && !photoTaken">
                                <i class="bi bi-camera"></i> Aktifkan Kamera Check-In
                            </button>
                            <button class="btn btn-primary btn-lg btn-block mb-2" @click="capture()" x-show="cameraOn && !photoTaken">
                                <i class="bi bi-camera-fill"></i> Ambil Foto
                            </button>
                            <button class="btn btn-success btn-lg btn-block" @click="submitAttendance('checkin')" x-show="photoTaken" :disabled="isLoading">
                                <span x-show="!isLoading"><i class="bi bi-box-arrow-in-right"></i> CHECK IN Sekarang</span>
                                <span x-show="isLoading"><i class="bi bi-hourglass-split"></i> Memproses...</span>
                            </button>
                        @else
                            {{-- Already checked in, show check-out --}}
                            <div class="alert alert-success mb-3">
                                ✅ Check-in pukul <strong>{{ $attendance->check_in }}</strong>
                                — Status: <strong>{{ ucfirst($attendance->status) }}</strong>
                            </div>
                            <button class="btn btn-warning btn-lg btn-block mb-2" @click="toggleCamera()" x-show="!cameraOn && !photoTaken">
                                <i class="bi bi-camera"></i> Aktifkan Kamera Check-Out
                            </button>
                            <button class="btn btn-primary btn-lg btn-block mb-2" @click="capture()" x-show="cameraOn && !photoTaken">
                                <i class="bi bi-camera-fill"></i> Ambil Foto
                            </button>
                            <button class="btn btn-warning btn-lg btn-block" @click="submitAttendance('checkout')" x-show="photoTaken" :disabled="isLoading">
                                <span x-show="!isLoading"><i class="bi bi-box-arrow-right"></i> CHECK OUT Sekarang</span>
                                <span x-show="isLoading"><i class="bi bi-hourglass-split"></i> Memproses...</span>
                            </button>
                        @endif

                        <div class="mt-3" x-show="message !== ''" x-text="message" :class="isError ? 'alert alert-danger' : 'alert alert-success'"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('attendanceApp', () => ({
        cameraOn: false,
        photoTaken: false,
        photoBase64: '',
        isLoading: false,
        message: '',
        isError: false,
        stream: null,

        init() {},

        async toggleCamera() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
                const video = document.getElementById('camera');
                video.srcObject = this.stream;
                video.style.display = 'block';
                document.getElementById('placeholder').style.display = 'none';
                this.cameraOn = true;
            } catch(e) {
                alert('Tidak dapat mengakses kamera: ' + e.message);
            }
        },

        capture() {
            const video = document.getElementById('camera');
            const canvas = document.getElementById('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            this.photoBase64 = canvas.toDataURL('image/jpeg', 0.8);

            const snapshot = document.getElementById('snapshot');
            snapshot.src = this.photoBase64;
            document.getElementById('photoPreview').style.display = 'block';
            video.style.display = 'none';
            this.photoTaken = true;

            // Stop stream
            if (this.stream) this.stream.getTracks().forEach(t => t.stop());
        },

        retake() {
            this.photoTaken = false;
            this.photoBase64 = '';
            document.getElementById('photoPreview').style.display = 'none';
            this.toggleCamera();
        },

        async submitAttendance(type) {
            this.isLoading = true;
            this.message = '';
            const endpoint = type === 'checkin' ? '/attendance/check-in' : '/attendance/check-out';
            try {
                const r = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify({ photo: this.photoBase64 })
                });
                const d = await r.json();
                this.isError = !d.success;
                this.message = d.message;
                if (d.success) {
                    setTimeout(() => window.location.reload(), 1500);
                }
            } catch(e) {
                this.isError = true;
                this.message = 'Terjadi kesalahan. Coba lagi.';
            }
            this.isLoading = false;
        }
    }));
});
</script>
@endsection
