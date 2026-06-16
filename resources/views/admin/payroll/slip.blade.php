<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Slip Gaji — {{ $monthName }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Courier New', monospace; font-size: 13px; color: #222; background: #fff; padding: 20px; }
        .slip { max-width: 500px; margin: 0 auto; border: 2px solid #222; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #222; padding-bottom: 12px; margin-bottom: 16px; }
        .header h2 { font-size: 18px; letter-spacing: 2px; }
        .header p { font-size: 11px; margin-top: 4px; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 16px; letter-spacing: 1px; text-transform: uppercase; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .info-row .label { color: #555; }
        .divider { border-top: 1px dashed #999; margin: 12px 0; }
        .amount-row { display: flex; justify-content: space-between; padding: 3px 0; }
        .amount-row.deduction span:last-child { color: #c00; }
        .amount-row.bonus span:last-child { color: #070; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 15px; font-weight: bold; border-top: 2px solid #222; border-bottom: 2px solid #222; margin: 8px 0; }
        .status-badge { text-align: center; margin-top: 16px; }
        .status-badge span { display: inline-block; padding: 4px 16px; border: 1px solid #222; border-radius: 4px; font-weight: bold; letter-spacing: 1px; font-size: 12px; }
        .footer { text-align: center; margin-top: 20px; font-size: 11px; color: #888; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="slip">
        <div class="header">
            <h2>CULINAIRE RESTAURANT</h2>
            <p>Jl. Contoh Alamat No. 1, Kota</p>
            <p>Telp: (021) 1234-5678</p>
        </div>

        <div class="title">SLIP GAJI — {{ $monthName }}</div>

        <div class="info-row">
            <span class="label">Nama Karyawan</span>
            <span><strong>{{ $payroll->employee_name }}</strong></span>
        </div>
        <div class="info-row">
            <span class="label">Jabatan</span>
            <span>{{ ucfirst($payroll->employee_role ?? 'Staff') }}</span>
        </div>
        <div class="info-row">
            <span class="label">Periode</span>
            <span>{{ $monthName }}</span>
        </div>

        <div class="divider"></div>

        <div class="amount-row">
            <span>Gaji Pokok</span>
            <span>Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</span>
        </div>
        @if($payroll->attendance_bonus > 0)
        <div class="amount-row bonus">
            <span>Bonus Kehadiran</span>
            <span>+ Rp {{ number_format($payroll->attendance_bonus, 0, ',', '.') }}</span>
        </div>
        @endif
        @if($payroll->commission > 0)
        <div class="amount-row bonus">
            <span>Komisi</span>
            <span>+ Rp {{ number_format($payroll->commission, 0, ',', '.') }}</span>
        </div>
        @endif
        @if($payroll->allowance > 0)
        <div class="amount-row bonus">
            <span>Tunjangan</span>
            <span>+ Rp {{ number_format($payroll->allowance, 0, ',', '.') }}</span>
        </div>
        @endif
        @if($payroll->deduction > 0)
        <div class="amount-row deduction">
            <span>Potongan</span>
            <span>- Rp {{ number_format($payroll->deduction, 0, ',', '.') }}</span>
        </div>
        @endif

        <div class="total-row">
            <span>TOTAL GAJI</span>
            <span>Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</span>
        </div>

        @if($payroll->notes)
        <div style="margin: 8px 0; font-size: 11px; color: #555;">
            <em>Catatan: {{ $payroll->notes }}</em>
        </div>
        @endif

        <div class="status-badge">
            @if($payroll->status === 'paid')
                <span style="color: green; border-color: green;">✓ SUDAH DIBAYAR — {{ $payroll->paid_at ? \Carbon\Carbon::parse($payroll->paid_at)->format('d/m/Y') : '' }}</span>
            @elseif($payroll->status === 'approved')
                <span style="color: blue; border-color: blue;">DISETUJUI</span>
            @else
                <span style="color: gray;">DRAFT</span>
            @endif
        </div>

        <div class="footer">
            Dicetak pada {{ now()->format('d/m/Y H:i') }}<br>
            Slip ini dibuat secara otomatis oleh sistem.
        </div>
    </div>

    <div class="no-print" style="text-align:center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 8px 24px; cursor: pointer; background: #222; color: #fff; border: none; border-radius: 4px;">
            🖨️ Cetak Slip
        </button>
        <button onclick="window.close()" style="padding: 8px 24px; cursor: pointer; margin-left: 8px; border: 1px solid #999; border-radius: 4px; background: #fff;">
            Tutup
        </button>
    </div>
</body>
</html>
