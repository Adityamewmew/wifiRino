@extends('layouts.admin')
@section('title', 'Dashboard Admin - Sans Speed')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
@php
    $formatRp = fn($n) => 'Rp ' . number_format($n, 0, ',', '.');
@endphp

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 15px;">
    <h1 class="page-title" style="margin: 0; font-size: 24px; font-weight: 700; color: #f8fafc;">
        <i class="fas fa-chart-line" style="color: #6366f1;"></i> Ringkasan Eksekutif
    </h1>
    <form method="GET" action="{{ url('/dashboard-admin') }}" style="display: flex; gap: 10px; align-items: center; background: var(--card-bg, rgba(30,41,59,0.9)); padding: 6px 12px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid var(--border-color, #e5e7eb);">
        <span style="font-size: 13px; font-weight: 600; color: #64748b; margin-right: 5px;"><i class="fas fa-calendar-alt"></i> Periode:</span>
        <select name="bulan" onchange="this.form.submit()" style="border: none; background: #f8fafc; padding: 6px 10px; border-radius: 6px; font-size: 14px; font-weight: 600; color: #0f172a;">
            @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $val => $label)
                <option value="{{ $val }}" {{ str_pad($bulan,2,'0',STR_PAD_LEFT) == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="tahun" onchange="this.form.submit()" style="border: none; background: #f8fafc; padding: 6px 10px; border-radius: 6px; font-size: 14px; font-weight: 600; color: #0f172a;">
            @for($y = date('Y') + 3; $y >= date('Y') - 2; $y--)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>
</div>

<!-- STATS GRID -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));">
    <div class="card-3d">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div class="stat-icon stat-icon-blue"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <h3 style="color: #60a5fa; font-size: 14px; font-weight: 800;">Pelanggan Aktif</h3>
                <p>{{ $pelangganAktif }}</p>
            </div>
        </div>
        <div style="margin-top: 10px; font-size: 12px; color: rgba(255,255,255,0.5);">dari {{ $totalPelanggan }} total terdaftar</div>
    </div>

    <div class="card-3d" style="border: 1px solid rgba(16,185,129,0.4); box-shadow: 0 0 15px rgba(16,185,129,0.1);">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div class="stat-icon" style="background: linear-gradient(135deg,#059669,#10b981); box-shadow: 0 8px 15px rgba(16,185,129,0.35);"><i class="fas fa-user-plus"></i></div>
            <div class="stat-info">
                <h3 style="color: #34d399; font-size: 14px; font-weight: 800;">Pelanggan Baru</h3>
                <p>{{ $pelangganBaru }}</p>
            </div>
        </div>
        <div style="margin-top: 10px; font-size: 12px; color: rgba(255,255,255,0.5);">Daftar bulan ini</div>
    </div>

    <div class="card-3d" style="border: 1px solid rgba(239,68,68,0.3); box-shadow: 0 0 15px rgba(239,68,68,0.08);">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div class="stat-icon" style="background: linear-gradient(135deg,#b91c1c,#ef4444); box-shadow: 0 8px 15px rgba(239,68,68,0.35);"><i class="fas fa-user-times"></i></div>
            <div class="stat-info">
                <h3 style="color: #f87171; font-size: 14px; font-weight: 800;">Berhenti Bulan Ini</h3>
                <p>{{ $pelangganBerhenti }}</p>
            </div>
        </div>
        <div style="margin-top: 10px; font-size: 12px; color: rgba(255,255,255,0.5);">Status berhenti di periode ini</div>
    </div>

    <div class="card-3d">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div class="stat-icon stat-icon-green"><i class="fas fa-wallet"></i></div>
            <div class="stat-info">
                <h3 style="color: #34d399; font-size: 14px; font-weight: 800;">Sudah Bayar</h3>
                <p>{{ $jmlLunas }}</p>
            </div>
        </div>
        <div style="margin-top: 10px; font-size: 12px; color: rgba(255,255,255,0.5);">Total: {{ $canViewFinance ? $formatRp($nominalLunas) : 'Disembunyikan' }}</div>
    </div>

    <div class="card-3d" style="border: 1px solid rgba(248, 113, 113, 0.3); box-shadow: 0 0 15px rgba(248, 113, 113, 0.1);">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div class="stat-icon stat-icon-red"><i class="fas fa-exclamation-circle"></i></div>
            <div class="stat-info">
                <h3 style="color: #f87171; font-size: 14px; font-weight: 800;">Belum Bayar</h3>
                <p>{{ $jmlBelum }}</p>
            </div>
        </div>
        <div style="margin-top: 10px; font-size: 12px; color: rgba(255,255,255,0.5);">Potensi: {{ $canViewFinance ? $formatRp($nominalBelum) : 'Disembunyikan' }}</div>
    </div>

    <div class="card-3d">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div class="stat-icon stat-icon-orange"><i class="fas fa-receipt"></i></div>
            <div class="stat-info">
                <h3 style="color: #fbbf24; font-size: 14px; font-weight: 800;">Total Tagihan Dibuat</h3>
                <p>{{ $totalTagihan }}</p>
            </div>
        </div>
        <div style="margin-top: 10px;">
            <div style="background: rgba(255,255,255,0.1); border-radius: 4px; height: 6px; overflow: hidden;">
                <div style="height: 100%; background: linear-gradient(90deg, #10b981, #34d399); border-radius: 4px; width: {{ $persenLunas }}%; transition: width 0.8s ease;"></div>
            </div>
            <div style="font-size: 11px; color: rgba(255,255,255,0.5); margin-top: 4px;">{{ $persenLunas }}% sudah terbayar</div>
        </div>
    </div>
</div>

<!-- CHART -->
@if($canViewFinance)
<div class="card-3d" style="padding: 24px; margin-bottom: 24px;">
    <h2 style="font-size: 16px; margin: 0 0 20px 0; font-weight: 700; color: #f8fafc;"><i class="fas fa-chart-area" style="color: #6366f1;"></i> Tren Pemasukan 12 Bulan Terakhir</h2>
    <div style="width: 100%; height: 300px;">
        <canvas id="revenueChart"></canvas>
    </div>
</div>
@endif

<!-- TUNGGAKAN TABLE -->
<div class="card-3d table-highlight" style="padding: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 20px; margin: 0; font-weight: 800; display: flex; align-items: center; gap: 10px; color: #fb7185;">
                <i class="fas fa-exclamation-triangle"></i> Tagihan Belum Bayar
                <span style="font-size: 13px; font-weight: 500; opacity:0.7;">— {{ $namaBulan[$bulan] }} {{ $tahun }}</span>
            </h2>
            <div class="dashboard-summary-pills" style="margin-top: 8px;">
                <span class="summary-pill"><span style="opacity:.8;">Tunggakan</span> <strong>{{ $jmlBelum }}</strong></span>
                <span class="summary-pill"><span style="opacity:.8;">Potensi</span> <strong>{{ $canViewFinance ? $formatRp($nominalBelum) : 'Disembunyikan' }}</strong></span>
            </div>
        </div>
        <a href="{{ url('/tagihan') }}" class="btn-primary" style="padding: 10px 20px; font-size: 14px; background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4); text-decoration: none; border-radius: 8px;">
            <i class="fas fa-file-invoice"></i> Lihat Semua Tagihan
        </a>
    </div>
    <div class="table-scroll-mobile">
        <table class="table-min-mobile">
            <thead>
                <tr>
                    <th style="color: #fb7185; border-bottom: 1px solid rgba(244, 63, 94, 0.3);">ID Pelanggan</th>
                    <th style="color: #fb7185; border-bottom: 1px solid rgba(244, 63, 94, 0.3);">Nama</th>
                    <th style="color: #fb7185; border-bottom: 1px solid rgba(244, 63, 94, 0.3);">Area</th>
                    <th style="color: #fb7185; border-bottom: 1px solid rgba(244, 63, 94, 0.3);">Total Tagihan</th>
                    <th style="color: #fb7185; border-bottom: 1px solid rgba(244, 63, 94, 0.3);">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tunggakanPreview as $t)
                <tr style="background: rgba(244, 63, 94, 0.05);">
                    <td style="font-weight: 700; color: #fb7185;">{{ $t['idPelanggan'] ?? '-' }}</td>
                    <td style="font-weight: 600; color: white;">{{ $t['namaPelanggan'] ?? '-' }}</td>
                    <td style="color: rgba(255,255,255,0.9);">{{ $t['area'] ?? '-' }}</td>
                    <td style="font-weight: 700; color: white;">{{ $canViewFinance ? $formatRp($t['totalTagihan'] ?? 0) : 'Disembunyikan' }}</td>
                    <td><span class="badge badge-danger">Belum Bayar</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; color: #34d399; padding: 20px;">
                        <i class="fas fa-check-circle"></i> Semua tagihan {{ $namaBulan[$bulan] }} {{ $tahun }} sudah lunas!
                    </td>
                </tr>
                @endforelse
                @if($jmlBelum > 5)
                <tr>
                    <td colspan="5" style="text-align:center; font-size:12px; color: rgba(255,255,255,0.4); padding:10px;">
                        Menampilkan 5 dari {{ $jmlBelum }} tunggakan — <a href="{{ url('/tagihan') }}" style="color:#60a5fa;">Lihat semua</a>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
@if($canViewFinance && count($trendLabels) > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: @json($trendData),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                borderWidth: 3,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8', callback: v => v >= 1000000 ? (v/1000000)+' Jt' : v >= 1000 ? (v/1000)+' Rb' : v } },
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
            }
        }
    });
});
</script>
@endif
@endpush
