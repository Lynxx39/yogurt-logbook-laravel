@extends('layouts.app')
@section('title', 'YogurtTrack — Logbook Siswa')

@php
use App\Services\EvaluatorService;
$stagesDef = EvaluatorService::stagesDef();
$done = count($stagesData);
$pct  = round($done / 6 * 100);
$stageDef = $stagesDef[$activeStage];
@endphp

@section('content')
<div class="dashboard student-dashboard">
  {{-- SIDEBAR --}}
  <aside class="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo"><span class="sidebar-logo-icon">🥣</span> YogurtTrack</div>
    </div>
    <div class="sidebar-user">
      <div class="user-avatar">{{ strtoupper(substr($user->name,0,1)) }}</div>
      <div>
        <div class="user-name">{{ $user->name }}</div>
        <div class="user-role">{{ $stagesData[1]['data']['kelompok'] ?? ($user->group_name ?? 'Siswa') }}</div>
      </div>
    </div>
    <nav class="sidebar-nav">
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
          <span class="nav-icon">{{ $isComplete ? '✔️' : $def['icon'] }}</span>
          <div class="nav-text">
            <span class="nav-label">Tahap {{ $num }}</span>
            <span class="nav-title">{{ $def['title'] }}</span>
          </div>
          @if($isLocked)<span class="nav-lock">🔒</span>
          @elseif($isComplete && !empty($def['editable']))<span class="nav-check edit-badge">✍️</span>
          @elseif($isComplete)<span class="nav-check">✓</span>
          @endif
        </a>
      @endforeach
    </nav>
    <div class="sidebar-progress">
      <div class="progress-label"><span>Progress</span><span class="progress-value">{{ $pct }}%</span></div>
      <div class="progress-bar"><div class="progress-fill" style="width:{{ $pct }}%"></div></div>
      <div class="progress-stages">{{ $done }} dari 6 tahap selesai</div>
    </div>
    <div class="sidebar-bottom">
      <form method="POST" action="/logout">@csrf
        <button type="submit" class="btn btn-ghost btn-logout">↩ Keluar</button>
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
        <h1>{{ $stageDef['icon'] }} {{ $stageDef['title'] }}</h1>
        <p class="content-subtitle">
          Tahap {{ $activeStage }} dari 6
          @if(!empty($stageDef['editable'])) &nbsp;•&nbsp; <em style="color:var(--accent)">Dapat diedit kapan saja</em>@endif
        </p>
      </div>
      @if(isset($stagesData[$activeStage]))
        @if(!empty($stageDef['editable']))
          <span class="badge badge-info">✍️ Editable</span>
        @else
          <span class="badge badge-success">✔️ Selesai</span>
        @endif
      @else
        <span class="badge badge-pending">⌛ Belum Diisi</span>
      @endif
    </div>

    <div class="content-body">
      {{-- Stage 6 always shows auto-report if data exists --}}
      @if($activeStage === 6)
        @if(isset($stagesData[6]))
          @include('student.partials.lab-report', compact('stagesData','evaluation','rekap'))
        @else
          <div class="info-banner">📑 Lab report akan muncul otomatis setelah kamu menyelesaikan Pengamatan Final (Jam ke-12).</div>
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
