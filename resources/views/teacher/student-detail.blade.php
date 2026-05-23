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
      <a href="{{ route('teacher.dashboard') }}" class="btn btn-ghost">↩ Kembali</a>
      <div>
        <h1>🧾 Logbook — {{ $s1['kelompok'] ?? $student->name }}</h1>
        <p>
          {{ $student->name }} &nbsp;•&nbsp;
          {{ $s1['ekstrak'] ?? '' }} &nbsp;•&nbsp;
          {{ $done }}/{{ $total }} Tahap &nbsp;•&nbsp;
          Bergabung {{ $student->created_at->format('d M Y') }}
        </p>
      </div>
    </div>
    <form method="POST" action="/logout">@csrf<button class="btn btn-ghost">Keluar</button></form>
  </header>

  {{-- EVALUATION --}}
  @if($evaluation)
    <div class="eval-wrapper">
      @include('partials.evaluation-card', compact('evaluation'))
    </div>
  @else
    <div class="info-banner" style="margin:24px 32px">
      ℹ️ Evaluasi tersedia setelah siswa menyelesaikan Pengamatan Final (Jam ke-12). Saat ini {{ $done }}/{{ $total }} tahap selesai.
    </div>
  @endif

  {{-- REKAPITULASI TABLE --}}
  @if($rekap)
  <div class="rekap-section" style="margin:0 32px 24px">
    <div class="rekap-header">
      <h2>📈 Tabel Rekapitulasi Pengamatan</h2>
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
  <div class="stages-accordion" style="margin:0 32px 40px">
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:8px">Detail Isian Per Tahap</h2>
    @foreach($stagesDef as $num => $def)
      @php $hasData = isset($stagesData[$num]); @endphp
      <div class="stage-card {{ $hasData ? 'filled' : 'empty' }}">
        <div class="stage-card-header" style="--stage-color:{{ $def['color'] }}"
             onclick="toggleCard({{ $num }})" role="button">
          <span class="stage-card-icon">{{ $hasData ? '✔️' : $def['icon'] }}</span>
          <div class="stage-card-meta">
            <div class="stage-card-title">Tahap {{ $num }}: {{ $def['title'] }}</div>
            <div class="stage-card-sub">
              @if($hasData) Diisi: {{ \Carbon\Carbon::parse($stagesData[$num]['submitted_at'])->format('d M Y, H:i') }}
              @else Belum diisi @endif
            </div>
          </div>
          <span class="stage-toggle" id="toggle-{{ $num }}">▼</span>
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
<script>
function toggleCard(n) {
  const body = document.getElementById('body-'+n);
  const toggle = document.getElementById('toggle-'+n);
  if (!body) return;
  const open = body.style.display !== 'none';
  body.style.display = open ? 'none' : 'block';
  if (toggle) toggle.style.transform = open ? '' : 'rotate(180deg)';
}
</script>
@endpush
