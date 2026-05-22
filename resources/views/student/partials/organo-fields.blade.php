{{-- Organoleptik fields partial: $jamLabel, $stageNum --}}
<div class="form-section">
  <h3 class="section-jam">🔬 Uji Organoleptik — {{ $jamLabel }}</h3>
  <p class="form-hint">Gunakan metode 1 sendok sampel. Amati keempat aspek di bawah ini dan tentukan statusnya.</p>

  {{-- WARNA --}}
  <div class="organo-block">
    <div class="organo-header">🎨 Warna</div>
    <div class="organo-desc">Normal: Sesuai warna ekstrak bahan. Tidak Normal: Muncul bercak hitam/hijau/abu-abu.</div>
    <div class="form-row">
      <div class="form-group">
        <label>Deskripsi Warna yang Diamati</label>
        <input type="text" name="warna" placeholder="Cth: Pink cerah (stroberi), atau ada bercak hijau" required>
      </div>
      <div class="form-group">
        <label>Status Warna</label>
        <div class="radio-group grid-2">
          <label class="radio-card normal-card">
            <input type="radio" name="warna_normal" value="1" required>
            <div class="radio-content"><span class="radio-icon">✔️</span><span>Normal</span></div>
          </label>
          <label class="radio-card abnormal-card">
            <input type="radio" name="warna_normal" value="0">
            <div class="radio-content"><span class="radio-icon">✖️</span><span>Tidak Normal</span></div>
          </label>
        </div>
      </div>
    </div>
  </div>

  {{-- AROMA --}}
  <div class="organo-block">
    <div class="organo-header">👃 Aroma</div>
    <div class="organo-desc">Normal: Asam khas yogurt / Asam segar beraroma buah/sayur. Tidak Normal: Bau busuk / Tengik / Tidak berbau sama sekali.</div>
    <div class="form-row">
      <div class="form-group">
        <label>Deskripsi Aroma yang Diamati</label>
        <input type="text" name="aroma" placeholder="Cth: Asam segar beraroma stroberi" required>
      </div>
      <div class="form-group">
        <label>Status Aroma</label>
        <div class="radio-group grid-2">
          <label class="radio-card normal-card">
            <input type="radio" name="aroma_normal" value="1" required>
            <div class="radio-content"><span class="radio-icon">✔️</span><span>Normal</span></div>
          </label>
          <label class="radio-card abnormal-card">
            <input type="radio" name="aroma_normal" value="0">
            <div class="radio-content"><span class="radio-icon">✖️</span><span>Tidak Normal</span></div>
          </label>
        </div>
      </div>
    </div>
  </div>

  {{-- TEKSTUR --}}
  <div class="organo-block">
    <div class="organo-header">🥄 Tekstur</div>
    <div class="organo-desc">Normal: Kental / Sangat Kental / Semi-padat (sesuai SNI). Tidak Normal: Cair / Encer (gagal memadat).</div>
    <div class="form-row">
      <div class="form-group">
        <label>Pilih Tekstur yang Diamati</label>
        <select name="tekstur" required>
          <option value="">— Pilih Tekstur —</option>
          <optgroup label="✔️ Normal">
            <option value="Kental">Kental</option>
            <option value="Sangat Kental">Sangat Kental</option>
            <option value="Semi-padat">Semi-padat</option>
          </optgroup>
          <optgroup label="✖️ Tidak Normal">
            <option value="Cair (Encer/Gagal memadat)">Cair (Encer/Gagal memadat)</option>
          </optgroup>
        </select>
      </div>
      <div class="form-group">
        <label>Status Tekstur</label>
        <div class="radio-group grid-2">
          <label class="radio-card normal-card">
            <input type="radio" name="tekstur_normal" value="1" required>
            <div class="radio-content"><span class="radio-icon">✔️</span><span>Normal</span></div>
          </label>
          <label class="radio-card abnormal-card">
            <input type="radio" name="tekstur_normal" value="0">
            <div class="radio-content"><span class="radio-icon">✖️</span><span>Tidak Normal</span></div>
          </label>
        </div>
      </div>
    </div>
  </div>

  {{-- RASA --}}
  <div class="organo-block">
    <div class="organo-header">👅 Rasa</div>
    <div class="organo-desc">Normal: Asam manis segar / Khas yogurt dan ekstrak. Tidak Normal: Pahit / Sangat hambar / Rasa asing (basi/busuk).</div>
    <div class="form-row">
      <div class="form-group">
        <label>Deskripsi Rasa yang Diamati</label>
        <input type="text" name="rasa" placeholder="Cth: Asam manis segar khas stroberi" required>
      </div>
      <div class="form-group">
        <label>Status Rasa</label>
        <div class="radio-group grid-2">
          <label class="radio-card normal-card">
            <input type="radio" name="rasa_normal" value="1" required>
            <div class="radio-content"><span class="radio-icon">✔️</span><span>Normal</span></div>
          </label>
          <label class="radio-card abnormal-card">
            <input type="radio" name="rasa_normal" value="0">
            <div class="radio-content"><span class="radio-icon">✖️</span><span>Tidak Normal</span></div>
          </label>
        </div>
      </div>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label>📝 Catatan Tambahan</label>
      <textarea name="catatan" rows="3" placeholder="Pengamatan tambahan, perubahan yang menarik..."></textarea>
    </div>
    <div class="form-group">
      <label>📸 Foto Kondisi Yogurt {{ $jamLabel }}</label>
      <div class="photo-upload-area-sm" onclick="document.getElementById('organo-foto-{{ $stageNum }}').click()">
        <span class="photo-upload-icon" style="font-size:24px">🖼️</span>
        <p>Klik untuk pilih foto</p>
        <input type="file" id="organo-foto-{{ $stageNum }}" name="foto" accept="image/*" style="display:none"
               onchange="prevPhoto(this,'organo-prev-{{ $stageNum }}')">
      </div>
      <div id="organo-prev-{{ $stageNum }}" class="photo-preview hidden"></div>
    </div>
  </div>
</div>
