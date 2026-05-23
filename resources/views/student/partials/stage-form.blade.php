{{-- Stage Form Partial — all 6 stages --}}
{{-- Variables: $stageNum, $stagesData, $existing (nullable pre-fill data) --}}

@if($stageNum === 1)
{{-- ====================================================
     TAHAP 1: FORMULATION STAGE
     ==================================================== --}}
<div class="stage-instruction">
  <div class="instruction-icon"><i data-lucide="clipboard-list" style="width:24px;height:24px;color:var(--accent);"></i></div>
  <div class="instruction-text">
    <strong>Sebelum memulai pembuatan yogurt, lengkapi rencana proyek kalian terlebih dahulu!</strong>
    Tuliskan bahan-bahan yang akan digunakan, komposisi (takaran) masing-masing bahan, dan durasi fermentasi selama <strong>12 jam</strong>.
  </div>
</div>

<form method="POST" action="{{ route('student.stage.save', 1, false) }}" class="stage-form" data-stage="{{ $stageNum }}" enctype="multipart/form-data">
  @csrf
  <div class="form-section">
    <h3><i data-lucide="users" style="width:18px;height:18px;vertical-align:middle;margin-right:4px;"></i> Identitas Kelompok</h3>
    <div class="form-row">
      <div class="form-group">
        <label for="s1-kelompok">Nama Kelompok</label>
        <input type="text" id="s1-kelompok" name="kelompok" value="{{ $existing['kelompok'] ?? old('kelompok') }}"
               placeholder="Contoh: Kelompok Stroberi" required>
      </div>
      <div class="form-group">
        <label for="s1-ekstrak">Jenis Ekstrak yang Dipilih</label>
        <input type="text" id="s1-ekstrak" name="ekstrak" value="{{ $existing['ekstrak'] ?? old('ekstrak') }}"
               placeholder="Contoh: Ekstrak Stroberi" required>
      </div>
    </div>
    <div class="form-group">
      <label for="s1-anggota">Nama Anggota Kelompok</label>
      <textarea id="s1-anggota" name="anggota" rows="3"
                placeholder="Tuliskan semua nama anggota, satu per baris&#10;Contoh:&#10;1. Andi&#10;2. Budi&#10;3. Citra" required>{{ $existing['anggota'] ?? old('anggota') }}</textarea>
    </div>
  </div>

  <div class="form-section">
    <h3><i data-lucide="flask-conical" style="width:18px;height:18px;vertical-align:middle;margin-right:4px;"></i> Komposisi Bahan</h3>
    <p class="form-hint">Tuliskan semua bahan dan takaran yang akan digunakan secara detail.</p>
    <div class="form-group">
      <label for="s1-komposisi">Komposisi Bahan Lengkap</label>
      <textarea id="s1-komposisi" name="komposisi" rows="5"
                placeholder="Contoh:&#10;- 200 ml Susu UHT Full Cream&#10;- 2 sdm Yogurt plain (starter)&#10;- 50 ml Ekstrak stroberi segar&#10;- 1 sdm gula pasir" required>{{ $existing['komposisi'] ?? old('komposisi') }}</textarea>
    </div>
    <div class="info-pill"><i data-lucide="timer" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Durasi Fermentasi: <strong>12 Jam</strong></div>
  </div>

  <div class="form-section">
    <h3><i data-lucide="lightbulb" style="width:18px;height:18px;vertical-align:middle;margin-right:4px;"></i> Inovasi &amp; Alasan Pemilihan</h3>
    <div class="form-group">
      <label for="s1-alasan">Mengapa kalian memilih komposisi dan jenis ekstrak tersebut? Apa inovasi yang ingin kalian tonjolkan?</label>
      <textarea id="s1-alasan" name="alasan_inovasi" rows="4"
                placeholder="Jelaskan alasan dan inovasi kelompok kalian..." required>{{ $existing['alasan_inovasi'] ?? old('alasan_inovasi') }}</textarea>
    </div>
  </div>

  <div class="form-section">
    <h3><i data-lucide="image" style="width:18px;height:18px;vertical-align:middle;margin-right:4px;"></i> Foto Bahan-Bahan <span class="required-badge">WAJIB</span></h3>
    <p class="form-hint">Upload foto semua bahan yang sudah disiapkan.</p>
    @if(!empty($existing['foto_bahan']))
      <div class="current-photo-wrap">
        <div class="current-photo-label"><i data-lucide="image" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Foto sebelumnya:</div>
        <img src="{{ url('/storage/' . ltrim($existing['foto_bahan'], '/')) }}" class="view-photo" alt="Foto bahan sebelumnya">
        <p class="form-hint">Upload foto baru jika ingin mengganti.</p>
      </div>
    @endif
    <div class="photo-upload-area" onclick="document.getElementById('s1-foto').click()">
      <div class="photo-upload-icon"><i data-lucide="image" style="width:32px;height:32px;color:var(--accent);"></i></div>
      <p>Klik untuk pilih foto bahan</p>
      <small>Format JPG/PNG, maksimal 5MB {{ empty($existing['foto_bahan']) ? '(Wajib)' : '(Opsional untuk update)' }}</small>
      <input type="file" id="s1-foto" name="foto_bahan" accept="image/*" style="display:none"
             onchange="prevPhoto(this,'s1-prev')" {{ empty($existing['foto_bahan']) ? 'required' : '' }}>
    </div>
    <div id="s1-prev" class="photo-preview hidden"></div>
    <div id="s1-warning" class="form-error hidden" style="margin-top:8px"></div>
  </div>

  @if($errors->any())<div class="form-error">{{ $errors->first() }}</div>@endif
  <div class="form-actions">
    <button type="submit" class="btn btn-primary">
      <i data-lucide="save" style="width:16px;height:16px;vertical-align:middle;margin-right:4px;"></i> {{ isset($stagesData[1]) ? 'Simpan Perubahan Rencana Proyek' : 'Simpan Rencana Proyek' }}
    </button>
  </div>
  @if(isset($stagesData[1]))
    <p class="form-hint" style="text-align:center;margin-top:12px"><i data-lucide="edit-3" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Tombol ini tidak permanen — kamu bisa mengubah rencana kapan saja.</p>
  @endif
</form>


@elseif($stageNum === 2)
{{-- ====================================================
     TAHAP 2: PRODUCTION DAY
     ==================================================== --}}
<div class="stage-instruction">
  <div class="instruction-icon"><i data-lucide="flask-conical" style="width:24px;height:24px;color:var(--accent);"></i></div>
  <div class="instruction-text">
    <strong>Saatnya membuat yogurtmu!</strong> Ikuti langkah pembuatan yang telah kamu rancang.
    Setelah selesai, dokumentasikan proses pembuatan dan kondisi awal yogurtmu (jam ke-0) sebelum difermentasi.
  </div>
</div>
<div class="attention-box">
  <i data-lucide="alert-triangle" style="width:16px;height:16px;vertical-align:middle;margin-right:4px;color:var(--gold);"></i> <strong>PERHATIAN:</strong> Simpan yogurt di <strong>suhu ruang (25–30°C)</strong>.
  Tempatkan di ruangan yang <strong>gelap</strong>, tidak terkena sinar matahari langsung.
  <strong>Jangan membuka wadah sama sekali hingga pengamatan pertama di jam ke-8</strong> untuk meminimalkan risiko kontaminasi bakteri luar.
</div>

<form method="POST" action="{{ route('student.stage.save', 2, false) }}" class="stage-form" data-stage="{{ $stageNum }}" enctype="multipart/form-data">
  @csrf
  <div class="form-section">
    <h3><i data-lucide="beaker" style="width:18px;height:18px;vertical-align:middle;margin-right:4px;"></i> Proses Pembuatan</h3>
    <div class="form-group">
      <label>Deskripsi Proses Pembuatan</label>
      <textarea name="proses" rows="5" placeholder="Ceritakan langkah-langkah yang sudah kalian lakukan..." required>{{ old('proses') }}</textarea>
    </div>
  </div>

  <div class="form-section">
    <h3><i data-lucide="help-circle" style="width:18px;height:18px;vertical-align:middle;margin-right:4px;"></i> Pertanyaan Prediksi</h3>
    <div class="prediction-card">
      <p class="prediction-q">Berdasarkan bahan yang kalian gunakan, menurut kalian pada <strong>jam ke berapa</strong> tekstur yogurt akan mulai mengental secara signifikan? Berikan alasannya!</p>
      <div class="form-row">
        <div class="form-group">
          <label>Prediksi Jam ke-</label>
          <div class="input-with-unit">
              <input type="number" name="prediksi_jam" min="1" max="12" step="1" placeholder="Tulis prediksi jam keberapa yogurt mulai mengental" value="{{ old('prediksi_jam') }}" required>
            <span class="input-unit">jam</span>
          </div>
        </div>
        <div class="form-group">
          <label>Alasan Prediksi</label>
          <textarea name="alasan_prediksi" rows="3" placeholder="Karena..." required>{{ old('alasan_prediksi') }}</textarea>
        </div>
      </div>
    </div>
  </div>

  <div class="form-section">
    <h3 class="section-jam"><i data-lucide="timer" style="width:18px;height:18px;vertical-align:middle;margin-right:4px;"></i> Kondisi Awal — Jam ke-0 (Sebelum Fermentasi)</h3>
    <p class="form-hint">Catat kondisi awal yogurt sebelum proses fermentasi dimulai sebagai data <em>baseline</em> pengamatanmu.</p>
    <div class="form-row">
      <div class="form-group">
        <label><i data-lucide="palette" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Warna Awal</label>
        <input type="text" name="jam0_warna" placeholder="Sesuai warna ekstrak bahan, cth: Pink" required value="{{ old('jam0_warna') }}">
      </div>
      <div class="form-group">
        <label><i data-lucide="wind" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Aroma Awal</label>
        <input type="text" name="jam0_aroma" placeholder="Cth: Aroma susu segar + stroberi" value="{{ old('jam0_aroma') }}" required>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label><i data-lucide="smile" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Rasa Awal</label>
        <input type="text" name="jam0_rasa" placeholder="Cth: Manis, segar beraroma stroberi" value="{{ old('jam0_rasa') }}" required>
      </div>
      <div class="form-group">
        <label><i data-lucide="activity" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Tekstur Awal</label>
        <div class="info-pill">Cair (normal untuk awal fermentasi)</div>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group" style="max-width:200px">
        <label><i data-lucide="flask-conical" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Nilai pH Awal (kertas lakmus) <span class="required-badge">WAJIB</span></label>
        <div class="input-with-unit">
          <input type="number" name="jam0_ph" min="0" max="14" step="0.1" placeholder="Cth: 6.5" value="{{ old('jam0_ph') }}" required>
          <span class="input-unit">pH</span>
        </div>
      </div>
      <div class="form-group">
        <label><i data-lucide="camera" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Foto Kertas Lakmus Awal (Jam ke-0) <span class="required-badge">WAJIB</span></label>
        @if(!empty($existing['jam0']['ph_foto']))
          <div class="current-photo-wrap">
            <div class="current-photo-label"><i data-lucide="image" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Foto sebelumnya:</div>
            <img src="{{ url('/storage/' . ltrim($existing['jam0']['ph_foto'], '/')) }}" class="view-photo" alt="Foto kertas lakmus awal sebelumnya" style="max-width: 150px; border-radius: 8px; margin-bottom: 8px;">
            <p class="form-hint">Upload foto baru jika ingin mengganti.</p>
          </div>
        @endif
        <div class="photo-upload-area-sm" onclick="document.getElementById('s2-ph-foto').click()" style="max-width: 320px;">
          <div class="photo-upload-icon" style="display:flex;justify-content:center;margin-bottom:8px;">
            <i data-lucide="image" style="width:24px;height:24px;color:var(--text-muted);"></i>
          </div>
          <p>Klik untuk pilih foto kertas lakmus</p>
          <small>Format JPG/PNG, maks. 5MB</small>
          <input type="file" id="s2-ph-foto" name="jam0_ph_foto" accept="image/*" style="display:none"
                 onchange="prevPhoto(this,'s2-ph-prev')" {{ empty($existing['jam0']['ph_foto'] ?? null) ? 'required' : '' }}>
        </div>
        <div id="s2-ph-prev" class="photo-preview hidden" style="margin-top: 8px;"></div>
      </div>
    </div>
    <div class="form-group">
      <label>Catatan Tambahan</label>
      <textarea name="jam0_catatan" rows="2" placeholder="Catatan lain..." required>{{ old('jam0_catatan') }}</textarea>
    </div>
    <h4><i data-lucide="camera" style="width:16px;height:16px;vertical-align:middle;margin-right:4px;"></i> Foto Produk Sebelum Fermentasi <span class="required-badge">WAJIB</span></h4>
    @if(!empty($existing['jam0']['foto']))
      <div class="current-photo-wrap">
        <div class="current-photo-label"><i data-lucide="image" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Foto sebelumnya:</div>
        <img src="{{ url('/storage/' . ltrim($existing['jam0']['foto'], '/')) }}" class="view-photo" alt="Foto produk awal sebelumnya" style="max-width: 150px; border-radius: 8px; margin-bottom: 8px;">
        <p class="form-hint">Upload foto baru jika ingin mengganti.</p>
      </div>
    @endif
    <div class="photo-upload-area" onclick="document.getElementById('s2-foto').click()">
      <div class="photo-upload-icon"><i data-lucide="image" style="width:32px;height:32px;color:var(--accent);"></i></div>
      <p>Klik untuk pilih foto kondisi awal yogurt</p>
      <small>JPG/PNG, maks. 5MB</small>
      <input type="file" id="s2-foto" name="jam0_foto" accept="image/*" style="display:none"
             class="required-on-empty"
             onchange="prevPhoto(this,'s2-prev')" {{ empty($existing['jam0']['foto'] ?? null) ? 'required' : '' }}>
    </div>
    <div id="s2-prev" class="photo-preview hidden"></div>
    <div id="s2-warning" class="form-error hidden" style="margin-top:8px"></div>
  </div>

  @if($errors->any())<div class="form-error">{{ $errors->first() }}</div>@endif
  <div class="form-actions"><button type="submit" class="btn btn-primary">Simpan & Lanjut ke Pengamatan <i data-lucide="arrow-right" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-left:4px;"></i></button></div>
</form>



@elseif($stageNum === 3)
{{-- ====================================================
  TAHAP 3: JAM KE-8
  ==================================================== --}}

<div class="stage-instruction">
  <div class="instruction-icon"><i data-lucide="clock" style="width:24px;height:24px;color:var(--accent);"></i></div>
  <div class="instruction-text">
    <strong>Waktunya pengamatan pertama!</strong> Setelah melewati <strong>8 jam</strong> masa inkubasi yang tenang, buka wadah sedikit dan ambil 1 sendok sampel yogurt dengan cara steril, lalu segera tutup kembali wadah utama.
    Amati apakah mulai terbentuk <em>lapisan whey</em> (cairan bening) di permukaan, dan catat perubahan organoleptik pada sampel tersebut (warna, aroma, rasa, tekstur).
  </div>
</div>

<form method="POST" action="{{ route('student.stage.save', 3, false) }}" class="stage-form" enctype="multipart/form-data">
  @csrf
  @include('student.partials.organo-fields', ['jamLabel'=>'Jam ke-8', 'stageNum'=>3])
  @if($errors->any())<div class="form-error">{{ $errors->first() }}</div>@endif
  <div class="form-actions"><button type="submit" class="btn btn-primary">Simpan Pengamatan Jam ke-8 <i data-lucide="arrow-right" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-left:4px;"></i></button></div>
</form>


@elseif($stageNum === 4)
{{-- ====================================================
     TAHAP 4: PENGAMATAN FINAL JAM KE-12
     ==================================================== --}}
<div class="stage-instruction">
  <div class="instruction-icon"><i data-lucide="timer" style="width:24px;height:24px;color:var(--accent);"></i></div>
  <div class="instruction-text">
    <strong>Ini adalah pengamatan terakhir!</strong> Yogurtmu sudah melewati <strong>12 jam fermentasi</strong>.
    Lakukan uji organoleptik lengkap, ukur pH akhir, dan tuliskan kesimpulan awalmu mengenai hasil fermentasi kelompokmu.
  </div>
</div>

<form method="POST" action="{{ route('student.stage.save', 4, false) }}" class="stage-form" enctype="multipart/form-data">
  @csrf
  @include('student.partials.organo-fields', ['jamLabel'=>'Jam ke-12 (Final)', 'stageNum'=>4])

  <div class="form-section">
    <h3><i data-lucide="flask-conical" style="width:18px;height:18px;vertical-align:middle;margin-right:4px;"></i> Pengukuran pH Akhir <span class="required-badge">WAJIB</span></h3>
    <p class="form-hint">Ukur pH menggunakan kertas lakmus.</p>
    <div class="form-row">
      <div class="form-group" style="max-width:200px">
        <label>Nilai pH Akhir</label>
        <div class="input-with-unit">
          <input type="number" name="ph_akhir" id="ph-akhir" min="0" max="14" step="0.1"
                 placeholder="Cth: 4.2" required value="{{ old('ph_akhir') }}">
          <span class="input-unit">pH</span>
        </div>
      </div>
      <div class="form-group">
        <label><i data-lucide="camera" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Foto Kertas Lakmus Akhir (Jam ke-12) <span class="required-badge">WAJIB</span></label>
        @if(!empty($existing['ph_akhir_foto']))
          <div class="current-photo-wrap">
            <div class="current-photo-label"><i data-lucide="image" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Foto sebelumnya:</div>
            <img src="{{ url('/storage/' . ltrim($existing['ph_akhir_foto'], '/')) }}" class="view-photo" alt="Foto kertas lakmus akhir sebelumnya" style="max-width: 150px; border-radius: 8px; margin-bottom: 8px;">
            <p class="form-hint">Upload foto baru jika ingin mengganti.</p>
          </div>
        @endif
        <div class="photo-upload-area-sm" onclick="document.getElementById('s4-ph-foto').click()" style="max-width: 320px;">
          <div class="photo-upload-icon" style="display:flex;justify-content:center;margin-bottom:8px;">
            <i data-lucide="image" style="width:24px;height:24px;color:var(--text-muted);"></i>
          </div>
          <p>Klik untuk pilih foto kertas lakmus</p>
          <small>Format JPG/PNG, maks. 5MB</small>
          <input type="file" id="s4-ph-foto" name="ph_akhir_foto" accept="image/*" style="display:none"
                 onchange="prevPhoto(this,'s4-ph-prev')" {{ empty($existing['ph_akhir_foto'] ?? null) ? 'required' : '' }}>
        </div>
        <div id="s4-ph-prev" class="photo-preview hidden" style="margin-top: 8px;"></div>
      </div>
    </div>
  </div>

  <div class="form-section">
    <h3><i data-lucide="file-edit" style="width:18px;height:18px;vertical-align:middle;margin-right:4px;"></i> Kesimpulan Awal</h3>
    <div class="form-group">
      <label>Menurut kelompokmu, apakah yogurt berhasil terbentuk? Mengapa?</label>
      <textarea name="kesimpulan_awal" rows="5" placeholder="Berdasarkan pengamatan kami, yogurt..." required></textarea>
    </div>
  </div>

  @if($errors->any())<div class="form-error">{{ $errors->first() }}</div>@endif
  <div class="form-actions">
    <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#00C896,#7C6FFF);box-shadow:0 4px 20px rgba(124,111,255,0.35)">
      <i data-lucide="check" style="width:18px;height:18px;vertical-align:middle;margin-right:4px;"></i> Selesai &amp; Lihat Hasil
    </button>
  </div>
</form>
@endif
