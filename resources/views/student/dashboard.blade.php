@extends('layouts.app')
@section('title', 'YogurtTrack — Logbook Siswa')

@php
use App\Services\EvaluatorService;
$stagesDef = EvaluatorService::stagesDef();
$done = count($stagesData);
$pct  = round($done / 6 * 100);
$stageDef = $stagesDef[$activeStage];

$lucideIcons = [
    1 => 'clipboard-list',
    2 => 'flask-conical',
    3 => 'clock',
    4 => 'timer',
    5 => 'trending-up',
];
@endphp

@section('content')
<div class="dashboard student-dashboard">
  {{-- SIDEBAR --}}
  <aside class="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <span class="brand-badge">
          <i data-lucide="beaker" style="width: 16px; height: 16px; color: #fff;"></i>
        </span>
        <span class="logo-text">YogurtTrack</span>
      </div>
    </div>
    <div class="sidebar-user">
      <div class="user-avatar">{{ strtoupper(substr($user->name,0,1)) }}</div>
      <div class="user-details">
        <div class="user-name">{{ $user->name }}</div>
        <div class="user-role">
          <i data-lucide="users" style="width: 12px; height: 12px; vertical-align: middle; margin-right: 2px; color: var(--accent);"></i>
          {{ $stagesData[1]['data']['kelompok'] ?? ($user->group_name ?? 'Siswa') }}
        </div>
      </div>
    </div>
    <div class="nav-scroll-wrap">
      <nav class="sidebar-nav" id="sidebar-nav-scroll">
        @foreach($stagesDef as $num => $def)
          @php
            $isComplete = isset($stagesData[$num]);
            $isActive   = $activeStage === $num;
            // Stage 1 always unlocked; others sequential
            $isLocked   = ($num > 1 && !isset($stagesData[1])) || ($num > 2 && !isset($stagesData[$num-1]));
          @endphp
          <a href="{{ $isLocked ? '#' : route('student.stage', $num) }}"
             class="nav-item{{ $isActive?' active':'' }}{{ $isComplete?' complete':'' }}{{ $isLocked?' locked':'' }}"
             style="--stage-color:{{ $def['color'] }}">
            <span class="nav-icon">
              @if($isComplete)
                <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: var(--success);"></i>
              @else
                <i data-lucide="{{ $lucideIcons[$num] ?? 'circle' }}" style="width: 18px; height: 18px;"></i>
              @endif
            </span>
            <div class="nav-text">
              <span class="nav-label">Tahap {{ $num }}</span>
              <span class="nav-title">{{ $def['title'] }}</span>
            </div>
            @if($isLocked)<span class="nav-lock"><i data-lucide="lock" style="width: 12px; height: 12px;"></i></span>
            @elseif($isComplete && !empty($def['editable']))<span class="nav-check edit-badge"><i data-lucide="edit-3" style="width: 12px; height: 12px;"></i></span>
            @elseif($isComplete)<span class="nav-check"><i data-lucide="check" style="width: 12px; height: 12px;"></i></span>
            @endif
          </a>
        @endforeach
      </nav>
      <div class="nav-scroll-hint" id="nav-scroll-hint" aria-hidden="true">
        <span class="nav-scroll-hint-label">Geser <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></span>
      </div>
    </div>
    <div class="sidebar-progress">
      <div class="progress-label"><span>Progress</span><span class="progress-value">{{ $pct }}%</span></div>
      <div class="progress-bar"><div class="progress-fill" style="width:{{ $pct }}%"></div></div>
      <div class="progress-stages">{{ $done }} dari 6 tahap selesai</div>
    </div>
    <div class="sidebar-bottom">
      <form method="POST" action="/logout">@csrf
        <button type="submit" class="btn btn-ghost btn-logout"><i data-lucide="log-out" style="width: 16px; height: 16px; vertical-align: middle;"></i><span class="btn-logout-text"> Keluar</span></button>
      </form>
    </div>
  </aside>

  {{-- MAIN CONTENT --}}
  <main class="main-content">
    @if(session('success'))
      <div class="flash-success">{{ session('success') }}</div>
    @endif

    <div class="content-header">
      <div>
        <h1>
          <i data-lucide="{{ $lucideIcons[$activeStage] ?? 'circle' }}" style="width: 28px; height: 28px; display: inline-block; vertical-align: middle; margin-right: 8px; color: {{ $stageDef['color'] }}"></i>
          {{ $stageDef['title'] }}
        </h1>
        <p class="content-subtitle">
          Tahap {{ $activeStage }} dari 6
          @if(!empty($stageDef['editable'])) &nbsp;•&nbsp; <em style="color:var(--accent)">Dapat diedit kapan saja</em>@endif
        </p>
      </div>
      @if(isset($stagesData[$activeStage]))
        @if(!empty($stageDef['editable']))
          <span class="badge badge-info"><i data-lucide="edit-3" style="width: 12px; height: 12px; vertical-align: middle; margin-right: 4px;"></i> Editable</span>
        @else
          <span class="badge badge-success"><i data-lucide="check" style="width: 12px; height: 12px; vertical-align: middle; margin-right: 4px;"></i> Selesai</span>
        @endif
      @else
        <span class="badge badge-pending"><i data-lucide="clock" style="width: 12px; height: 12px; vertical-align: middle; margin-right: 4px;"></i> Belum Diisi</span>
      @endif
    </div>

    <div class="content-body">
      {{-- Stage 6 always shows auto-report if data exists --}}
      @if($activeStage === 6)
        @if(isset($stagesData[6]))
          @include('student.partials.lab-report', compact('stagesData','evaluation','rekap'))
        @else
          <div class="info-banner"><i data-lucide="file-text" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 6px; color: var(--accent);"></i> Lab report akan muncul otomatis setelah kamu menyelesaikan Pengamatan Final (Jam ke-12).</div>
        @endif
      @elseif(isset($stagesData[$activeStage]) && empty($stageDef['editable']))
        {{-- Read-only for non-editable completed stages --}}
        @include('student.partials.stage-view', [
          'stageNum'    => $activeStage,
          'data'        => $stagesData[$activeStage]['data'],
          'submittedAt' => $stagesData[$activeStage]['submitted_at'],
          'stagesData'  => $stagesData,
        ])
      @else
        {{-- Show form (stage 1 always shows form even if completed) --}}
        @include('student.partials.stage-form', [
          'stageNum'   => $activeStage,
          'stagesData' => $stagesData,
          'existing'   => $stagesData[$activeStage]['data'] ?? null,
        ])
      @endif
    </div>
  </main>
</div>
@endsection
