@extends('layouts.app')
@section('title', 'Dashboard Guru')

@section('content')
<div class="dashboard teacher-dashboard">
  <header class="teacher-header">
    <div class="teacher-header-left">
      <div class="sidebar-logo">
        <span class="brand-badge">
          <i data-lucide="beaker" style="width:16px;height:16px;color:#fff;"></i>
        </span>
        <span class="logo-text">YogurtTrack</span>
      </div>
      <div class="header-divider"></div>
      <div>
        <h1>Dashboard Pemantauan</h1>
        <p>Pantau aktivitas seluruh siswa</p>
      </div>
    </div>
    <div class="teacher-header-right">
      <div class="teacher-user-info">
        <div class="teacher-avatar" style="display:flex;align-items:center;justify-content:center;background:var(--accent-dim);border:1px solid var(--border);">
          <i data-lucide="user-check" style="width:20px;height:20px;color:var(--accent);"></i>
        </div>
        <div class="teacher-user-details">
          <div class="teacher-name">{{ session('user')['name'] }}</div>
          <div class="teacher-role">Guru / Admin</div>
        </div>
      </div>
      <form method="POST" action="/logout" style="margin: 0;">@csrf
        <button class="btn btn-logout-teacher" style="display:inline-flex;align-items:center;gap:6px;">
          <i data-lucide="log-out" style="width:14px;height:14px;"></i><span class="btn-logout-text"> Keluar</span>
        </button>
      </form>
    </div>
  </header>

  <div class="stats-grid">
    <div class="stat-card s-total">
      <div class="stat-icon" style="display:flex;align-items:center;justify-content:center;background:var(--accent-dim);">
        <i data-lucide="users" style="width:24px;height:24px;color:var(--accent);"></i>
      </div>
      <div class="stat-value">{{ $stats['total'] }}</div>
      <div class="stat-label">Total Siswa</div>
    </div>
    <div class="stat-card s-done">
      <div class="stat-icon" style="display:flex;align-items:center;justify-content:center;background:var(--purple-dim);">
        <i data-lucide="folder-check" style="width:24px;height:24px;color:var(--purple);"></i>
      </div>
      <div class="stat-value">{{ $stats['selesai'] }}</div>
      <div class="stat-label">Logbook Selesai</div>
    </div>
    <div class="stat-card s-success">
      <div class="stat-icon" style="display:flex;align-items:center;justify-content:center;background:var(--success-dim);">
        <i data-lucide="rocket" style="width:24px;height:24px;color:var(--success);"></i>
      </div>
      <div class="stat-value">{{ $stats['berhasil'] }}</div>
      <div class="stat-label">Proyek Berhasil</div>
    </div>
    <div class="stat-card s-fail">
      <div class="stat-icon" style="display:flex;align-items:center;justify-content:center;background:var(--warning-dim);">
        <i data-lucide="alert-triangle" style="width:24px;height:24px;color:var(--warning);"></i>
      </div>
      <div class="stat-value">{{ $stats['kurang'] }}</div>
      <div class="stat-label">Kurang Berhasil</div>
    </div>
  </div>

  <div class="teacher-body">
    <div class="section-header">
      <h2>Daftar Siswa ({{ $stats['total'] }})</h2>
      <div class="filter-bar">
        <input type="text" id="s-search" placeholder="Cari nama siswa..." oninput="filterStudents()">
        <select id="s-filter" onchange="filterStudents()">
          <option value="">Semua Status</option>
          <option value="berhasil">Berhasil</option>
          <option value="kurang_berhasil">Kurang Berhasil</option>
          <option value="in_progress">Sedang Berlangsung</option>
        </select>
      </div>
    </div>

    @if($studentData->isEmpty())
      <div class="empty-state">
        <div class="empty-icon" style="display:flex;justify-content:center;margin-bottom:12px;">
          <i data-lucide="inbox" style="width:48px;height:48px;color:var(--text-muted);"></i>
        </div>
        <h3>Belum Ada Siswa Terdaftar</h3>
        <p>Siswa perlu mendaftar melalui halaman utama terlebih dahulu</p>
      </div>
    @else
      <div class="student-grid" id="student-grid">
        @php
          $stagesDef = \App\Services\EvaluatorService::stagesDef();
          $total = count($stagesDef);
          $stageColors = [];
          foreach ($stagesDef as $d) { $stageColors[] = $d['color'] ?? '#cccccc'; }
        @endphp
        @foreach($studentData as $i => $sd)
          @php
            $ev = $sd['evaluation'];
            $statusData = 'in_progress'; $cardCls = '';
            $badge = '<span class="badge badge-pending"><i data-lucide="list-todo" style="width:12px;height:12px;vertical-align:middle;margin-right:4px;"></i>'.$sd['done'].'/'.$total.' Tahap</span>';
            if ($ev) {
              if ($ev['result'] === 'berhasil') {
                $statusData='berhasil'; $cardCls='card-success';
                $badge='<span class="badge badge-success"><i data-lucide="check-circle" style="width:12px;height:12px;vertical-align:middle;margin-right:4px;"></i>Berhasil</span>';
              } else {
                $statusData='kurang_berhasil'; $cardCls='card-fail';
                $badge='<span class="badge badge-warning"><i data-lucide="alert-circle" style="width:12px;height:12px;vertical-align:middle;margin-right:4px;"></i>Kurang Berhasil</span>';
              }
            }
          @endphp
          <div class="student-card {{ $cardCls }}"
               data-status="{{ $statusData }}"
               data-name="{{ strtolower($sd['user']->name) }}"
               onclick="location.href='{{ route('teacher.student', $sd['user']->username) }}'"
               style="animation-delay:{{ $i*0.05 }}s" role="button" tabindex="0">
            <div class="student-card-header">
              <div class="student-avatar">{{ strtoupper(substr($sd['user']->name,0,1)) }}</div>
              <div class="student-info">
                <div class="student-name">{{ $sd['user']->name }}</div>
                <div class="student-group">
                  @if($sd['kelompok'])
                    <i data-lucide="flask-conical" style="width:12px;height:12px;vertical-align:middle;margin-right:4px;color:var(--accent);"></i>{{ $sd['kelompok'] }}
                  @else
                    {{ $sd['user']->group_name ?? 'Siswa' }}
                  @endif
                </div>
              </div>
              {!! $badge !!}
            </div>
            <div class="stage-progress-row">
              <div class="stage-dots">
                @for($s=1;$s<=$total;$s++)
                  <div class="stage-dot {{ isset($sd['stagesData'][$s])?'done':'' }}"
                       style="--dot-color:{{ $stageColors[$s-1] }}" title="Tahap {{ $s }}"></div>
                @endfor
              </div>
              <div class="progress-bar mini"><div class="progress-fill" style="width:{{ $sd['pct'] }}%"></div></div>
              <span class="stage-pct">{{ $sd['pct'] }}%</span>
            </div>
            <div class="student-card-footer">
              <span>Bergabung: {{ $sd['user']->created_at->format('d M Y') }}</span>
              <span class="view-link" style="display:inline-flex;align-items:center;gap:2px;">
                Lihat Detail <i data-lucide="arrow-right" style="width:12px;height:12px;"></i>
              </span>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</div>
@endsection
