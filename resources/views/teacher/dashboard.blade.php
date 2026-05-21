@extends('layouts.app')
@section('title', 'Dashboard Guru')

@section('content')
<div class="dashboard teacher-dashboard">
  <header class="teacher-header">
    <div class="teacher-header-left">
      <div class="sidebar-logo">🥛 YogurtLog</div>
      <div>
        <h1>Dashboard Pemantauan</h1>
        <p>Pantau aktivitas seluruh siswa secara real-time</p>
      </div>
    </div>
    <div class="teacher-user-info">
      <div class="teacher-avatar">👩‍🏫</div>
      <div>
        <div class="teacher-name">{{ session('user')['name'] }}</div>
        <div class="teacher-role">Guru / Admin</div>
      </div>
      <form method="POST" action="/logout">@csrf
        <button class="btn btn-ghost">Keluar</button>
      </form>
    </div>
  </header>

  <div class="stats-grid">
    <div class="stat-card s-total"><div class="stat-icon">👥</div><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Total Siswa</div></div>
    <div class="stat-card s-done"><div class="stat-icon">✅</div><div class="stat-value">{{ $stats['selesai'] }}</div><div class="stat-label">Logbook Selesai</div></div>
    <div class="stat-card s-success"><div class="stat-icon">🏆</div><div class="stat-value">{{ $stats['berhasil'] }}</div><div class="stat-label">Proyek Berhasil</div></div>
    <div class="stat-card s-fail"><div class="stat-icon">⚠️</div><div class="stat-value">{{ $stats['kurang'] }}</div><div class="stat-label">Kurang Berhasil</div></div>
  </div>

  <div class="teacher-body">
    <div class="section-header">
      <h2>Daftar Siswa ({{ $stats['total'] }})</h2>
      <div class="filter-bar">
        <input type="text" id="s-search" placeholder="🔍 Cari nama siswa..." oninput="filterStudents()">
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
        <div class="empty-icon">👥</div>
        <h3>Belum Ada Siswa Terdaftar</h3>
        <p>Siswa perlu mendaftar melalui halaman utama terlebih dahulu</p>
      </div>
    @else
      <div class="student-grid" id="student-grid">
        @php $stageColors = ['#7C6FFF','#00A8FF','#FF9500','#FF6B6B','#00C896','#F5C842']; @endphp
        @foreach($studentData as $i => $sd)
          @php
            $ev = $sd['evaluation'];
            $statusData = 'in_progress'; $cardCls = '';
            $badge = '<span class="badge badge-pending">'.$sd['done'].'/6 Tahap</span>';
            if ($ev) {
              if ($ev['result'] === 'berhasil') {
                $statusData='berhasil'; $cardCls='card-success';
                $badge='<span class="badge badge-success">🏆 Berhasil</span>';
              } else {
                $statusData='kurang_berhasil'; $cardCls='card-fail';
                $badge='<span class="badge badge-warning">⚠️ Kurang Berhasil</span>';
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
                    🌿 {{ $sd['kelompok'] }}
                  @else
                    {{ $sd['user']->group_name ?? 'Siswa' }}
                  @endif
                </div>
              </div>
              {!! $badge !!}
            </div>
            <div class="stage-progress-row">
              <div class="stage-dots">
                @for($s=1;$s<=6;$s++)
                  <div class="stage-dot {{ isset($sd['stagesData'][$s])?'done':'' }}"
                       style="--dot-color:{{ $stageColors[$s-1] }}" title="Tahap {{ $s }}"></div>
                @endfor
              </div>
              <div class="progress-bar mini"><div class="progress-fill" style="width:{{ $sd['pct'] }}%"></div></div>
              <span class="stage-pct">{{ $sd['pct'] }}%</span>
            </div>
            <div class="student-card-footer">
              <span>Bergabung: {{ $sd['user']->created_at->format('d M Y') }}</span>
              <span class="view-link">Lihat Detail →</span>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</div>
@endsection
