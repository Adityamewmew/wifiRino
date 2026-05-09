@if(session('success'))
    <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: 14px; font-weight: 600; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: 14px; font-weight: 600; border: 1px solid #fecaca; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif
@if(session('info'))
    <div style="background: #dbeafe; color: #1e3a5f; padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: 14px; font-weight: 600; border: 1px solid #93c5fd; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-info-circle"></i> {{ session('info') }}
    </div>
@endif
