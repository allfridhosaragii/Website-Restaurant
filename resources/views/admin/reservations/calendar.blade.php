@extends('layouts.admin')

@section('title', 'Kalender Reservasi')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    #calendar {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .fc-event {
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4" x-data="reservationCalendar()">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kalender Reservasi</h1>
    </div>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Reservation Detail Modal -->
    <div class="modal fade" id="resModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" x-text="selectedRes?.title">Detail Reservasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Nama:</span>
                            <strong x-text="selectedRes?.extendedProps.name"></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>No. HP:</span>
                            <strong x-text="selectedRes?.extendedProps.phone"></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Jumlah Tamu:</span>
                            <strong x-text="selectedRes?.extendedProps.guests + ' Orang'"></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Status Deposit:</span>
                            <span class="badge" 
                                  :class="{'bg-success': selectedRes?.extendedProps.deposit_status == 'paid', 'bg-warning text-dark': selectedRes?.extendedProps.deposit_status == 'pending', 'bg-secondary': selectedRes?.extendedProps.deposit_status == 'refunded'}" 
                                  x-text="selectedRes?.extendedProps.deposit_status"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Status:</span>
                            <strong x-text="selectedRes?.extendedProps.status"></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Mulai:</span>
                            <strong x-text="formatDate(selectedRes?.start)"></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Selesai:</span>
                            <strong x-text="formatDate(selectedRes?.end)"></strong>
                        </li>
                    </ul>
                    
                    <div x-show="selectedRes?.extendedProps.status !== 'checked_in' && selectedRes?.extendedProps.status !== 'completed' && selectedRes?.extendedProps.status !== 'cancelled'">
                        <form :action="`/admin/reservations/${selectedRes?.id}/check-in`" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Check-in Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
function reservationCalendar() {
    return {
        selectedRes: null,
        modal: null,
        
        init() {
            const modalEl = document.getElementById('resModal');
            if(modalEl) {
                this.modal = new bootstrap.Modal(modalEl);
            }

            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                slotMinTime: '09:00:00',
                slotMaxTime: '23:00:00',
                allDaySlot: false,
                editable: true, // allow drag and drop
                eventResizableFromStart: true,
                events: '/admin/reservations/calendar/data',
                
                eventClick: (info) => {
                    this.selectedRes = info.event;
                    this.modal.show();
                },
                
                eventDrop: (info) => {
                    this.updateReservation(info.event, info.revert);
                },
                
                eventResize: (info) => {
                    this.updateReservation(info.event, info.revert);
                }
            });
            calendar.render();
        },
        
        updateReservation(event, revertFunc) {
            const data = {
                _token: '{{ csrf_token() }}',
                start: event.start.toISOString(),
                end: event.end ? event.end.toISOString() : null,
            };
            
            fetch(`/admin/reservations/${event.id}/calendar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if(data.error) {
                    alert(data.error);
                    revertFunc();
                } else {
                    // Success
                    console.log('Reservation updated');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan saat memindahkan jadwal.');
                revertFunc();
            });
        },
        
        formatDate(dateObj) {
            if(!dateObj) return '-';
            return dateObj.toLocaleString('id-ID', {
                dateStyle: 'medium',
                timeStyle: 'short'
            });
        }
    }
}
</script>
@endpush
