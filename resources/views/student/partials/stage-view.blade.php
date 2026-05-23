@php use App\Services\EvaluatorService; @endphp

@if($stageNum === 1)
  <div class="view-section">
    <div class="info-grid">
      <div class="info-item"><span class="info-label"><i data-lucide="users" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Nama Kelompok</span><span class="info-value">{{ $data['kelompok'] }}</span></div>
      <div class="info-item"><span class="info-label"><i data-lucide="flask-conical" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Jenis Ekstrak</span><span class="info-value">{{ $data['ekstrak'] }}</span></div>
      <div class="info-item full-width"><span class="info-label"><i data-lucide="user" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Anggota Kelompok</span><span class="info-value" style="white-space:pre-line">{{ $data['anggota'] }}</span></div>
      <div class="info-item full-width"><span class="info-label"><i data-lucide="clipboard-list" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Komposisi Bahan</span><span class="info-value" style="white-space:pre-line">{{ $data['komposisi'] }}</span></div>
      <div class="info-item"><span class="info-label"><i data-lucide="timer" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Durasi Fermentasi</span><span class="info-value">{{ $data['durasi'] ?? '12 jam' }}</span></div>
    </div>
    @if(!empty($data['alasan_inovasi']))
      <div class="view-note"><i data-lucide="lightbulb" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;color:var(--gold);"></i> <strong>Alasan &amp; Inovasi:</strong><br>{{ $data['alasan_inovasi'] }}</div>
    @endif
    @if(!empty($data['foto_bahan']))
      <div class="view-photo-wrap">
        <h4><i data-lucide="image" style="width:16px;height:16px;vertical-align:middle;margin-right:4px;"></i> Foto Bahan</h4>
        <img
          src="{{ url('/storage/' . ltrim($data['foto_bahan'], '/')) }}"
          class="view-photo clickable"
          alt="Foto bahan kelompok"
          onclick='YogurtLightbox.open(@json(url('/storage/' . ltrim($data['foto_bahan'], '/'))), @json("Foto bahan"))'
        >
        <div class="photo-caption"><small>Click to view full</small></div>
      </div>
    @else
      <div class="view-photo-missing">
        <h4><i data-lucide="image" style="width:16px;height:16px;vertical-align:middle;margin-right:4px;"></i> Foto Bahan</h4>
        <div class="missing-box">— Foto tidak tersedia. Mohon minta siswa mengunggah ulang atau periksa entri data.</div>
      </div>
    @endif
  </div>

@elseif($stageNum === 2)
  <div class="view-section">
    <h3><i data-lucide="beaker" style="width:18px;height:18px;vertical-align:middle;margin-right:4px;"></i> Proses Pembuatan</h3>
    <div class="view-text" style="white-space:pre-line">{{ $data['proses'] }}</div>

    @if(!empty($data['prediksi_jam']))
      <div class="prediction-result">
        <span class="pred-icon"><i data-lucide="help-circle" style="width:20px;height:20px;color:var(--accent);"></i></span>
        <div>
          <div class="pred-label">Prediksi Pengentalan</div>
          <div class="pred-value">Jam ke-{{ $data['prediksi_jam'] }}</div>
          @if(!empty($data['alasan_prediksi']))<div class="pred-alasan">{{ $data['alasan_prediksi'] }}</div>@endif
        </div>
      </div>
    @endif

    <h3 class="section-jam" style="margin-top:20px"><i data-lucide="timer" style="width:18px;height:18px;vertical-align:middle;margin-right:4px;"></i> Kondisi Awal — Jam ke-0</h3>
    @php $j0 = $data['jam0'] ?? []; @endphp
    <div class="organo-grid">
      <div class="organo-item"><span class="organo-label"><i data-lucide="palette" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Warna</span><span class="organo-value">{{ $j0['warna'] ?? '-' }}</span></div>
      <div class="organo-item"><span class="organo-label"><i data-lucide="wind" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Aroma</span><span class="organo-value">{{ $j0['aroma'] ?? '-' }}</span></div>
      <div class="organo-item"><span class="organo-label"><i data-lucide="activity" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Tekstur</span><span class="organo-value">{{ $j0['tekstur'] ?? 'Cair (awal)' }}</span></div>
      <div class="organo-item"><span class="organo-label"><i data-lucide="smile" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Rasa</span><span class="organo-value">{{ $j0['rasa'] ?? '-' }}</span></div>
      @if(isset($j0['ph']))<div class="organo-item"><span class="organo-label"><i data-lucide="flask-conical" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> pH Awal</span><span class="organo-value ph-badge">{{ $j0['ph'] }}</span></div>@endif
    </div>
    @if(!empty($j0['catatan']))<div class="view-note">{{ $j0['catatan'] }}</div>@endif
    <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-top: 16px;">
      <div style="flex: 1; min-width: 250px;">
        <h4 style="font-size: 0.85rem; margin-bottom: 8px; color: var(--text-muted);"><i data-lucide="image" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Foto Produk Jam ke-0</h4>
        @if(!empty($j0['foto']))
          <img
            src="{{ url('/storage/' . ltrim($j0['foto'], '/')) }}"
            class="view-photo clickable"
            alt="Foto jam ke-0"
            onclick='YogurtLightbox.open(@json(url('/storage/' . ltrim($j0['foto'], '/'))), @json("Foto Jam ke-0"))'
          >
          <div class="photo-caption"><small>Click to view full</small></div>
        @else
          <div class="view-photo-missing"><div class="missing-box">Foto produk tidak tersedia.</div></div>
        @endif
      </div>
      <div style="flex: 1; min-width: 250px;">
        <h4 style="font-size: 0.85rem; margin-bottom: 8px; color: var(--text-muted);"><i data-lucide="camera" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Dokumentasi Kertas Lakmus</h4>
        @if(!empty($j0['ph_foto']))
          <img
            src="{{ url('/storage/' . ltrim($j0['ph_foto'], '/')) }}"
            class="view-photo clickable"
            alt="Foto kertas lakmus jam ke-0"
            onclick='YogurtLightbox.open(@json(url('/storage/' . ltrim($j0['ph_foto'], '/'))), @json("Foto Kertas Lakmus Jam ke-0"))'
          >
          <div class="photo-caption"><small>Click to view full</small></div>
        @else
          <div class="view-photo-missing"><div class="missing-box">Foto kertas lakmus tidak tersedia.</div></div>
        @endif
      </div>
    </div>
  </div>

@elseif(in_array($stageNum, [3, 4]))
  @php
    $labels = [3 => 'Jam ke-8', 4 => 'Jam ke-12 (Final)'];
    $label = $labels[$stageNum] ?? 'Pengamatan';
    // Compute rule-based flags first (prefer $rekap if available)
    $computed = ['warna_normal' => null, 'aroma_normal' => null, 'rasa_normal' => null, 'tekstur_normal' => null];
    if (isset($rekap) && is_array($rekap)) {
        foreach ($rekap as $r) {
            if ($stageNum === 3 && ($r['waktu'] ?? null) === 8) {
                $computed['warna_normal']   = $r['warna_normal'] ?? $computed['warna_normal'];
                $computed['aroma_normal']   = $r['aroma_normal'] ?? $computed['aroma_normal'];
                $computed['rasa_normal']    = $r['rasa_normal'] ?? $computed['rasa_normal'];
                $computed['tekstur_normal'] = $r['tekstur_normal'] ?? $computed['tekstur_normal'];
            }
            if ($stageNum === 4 && ($r['waktu'] ?? null) === 12) {
                $computed['warna_normal']   = $r['warna_normal'] ?? $computed['warna_normal'];
                $computed['aroma_normal']   = $r['aroma_normal'] ?? $computed['aroma_normal'];
                $computed['rasa_normal']    = $r['rasa_normal'] ?? $computed['rasa_normal'];
                $computed['tekstur_normal'] = $r['tekstur_normal'] ?? $computed['tekstur_normal'];
            }
        }
    }
  @endphp
  <div class="view-section">
    <div class="info-banner"><i data-lucide="clock" style="width:16px;height:16px;vertical-align:middle;margin-right:4px;"></i> Data Pengamatan {{ $label }}</div>
    <div class="organo-grid-full">
      @php $warnaFlag = $computed['warna_normal'] ?? ($data['warna_normal'] ?? null); @endphp
      <div class="organo-row {{ $warnaFlag ? 'row-normal' : 'row-abnormal' }}">
        <span class="organo-param"><i data-lucide="palette" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Warna</span>
        <span class="organo-desc-val">
          <strong>Deskripsi:</strong> {{ $data['warna'] }}
          @if(!empty($data['warna_opsi']))
            <br><small style="color:var(--text-muted);display:inline-block;margin-top:4px;"><strong>Pilihan:</strong> {{ implode(', ', (array)$data['warna_opsi']) }}</small>
          @endif
        </span>
        <span class="organo-status {{ EvaluatorService::normalClass($warnaFlag) }}">{{ EvaluatorService::normalLabel($warnaFlag) }}</span>
      </div>

      @php $aromaFlag = $computed['aroma_normal'] ?? ($data['aroma_normal'] ?? null); @endphp
      <div class="organo-row {{ $aromaFlag ? 'row-normal' : 'row-abnormal' }}">
        <span class="organo-param"><i data-lucide="wind" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Aroma</span>
        <span class="organo-desc-val">
          <strong>Deskripsi:</strong> {{ $data['aroma'] }}
          @if(!empty($data['aroma_opsi']))
            <br><small style="color:var(--text-muted);display:inline-block;margin-top:4px;"><strong>Pilihan:</strong> {{ implode(', ', (array)$data['aroma_opsi']) }}</small>
          @endif
        </span>
        <span class="organo-status {{ EvaluatorService::normalClass($aromaFlag) }}">{{ EvaluatorService::normalLabel($aromaFlag) }}</span>
      </div>

      @php $teksturFlag = $computed['tekstur_normal'] ?? ($data['tekstur_normal'] ?? null); @endphp
      <div class="organo-row {{ $teksturFlag ? 'row-normal' : 'row-abnormal' }}">
        <span class="organo-param"><i data-lucide="activity" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Tekstur</span>
        <span class="organo-desc-val">{{ $data['tekstur'] }}</span>
        <span class="organo-status {{ EvaluatorService::normalClass($teksturFlag) }}">{{ EvaluatorService::normalLabel($teksturFlag) }}</span>
      </div>

      @php $rasaFlag = $computed['rasa_normal'] ?? ($data['rasa_normal'] ?? null); @endphp
      <div class="organo-row {{ $rasaFlag ? 'row-normal' : 'row-abnormal' }}">
        <span class="organo-param"><i data-lucide="smile" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Rasa</span>
        <span class="organo-desc-val">
          <strong>Deskripsi:</strong> {{ $data['rasa'] }}
          @if(!empty($data['rasa_opsi']))
            <br><small style="color:var(--text-muted);display:inline-block;margin-top:4px;"><strong>Pilihan:</strong> {{ implode(', ', (array)$data['rasa_opsi']) }}</small>
          @endif
        </span>
        <span class="organo-status {{ EvaluatorService::normalClass($rasaFlag) }}">{{ EvaluatorService::normalLabel($rasaFlag) }}</span>
      </div>

      @if($stageNum === 4 && isset($data['ph_akhir']))
        <div class="organo-row">
          <span class="organo-param"><i data-lucide="flask-conical" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> pH Akhir</span>
          <span class="organo-desc-val"><span class="ph-badge">{{ $data['ph_akhir'] }}</span></span>
        </div>
      @endif
    </div>
    @if(!empty($data['catatan']))<div class="view-note">{{ $data['catatan'] }}</div>@endif
    @if($stageNum === 4)
      <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-top: 16px;">
        <div style="flex: 1; min-width: 250px;">
          <h4 style="font-size: 0.85rem; margin-bottom: 8px; color: var(--text-muted);"><i data-lucide="image" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Foto Produk Jam ke-12</h4>
          @if(!empty($data['foto']))
            <img
              src="{{ url('/storage/' . ltrim($data['foto'], '/')) }}"
              class="view-photo clickable"
              alt="Foto jam ke-12"
              onclick='YogurtLightbox.open(@json(url('/storage/' . ltrim($data['foto'], '/'))), @json("Foto Jam ke-12"))'
            >
            <div class="photo-caption"><small>Click to view full</small></div>
          @else
            <div class="view-photo-missing"><div class="missing-box">Foto produk tidak tersedia.</div></div>
          @endif
        </div>
        <div style="flex: 1; min-width: 250px;">
          <h4 style="font-size: 0.85rem; margin-bottom: 8px; color: var(--text-muted);"><i data-lucide="camera" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Dokumentasi Kertas Lakmus</h4>
          @if(!empty($data['ph_akhir_foto']))
            <img
              src="{{ url('/storage/' . ltrim($data['ph_akhir_foto'], '/')) }}"
              class="view-photo clickable"
              alt="Foto kertas lakmus jam ke-12"
              onclick='YogurtLightbox.open(@json(url('/storage/' . ltrim($data['ph_akhir_foto'], '/'))), @json("Foto Kertas Lakmus Jam ke-12"))'
            >
            <div class="photo-caption"><small>Click to view full</small></div>
          @else
            <div class="view-photo-missing"><div class="missing-box">Foto kertas lakmus tidak tersedia.</div></div>
          @endif
        </div>
      </div>
    @else
      @if(!empty($data['foto']))
        <img
          src="{{ url('/storage/' . ltrim($data['foto'], '/')) }}"
          class="view-photo clickable"
          alt="Foto {{ $label }}"
          onclick='YogurtLightbox.open(@json(url('/storage/' . ltrim($data['foto'], '/'))), @json("Foto " . $label))'
        >
        <div class="photo-caption"><small>Click to view full</small></div>
      @else
        <div class="view-photo-missing"><div class="missing-box">Foto pengamatan {{ $label }} tidak tersedia.</div></div>
      @endif
    @endif
    @if($stageNum === 4 && !empty($data['kesimpulan_awal']))
      <div class="view-note"><i data-lucide="file-edit" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;color:var(--accent);"></i> <strong>Kesimpulan Awal:</strong><br>{{ $data['kesimpulan_awal'] }}</div>
    @endif
  </div>
@endif

@once
  <div id="image-modal-overlay" class="image-modal-overlay" role="dialog" aria-modal="true" aria-label="Image preview">
    <div id="image-modal-box" class="image-modal-box" role="document">
      <div class="image-modal-toolbar">
        <div class="image-modal-zoom-controls">
          <button id="image-zoom-out" class="image-modal-tool-btn" type="button" aria-label="Zoom out"><i data-lucide="zoom-out" style="width:16px;height:16px;"></i></button>
          <button id="image-zoom-reset" class="image-modal-tool-btn" type="button" aria-label="Reset zoom">100%</button>
          <button id="image-zoom-in" class="image-modal-tool-btn" type="button" aria-label="Zoom in"><i data-lucide="zoom-in" style="width:16px;height:16px;"></i></button>
        </div>
        <button id="image-modal-close" class="image-modal-close" type="button" aria-label="Close preview"><i data-lucide="x" style="width:18px;height:18px;"></i></button>
      </div>
      <div class="image-modal-content">
        <img id="image-modal-img" src="" alt="" class="image-modal-img">
      </div>
      <div id="image-modal-caption" class="image-modal-caption"></div>
    </div>
  </div>

  @push('scripts')
  <script>
  window.YogurtLightbox = window.YogurtLightbox || (function () {
    const ids = {
      overlay: 'image-modal-overlay',
      box: 'image-modal-box',
      close: 'image-modal-close',
      img: 'image-modal-img',
      caption: 'image-modal-caption',
      zoomIn: 'image-zoom-in',
      zoomOut: 'image-zoom-out',
      zoomReset: 'image-zoom-reset'
    };

    const zoom = {
      value: 1,
      min: 0.5,
      max: 4,
      step: 0.2
    };

    function byId(id) { return document.getElementById(id); }

    function clampScale(value) {
      return Math.min(zoom.max, Math.max(zoom.min, value));
    }

    function applyZoom(value) {
      const img = byId(ids.img);
      const resetBtn = byId(ids.zoomReset);
      if (!img) return;

      zoom.value = clampScale(value);
      img.style.transform = 'scale(' + zoom.value.toFixed(2) + ')';

      if (resetBtn) {
        resetBtn.textContent = Math.round(zoom.value * 100) + '%';
      }
    }

    function ensureOverlayMounted() {
      const overlay = byId(ids.overlay);
      if (!overlay) return null;
      // Stage partial can be rendered inside collapsed accordion bodies.
      // Move modal to <body> so it is never hidden by parent display:none.
      if (overlay.parentElement !== document.body) {
        document.body.appendChild(overlay);
      }
      return overlay;
    }

    function open(src, caption) {
      const overlay = ensureOverlayMounted();
      const box = byId(ids.box);
      const img = byId(ids.img);
      const cap = byId(ids.caption);
      if (!overlay || !box || !img || !cap) return;

      img.src = src || '';
      img.alt = caption || 'Preview image';
      cap.innerHTML = caption ? `<i data-lucide="info" style="width:14px;height:14px;vertical-align:middle;margin-right:6px;color:var(--accent);"></i>` + caption : '';
      applyZoom(1);

      overlay.classList.add('is-open');
      box.classList.remove('is-animating-out');
      box.classList.add('is-animating-in');
      document.body.classList.add('modal-open');

      if (window.lucide) {
        window.lucide.createIcons();
      }
    }

    function close() {
      const overlay = ensureOverlayMounted();
      const box = byId(ids.box);
      const img = byId(ids.img);
      if (!overlay || !box || !img) return;

      box.classList.remove('is-animating-in');
      box.classList.add('is-animating-out');
      window.setTimeout(function () {
        overlay.classList.remove('is-open');
        box.classList.remove('is-animating-out');
        img.src = '';
        applyZoom(1);
        document.body.classList.remove('modal-open');
      }, 170);
    }

    function init() {
      ensureOverlayMounted();

      document.addEventListener('click', function (e) {
        if (e.target && (e.target.id === ids.overlay || e.target.closest('#' + ids.close))) {
          close();
        }
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') close();
      });

      const zoomInBtn = byId(ids.zoomIn);
      const zoomOutBtn = byId(ids.zoomOut);
      const zoomResetBtn = byId(ids.zoomReset);
      const modalBox = byId(ids.box);

      if (zoomInBtn) zoomInBtn.addEventListener('click', function () { applyZoom(zoom.value + zoom.step); });
      if (zoomOutBtn) zoomOutBtn.addEventListener('click', function () { applyZoom(zoom.value - zoom.step); });
      if (zoomResetBtn) zoomResetBtn.addEventListener('click', function () { applyZoom(1); });

      if (modalBox) {
        modalBox.addEventListener('wheel', function (e) {
          const overlay = byId(ids.overlay);
          if (!overlay || !overlay.classList.contains('is-open')) return;
          e.preventDefault();
          const delta = e.deltaY < 0 ? zoom.step : -zoom.step;
          applyZoom(zoom.value + delta);
        }, { passive: false });
      }
    }

    init();
    return { open: open, close: close };
  })();
  </script>
  @endpush
@endonce

  <div class="stage-completed-note"><i data-lucide="check" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;color:var(--success);"></i> Disubmit pada {{ \Carbon\Carbon::parse($submittedAt)->timezone(config('app.timezone'))->format('d M Y, H:i') }}</div>

  @if(session('user')['role'] === 'guru' && isset($student))
    <div style="margin-top: 24px; padding-top: 20px; border-top: 1px dashed var(--border); display: flex; justify-content: flex-end;">
      <form method="POST" action="{{ route('teacher.student.stage.reset', [$student->username, $stageNum], false) }}" onsubmit="return confirm('Apakah Anda yakin ingin me-reset tahap ini untuk siswa {{ $student->name }}? Data pada tahapan ini akan dihapus permanen, dan siswa harus mengisi ulang.')">
        @csrf
        <button type="submit" class="btn btn-outline btn-sm" style="color: var(--error); border-color: rgba(239, 68, 68, 0.2); background: rgba(239, 68, 68, 0.05); display: inline-flex; align-items: center; gap: 6px;">
          <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i> Reset &amp; Minta Siswa Isi Ulang
        </button>
      </form>
    </div>
  @endif
