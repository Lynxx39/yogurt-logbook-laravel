@extends('layouts.app')
@section('title', 'Detail Logbook — ' . $student->name)

@php
use App\Services\EvaluatorService;
$stagesDef = EvaluatorService::stagesDef();
$total = count($stagesDef);
$done = EvaluatorService::completedStageCount($stagesData);
$s1 = $stagesData[1]['data'] ?? null;
@endphp

@section('content')
<div class="dashboard teacher-dashboard">
  <header class="teacher-header">
    <div class="teacher-header-left">
      <a href="{{ route('teacher.dashboard') }}" class="btn btn-back-teacher" style="display:inline-flex;align-items:center;gap:6px;">
        <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Kembali
      </a>
      <div class="header-divider"></div>
      <div>
        <h1>
          Logbook — {{ $s1['kelompok'] ?? $student->name }}
        </h1>
        <p>
          {{ $student->name }} &nbsp;•&nbsp;
          {{ $s1['ekstrak'] ?? 'Tanpa Ekstrak' }} &nbsp;•&nbsp;
          {{ $done }}/{{ $total }} Tahap &nbsp;•&nbsp;
          Bergabung {{ $student->created_at->format('d M Y') }}
        </p>
      </div>
    </div>
    <div class="teacher-header-right">
      <form method="POST" action="/logout" style="margin: 0;">@csrf
        <button class="btn btn-logout-teacher" style="display:inline-flex;align-items:center;gap:6px;">
          <i data-lucide="log-out" style="width:14px;height:14px;"></i><span class="btn-logout-text"> Keluar</span>
        </button>
      </form>
    </div>
  </header>

  @if(session('success'))
    <div class="flash-success" style="margin: 20px 36px 0;">{{ session('success') }}</div>
  @endif

  {{-- EVALUATION --}}
  @if($evaluation)
    <div class="eval-wrapper">
      @include('partials.evaluation-card', compact('evaluation'))
    </div>
  @else
    <div class="info-banner">
      <i data-lucide="info" style="width:16px;height:16px;color:var(--accent);flex-shrink:0;"></i>
      <span>Evaluasi tersedia setelah siswa menyelesaikan Pengamatan Final (Jam ke-12). Saat ini {{ $done }}/{{ $total }} tahap selesai.</span>
    </div>
  @endif

  {{-- REKAPITULASI TABLE --}}
  @if($rekap)
  <div class="rekap-section">
    <div class="rekap-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
      <div>
        <h2>
          <i data-lucide="bar-chart-3" style="width:20px;height:20px;vertical-align:middle;margin-right:6px;color:var(--accent);"></i>
          Tabel Rekapitulasi Pengamatan
        </h2>
      </div>
      <button type="button" id="btn-download-rekap" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
        <i data-lucide="download" style="width: 14px; height: 14px;"></i> Unduh Gambar
      </button>
    </div>
    <div class="rekap-table-wrap">
      <table class="rekap-table">
        <thead>
          <tr><th>Waktu</th><th>Warna</th><th>Aroma</th><th>Rasa</th><th>Tekstur</th><th>pH</th></tr>
        </thead>
        <tbody>
          @foreach($rekap as $row)
          <tr class="{{ $row['waktu'] === 12 ? 'row-final' : '' }}">
            <td class="td-waktu"><strong>{{ $row['label'] }}</strong></td>
            <td>
              <div class="rekap-val">{{ $row['warna'] }}</div>
              @php
                $warnaNormal = $row['warna_normal'] ?? null;
                // If we have an overall evaluation, override final-stage warna status using its 'Warna' indicator
                if (($row['waktu'] ?? null) === 12 && isset($evaluation['indicators'])) {
                  foreach ($evaluation['indicators'] as $ind) {
                    if ($ind['id'] === 5) { $warnaNormal = $ind['passed']; break; }
                  }
                }
              @endphp
              @if($warnaNormal !== null)
                <div class="rekap-status {{ EvaluatorService::normalClass($warnaNormal) }}">{{ EvaluatorService::normalLabel($warnaNormal) }}</div>
              @endif
            </td>
            <td><div class="rekap-val">{{ $row['aroma'] }}</div>
              @if($row['aroma_normal'] !== null)
                <div class="rekap-status {{ EvaluatorService::normalClass($row['aroma_normal']) }}">{{ EvaluatorService::normalLabel($row['aroma_normal']) }}</div>
              @endif</td>
            <td><div class="rekap-val">{{ $row['rasa'] }}</div>
              @if($row['rasa_normal'] !== null)
                <div class="rekap-status {{ EvaluatorService::normalClass($row['rasa_normal']) }}">{{ EvaluatorService::normalLabel($row['rasa_normal']) }}</div>
              @endif</td>
            <td><div class="rekap-val">{{ $row['tekstur'] }}</div>
              @if($row['tekstur_normal'] !== null)
                <div class="rekap-status {{ EvaluatorService::normalClass($row['tekstur_normal']) }}">{{ EvaluatorService::normalLabel($row['tekstur_normal']) }}</div>
              @endif</td>
            <td class="td-ph">
              @if($row['ph'] !== null)<span class="ph-badge">{{ $row['ph'] }}</span>
              @else<span class="ph-empty">—</span>@endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  {{-- STAGE ACCORDION --}}
  <div class="stages-accordion">
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:12px">Detail Isian Per Tahap</h2>
    @foreach($stagesDef as $num => $def)
      @php $hasData = isset($stagesData[$num]); @endphp
      <div class="stage-card {{ $hasData ? 'filled' : 'empty' }}">
        <div class="stage-card-header" style="--stage-color:{{ $def['color'] }}; display:flex; align-items:center;"
             onclick="toggleCard({{ $num }})" role="button">
          
          <span class="stage-card-icon" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:{{ $hasData ? 'var(--success-dim)' : 'var(--surface-3)' }};border:1px solid {{ $hasData ? 'var(--success-border)' : 'var(--border)' }};margin-right:12px;flex-shrink:0;">
            @if($hasData)
              <i data-lucide="check" style="width:16px;height:16px;color:var(--success);"></i>
            @else
              <i data-lucide="{{ [1=>'clipboard-list', 2=>'flask-conical', 3=>'clock', 4=>'timer', 5=>'trending-up'][$num] ?? 'circle' }}" style="width:16px;height:16px;color:{{ $def['color'] }};"></i>
            @endif
          </span>

          <div class="stage-card-meta" style="flex:1;">
            <div class="stage-card-title">Tahap {{ $num }}: {{ $def['title'] }}</div>
            <div class="stage-card-sub">
              @if($hasData) Diisi: {{ \Carbon\Carbon::parse($stagesData[$num]['submitted_at'])->timezone(config('app.timezone'))->format('d M Y, H:i') }}
              @else Belum diisi @endif
            </div>
          </div>
          <span class="stage-toggle" id="toggle-{{ $num }}" style="transition: transform var(--transition); display: inline-block;">
            <i data-lucide="chevron-down" style="width:16px;height:16px;color:var(--text-muted);"></i>
          </span>
        </div>
        @if($hasData && $num < $total)
        <div class="stage-card-body" id="body-{{ $num }}" style="display:none">
          @include('student.partials.stage-view', [
            'stageNum'    => $num,
            'data'        => $stagesData[$num]['data'],
            'submittedAt' => $stagesData[$num]['submitted_at'],
            'stagesData'  => $stagesData,
          ])
        </div>
        @endif
      </div>
    @endforeach
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
function toggleCard(n) {
  const body = document.getElementById('body-'+n);
  const toggle = document.getElementById('toggle-'+n);
  if (!body) return;
  const open = body.style.display !== 'none';
  body.style.display = open ? 'none' : 'block';
  if (toggle) toggle.style.transform = open ? '' : 'rotate(180deg)';
}

document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btn-download-rekap');
    if (btn) {
        btn.addEventListener('click', function () {
            const tableWrap = document.querySelector('.rekap-section');
            if (!tableWrap) return;
            
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i data-lucide="loader-2" style="width:14px;height:14px;margin-right:6px;display:inline-block;animation:spin 1s linear infinite;"></i> Mengunduh...';
            if (window.lucide) window.lucide.createIcons();
            
            html2canvas(tableWrap, {
                backgroundColor: null,
                scale: 2,
                useCORS: true,
                logging: false,
                windowWidth: 1200,
                onclone: function (clonedDoc) {
                    const style = clonedDoc.createElement('style');
                    style.innerHTML = `
                        *, *::before, *::after {
                            animation: none !important;
                            transition: none !important;
                            transform: none !important;
                        }
                    `;
                    clonedDoc.head.appendChild(style);

                    const clonedSection = clonedDoc.querySelector('.rekap-section');
                    if (clonedSection) {
                        clonedSection.style.width = '1000px';
                        clonedSection.style.padding = '24px';
                        clonedSection.style.borderRadius = '16px';
                        clonedSection.style.margin = '0';
                        if (document.documentElement.getAttribute('data-theme') === 'dark') {
                            clonedSection.style.background = '#1e293b';
                            clonedSection.style.color = '#f8fafc';
                            clonedSection.style.border = '1px solid #334155';
                        } else {
                            clonedSection.style.background = '#ffffff';
                            clonedSection.style.color = '#0f172a';
                            clonedSection.style.border = '1px solid #e2e8f0';
                        }
                    }
                    const clonedBtn = clonedDoc.getElementById('btn-download-rekap');
                    if (clonedBtn) clonedBtn.style.display = 'none';
                }
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'rekapitulasi-logbook-yogurt.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                
                btn.innerHTML = originalText;
                if (window.lucide) window.lucide.createIcons();
            }).catch(err => {
                console.error(err);
                alert('Gagal mengunduh gambar');
                btn.innerHTML = originalText;
                if (window.lucide) window.lucide.createIcons();
            });
        });
    }
});
</script>
<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endpush
