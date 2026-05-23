@php use App\Services\EvaluatorService; @endphp

@if($stageNum === 1)
  <div class="view-section">
    <div class="info-grid">
      <div class="info-item"><span class="info-label">🧑‍🤝‍🧑 Nama Kelompok</span><span class="info-value">{{ $data['kelompok'] }}</span></div>
      <div class="info-item"><span class="info-label">🧪 Jenis Ekstrak</span><span class="info-value">{{ $data['ekstrak'] }}</span></div>
      <div class="info-item full-width"><span class="info-label">👤 Anggota Kelompok</span><span class="info-value" style="white-space:pre-line">{{ $data['anggota'] }}</span></div>
      <div class="info-item full-width"><span class="info-label">⚗️ Komposisi Bahan</span><span class="info-value" style="white-space:pre-line">{{ $data['komposisi'] }}</span></div>
      <div class="info-item"><span class="info-label">⏱️ Durasi Fermentasi</span><span class="info-value">{{ $data['durasi'] ?? '12 jam' }}</span></div>
    </div>
    @if(!empty($data['alasan_inovasi']))
      <div class="view-note"><strong>💡 Alasan &amp; Inovasi:</strong><br>{{ $data['alasan_inovasi'] }}</div>
    @endif
    @if(!empty($data['foto_bahan']))
      <div class="view-photo-wrap">
        <h4>🖼️ Foto Bahan</h4>
        <img src="{{ Storage::url($data['foto_bahan']) }}" class="view-photo" alt="Foto bahan kelompok">
      </div>
    @else
      <div class="view-photo-missing">
        <h4>🖼️ Foto Bahan</h4>
        <div class="missing-box">— Foto tidak tersedia. Mohon minta siswa mengunggah ulang atau periksa entri data.</div>
      </div>
    @endif
  </div>

@elseif($stageNum === 2)
  <div class="view-section">
    <h3>🔬 Proses Pembuatan</h3>
    <div class="view-text" style="white-space:pre-line">{{ $data['proses'] }}</div>

    @if(!empty($data['prediksi_jam']))
      <div class="prediction-result">
        <span class="pred-icon">🤔</span>
        <div>
          <div class="pred-label">Prediksi Pengentalan</div>
          <div class="pred-value">Jam ke-{{ $data['prediksi_jam'] }}</div>
          @if(!empty($data['alasan_prediksi']))<div class="pred-alasan">{{ $data['alasan_prediksi'] }}</div>@endif
        </div>
      </div>
    @endif

    <h3 class="section-jam" style="margin-top:20px">⏱️ Kondisi Awal — Jam ke-0</h3>
    @php $j0 = $data['jam0'] ?? []; @endphp
    <div class="organo-grid">
      <div class="organo-item"><span class="organo-label">🎨 Warna</span><span class="organo-value">{{ $j0['warna'] ?? '-' }}</span></div>
      <div class="organo-item"><span class="organo-label">👃 Aroma</span><span class="organo-value">{{ $j0['aroma'] ?? '-' }}</span></div>
      <div class="organo-item"><span class="organo-label">🥄 Tekstur</span><span class="organo-value">{{ $j0['tekstur'] ?? 'Cair (awal)' }}</span></div>
      <div class="organo-item"><span class="organo-label">👅 Rasa</span><span class="organo-value">{{ $j0['rasa'] ?? '-' }}</span></div>
      @if(isset($j0['ph']))<div class="organo-item"><span class="organo-label">🧪 pH Awal</span><span class="organo-value ph-badge">{{ $j0['ph'] }}</span></div>@endif
    </div>
    @if(!empty($j0['catatan']))<div class="view-note">{{ $j0['catatan'] }}</div>@endif
    @if(!empty($j0['foto']))
      <img src="{{ Storage::url($j0['foto']) }}" class="view-photo" alt="Foto jam ke-0">
    @else
      <div class="view-photo-missing"><div class="missing-box">Foto jam ke-0 tidak tersedia.</div></div>
    @endif
  </div>

@elseif(in_array($stageNum, [3, 4]))
  @php
    $labels = [3 => 'Jam ke-8', 4 => 'Jam ke-12 (Final)'];
    $label = $labels[$stageNum] ?? 'Pengamatan';
  @endphp
  <div class="view-section">
    <div class="info-banner">🕐 Data Pengamatan {{ $label }}</div>
    <div class="organo-grid-full">
      <div class="organo-row {{ $data['warna_normal'] ? 'row-normal' : 'row-abnormal' }}">
        <span class="organo-param">🎨 Warna</span>
        <span class="organo-desc-val">{{ $data['warna'] }}</span>
        <span class="organo-status {{ EvaluatorService::normalClass($data['warna_normal']) }}">{{ EvaluatorService::normalLabel($data['warna_normal']) }}</span>
      </div>
      <div class="organo-row {{ $data['aroma_normal'] ? 'row-normal' : 'row-abnormal' }}">
        <span class="organo-param">👃 Aroma</span>
        <span class="organo-desc-val">{{ $data['aroma'] }}</span>
        <span class="organo-status {{ EvaluatorService::normalClass($data['aroma_normal']) }}">{{ EvaluatorService::normalLabel($data['aroma_normal']) }}</span>
      </div>
      <div class="organo-row {{ $data['tekstur_normal'] ? 'row-normal' : 'row-abnormal' }}">
        <span class="organo-param">🥄 Tekstur</span>
        <span class="organo-desc-val">{{ $data['tekstur'] }}</span>
        <span class="organo-status {{ EvaluatorService::normalClass($data['tekstur_normal']) }}">{{ EvaluatorService::normalLabel($data['tekstur_normal']) }}</span>
      </div>
      <div class="organo-row {{ $data['rasa_normal'] ? 'row-normal' : 'row-abnormal' }}">
        <span class="organo-param">👅 Rasa</span>
        <span class="organo-desc-val">{{ $data['rasa'] }}</span>
        <span class="organo-status {{ EvaluatorService::normalClass($data['rasa_normal']) }}">{{ EvaluatorService::normalLabel($data['rasa_normal']) }}</span>
      </div>
      @if($stageNum === 4 && isset($data['ph_akhir']))
        <div class="organo-row">
          <span class="organo-param">🧪 pH Akhir</span>
          <span class="organo-desc-val"><span class="ph-badge">{{ $data['ph_akhir'] }}</span></span>
          <span class="organo-status {{ ($data['ph_akhir'] >= 3.8 && $data['ph_akhir'] <= 4.5) ? 'status-normal' : 'status-abnormal' }}">
            {{ ($data['ph_akhir'] >= 3.8 && $data['ph_akhir'] <= 4.5) ? '✔️ Normal (3,8–4,5)' : '✖️ Di luar rentang' }}
          </span>
        </div>
      @endif
    </div>
    @if(!empty($data['catatan']))<div class="view-note">{{ $data['catatan'] }}</div>@endif
    @if(!empty($data['foto']))
      <img src="{{ Storage::url($data['foto']) }}" class="view-photo" alt="Foto {{ $label }}">
    @else
      <div class="view-photo-missing"><div class="missing-box">Foto pengamatan {{ $label }} tidak tersedia.</div></div>
    @endif
    @if($stageNum === 4 && !empty($data['kesimpulan_awal']))
      <div class="view-note"><strong>📝 Kesimpulan Awal:</strong><br>{{ $data['kesimpulan_awal'] }}</div>
    @endif
  </div>
@endif

<div class="stage-completed-note">✔️ Disubmit pada {{ \Carbon\Carbon::parse($submittedAt)->format('d M Y, H:i') }}</div>
