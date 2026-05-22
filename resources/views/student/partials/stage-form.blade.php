{{-- Stage Form Partial — all 6 stages --}}
{{-- Variables: $stageNum, $stagesData, $existing (nullable pre-fill data) --}}

@if($stageNum === 1)
{{-- ====================================================
     TAHAP 1: FORMULATION STAGE
     ==================================================== --}}
<div class="stage-instruction">
  <div class="instruction-icon">🧾</div>
  <div class="instruction-text">
    <strong>Sebelum memulai pembuatan yogurt, lengkapi rencana proyek kalian terlebih dahulu!</strong>
    Tuliskan bahan-bahan yang akan digunakan, komposisi (takaran) masing-masing bahan, dan durasi fermentasi selama <strong>12 jam</strong>.
  </div>
</div>

<form method="POST" action="{{ route('student.stage.save', 1) }}" class="stage-form" data-stage="{{ $stageNum }}" enctype="multipart/form-data">
  @csrf
  <div class="form-section">
    <h3>🧑‍🤝‍🧑 Identitas Kelompok</h3>
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
    <h3>🧪 Komposisi Bahan</h3>
    <p class="form-hint">Tuliskan semua bahan dan takaran yang akan digunakan secara detail.</p>
    <div class="form-group">
      <label for="s1-komposisi">Komposisi Bahan Lengkap</label>
      <textarea id="s1-komposisi" name="komposisi" rows="5"
                placeholder="Contoh:&#10;- 200 ml Susu UHT Full Cream&#10;- 2 sdm Yogurt plain (starter)&#10;- 50 ml Ekstrak stroberi segar&#10;- 1 sdm gula pasir" required>{{ $existing['komposisi'] ?? old('komposisi') }}</textarea>
    </div>
    <div class="info-pill">⏱️ Durasi Fermentasi: <strong>12 Jam</strong></div>
  </div>

  <div class="form-section">
    <h3>💡 Inovasi &amp; Alasan Pemilihan</h3>
    <div class="form-group">
      <label for="s1-alasan">Mengapa kalian memilih komposisi dan jenis ekstrak tersebut? Apa inovasi yang ingin kalian tonjolkan?</label>
      <textarea id="s1-alasan" name="alasan_inovasi" rows="4"
                placeholder="Jelaskan alasan dan inovasi kelompok kalian..." required>{{ $existing['alasan_inovasi'] ?? old('alasan_inovasi') }}</textarea>
    </div>
  </div>

  <div class="form-section">
    <h3>🖼️ Foto Bahan-Bahan <span class="required-badge">WAJIB</span></h3>
    <p class="form-hint">Upload foto semua bahan yang sudah disiapkan.</p>
    @if(!empty($existing['foto_bahan']))
      <div class="current-photo-wrap">
        <div class="current-photo-label">🖼️ Foto sebelumnya:</div>
        <img src="{{ Storage::url($existing['foto_bahan']) }}" class="view-photo" alt="Foto bahan sebelumnya">
        <p class="form-hint">Upload foto baru jika ingin mengganti.</p>
      </div>
    @endif
    <div class="photo-upload-area" onclick="document.getElementById('s1-foto').click()">
      <div class="photo-upload-icon">🖼️</div>
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
      {{ isset($stagesData[1]) ? '🗂️ Simpan Perubahan Rencana Proyek' : '🗂️ Simpan Rencana Proyek' }}
    </button>
  </div>
  @if(isset($stagesData[1]))
    <p class="form-hint" style="text-align:center;margin-top:12px">✍️ Tombol ini tidak permanen — kamu bisa mengubah rencana kapan saja.</p>
  @endif
</form>


@elseif($stageNum === 2)
{{-- ====================================================
     TAHAP 2: PRODUCTION DAY
     ==================================================== --}}
<div class="stage-instruction">
  <div class="instruction-icon">🧪</div>
  <div class="instruction-text">
    <strong>Saatnya membuat yogurtmu!</strong> Ikuti langkah pembuatan yang telah kamu rancang.
    Setelah selesai, dokumentasikan proses pembuatan dan kondisi awal yogurtmu (jam ke-0) sebelum difermentasi.
  </div>
</div>
<div class="attention-box">
  ❗ <strong>PERHATIAN:</strong> Simpan yogurt di <strong>suhu ruang (25–30°C)</strong>.
  Tempatkan di ruangan yang <strong>gelap</strong>, tidak terkena sinar matahari langsung.
  <strong>Jangan membuka wadah sama sekali hingga pengamatan pertama di jam ke-8</strong> untuk meminimalkan risiko kontaminasi bakteri luar.
</div>

<form method="POST" action="{{ route('student.stage.save', 2) }}" class="stage-form" data-stage="{{ $stageNum }}" enctype="multipart/form-data">
  @csrf
  <div class="form-section">
    <h3>🔬 Proses Pembuatan</h3>
    <div class="form-group">
      <label>Deskripsi Proses Pembuatan</label>
      <textarea name="proses" rows="5" placeholder="Ceritakan langkah-langkah yang sudah kalian lakukan..." required>{{ old('proses') }}</textarea>
    </div>
  </div>

  <div class="form-section">
    <h3>🤔 Pertanyaan Prediksi</h3>
    <div class="prediction-card">
      <p class="prediction-q">Berdasarkan bahan yang kalian gunakan, menurut kalian pada <strong>jam ke berapa</strong> tekstur yogurt akan mulai mengental secara signifikan? Berikan alasannya!</p>
      <div class="form-row">
        <div class="form-group">
          <label>Prediksi Jam ke-</label>
          <div class="input-with-unit">
              <input type="number" name="prediksi_jam" min="1" max="12" step="1" placeholder="Contoh: 8" value="{{ old('prediksi_jam') }}" required>
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
    <h3 class="section-jam">⏱️ Kondisi Awal — Jam ke-0 (Sebelum Fermentasi)</h3>
    <p class="form-hint">Catat kondisi awal yogurt sebelum proses fermentasi dimulai sebagai data <em>baseline</em> pengamatanmu.</p>
    <div class="form-row">
      <div class="form-group">
        <label>🎨 Warna Awal</label>
        <input type="text" name="jam0_warna" placeholder="Sesuai warna ekstrak bahan, cth: Pink" required value="{{ old('jam0_warna') }}">
      </div>
      <div class="form-group">
        <label>👃 Aroma Awal</label>
        <input type="text" name="jam0_aroma" placeholder="Cth: Aroma susu segar + stroberi" value="{{ old('jam0_aroma') }}" required>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>👅 Rasa Awal</label>
        <input type="text" name="jam0_rasa" placeholder="Cth: Manis, segar beraroma stroberi" value="{{ old('jam0_rasa') }}" required>
      </div>
      <div class="form-group">
        <label>🥄 Tekstur Awal</label>
        <div class="info-pill">Cair (normal untuk awal fermentasi)</div>
      </div>
    </div>
    <div class="form-group" style="max-width:200px">
      <label>🧪 Nilai pH Awal (kertas lakmus)</label>
      <div class="input-with-unit">
        <input type="number" name="jam0_ph" min="0" max="14" step="0.1" placeholder="Cth: 6.5" value="{{ old('jam0_ph') }}">
        <span class="input-unit">pH</span>
      </div>
    </div>
    <div class="form-group">
      <label>Catatan Tambahan</label>
      <textarea name="jam0_catatan" rows="2" placeholder="Catatan lain..." required>{{ old('jam0_catatan') }}</textarea>
    </div>
    <h4>📸 Foto Produk Sebelum Fermentasi <span class="required-badge">WAJIB</span></h4>
    <div class="photo-upload-area" onclick="document.getElementById('s2-foto').click()">
      <div class="photo-upload-icon">🖼️</div>
      <p>Klik untuk pilih foto kondisi awal yogurt</p>
      <small>JPG/PNG, maks. 5MB</small>
      <input type="file" id="s2-foto" name="jam0_foto" accept="image/*" style="display:none"
             class="required-on-empty"
             onchange="prevPhoto(this,'s2-prev')" {{ empty($existing['jam0_foto'] ?? $existing['foto'] ?? null) ? 'required' : '' }}>
    </div>
    <div id="s2-prev" class="photo-preview hidden"></div>
    <div id="s2-warning" class="form-error hidden" style="margin-top:8px"></div>
  </div>

  @if($errors->any())<div class="form-error">{{ $errors->first() }}</div>@endif
  <div class="form-actions"><button type="submit" class="btn btn-primary">Simpan & Lanjut ke Pengamatan ↗</button></div>
</form>



@elseif($stageNum === 4)
{{-- ====================================================
     TAHAP 4: JAM KE-8
     ==================================================== --}}

<div class="stage-instruction">
  <div class="instruction-icon">🕗</div>
  <div class="instruction-text">
    <strong>Waktunya pengamatan pertama!</strong> Setelah melewati <strong>8 jam</strong> masa inkubasi yang tenang, buka wadah sedikit dan ambil 1 sendok sampel yogurt dengan cara steril, lalu segera tutup kembali wadah utama.
    Amati apakah mulai terbentuk <em>lapisan whey</em> (cairan bening) di permukaan, dan catat perubahan organoleptik pada sampel tersebut (warna, aroma, rasa, tekstur).
  </div>
</div>

<form method="POST" action="{{ route('student.stage.save', 4) }}" class="stage-form" enctype="multipart/form-data">
  @csrf
  @include('student.partials.organo-fields', ['jamLabel'=>'Jam ke-8', 'stageNum'=>4])
  @if($errors->any())<div class="form-error">{{ $errors->first() }}</div>@endif
  <div class="form-actions"><button type="submit" class="btn btn-primary">Simpan Pengamatan Jam ke-8 ↗</button></div>
</form>


@elseif($stageNum === 5)
{{-- ====================================================
     TAHAP 5: PENGAMATAN FINAL JAM KE-12
     ==================================================== --}}
<div class="stage-instruction">
  <div class="instruction-icon">⏱️</div>
  <div class="instruction-text">
    <strong>Ini adalah pengamatan terakhir!</strong> Yogurtmu sudah melewati <strong>12 jam fermentasi</strong>.
    Lakukan uji organoleptik lengkap, ukur pH akhir, dan tuliskan kesimpulan awalmu mengenai hasil fermentasi kelompokmu.
  </div>
</div>

<form method="POST" action="{{ route('student.stage.save', 5) }}" class="stage-form" enctype="multipart/form-data">
  @csrf
  @include('student.partials.organo-fields', ['jamLabel'=>'Jam ke-12 (Final)', 'stageNum'=>5])

  <div class="form-section">
    <h3>🧪 Pengukuran pH Akhir <span class="required-badge">WAJIB</span></h3>
    <p class="form-hint">Ukur pH menggunakan kertas lakmus. pH berhasil: <strong>3,8–4,5</strong></p>
    <div class="form-group" style="max-width:200px">
      <label>Nilai pH Akhir</label>
      <div class="input-with-unit">
        <input type="number" name="ph_akhir" id="ph-akhir" min="0" max="14" step="0.1"
               placeholder="Cth: 4.2" required oninput="showPhHint(this.value)">
        <span class="input-unit">pH</span>
      </div>
      <div id="ph-hint" class="hint-block-wrap"></div>
    </div>
  </div>

  <div class="form-section">
    <h3>📝 Kesimpulan Awal</h3>
    <div class="form-group">
      <label>Menurut kelompokmu, apakah yogurt berhasil terbentuk? Mengapa?</label>
      <textarea name="kesimpulan_awal" rows="5" placeholder="Berdasarkan pengamatan kami, yogurt..." required></textarea>
    </div>
  </div>

  @if($errors->any())<div class="form-error">{{ $errors->first() }}</div>@endif
  <div class="form-actions">
    <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#00C896,#7C6FFF);box-shadow:0 4px 20px rgba(124,111,255,0.35)">
      🧪 Selesai &amp; Lihat Hasil
    </button>
  </div>
</form>
@endif
