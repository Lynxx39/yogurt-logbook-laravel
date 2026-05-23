{{-- Organoleptik fields partial: $jamLabel, $stageNum --}}
<div class="form-section">
  <h3 class="section-jam">
    <i data-lucide="microscope" style="width:18px;height:18px;vertical-align:middle;margin-right:6px;"></i>
    Uji Organoleptik — {{ $jamLabel }}
  </h3>
  <p class="form-hint">Gunakan metode 1 sendok sampel. Amati keempat aspek di bawah ini dan tentukan statusnya.</p>

  {{-- WARNA --}}
  <div class="organo-block">
    <div class="organo-header">
      <i data-lucide="palette" style="width:16px;height:16px;vertical-align:middle;margin-right:6px;color:var(--accent);"></i>
      Warna
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Deskripsi Warna yang Diamati</label>
        <input type="text" name="warna" placeholder="Cth: Pink cerah (stroberi), atau ada bercak hijau" required>
      </div>
      <div class="form-group">
        <label>Pilih warna sesuai deskripsi kalian (*pilihan dpt lebih dari 1)</label>
        <div class="checkbox-group">
          <label class="checkbox-card">
            <input type="checkbox" name="warna_opsi[]" value="sesuai warna ekstrak bahan">
            <div class="checkbox-content">
              <span class="checkbox-box"></span>
              <span class="checkbox-label-text">Sesuai warna ekstrak bahan</span>
            </div>
          </label>
          <label class="checkbox-card">
            <input type="checkbox" name="warna_opsi[]" value="muncul bercak hitam/hijau/abu-abu (tekstur jamur)">
            <div class="checkbox-content">
              <span class="checkbox-box"></span>
              <span class="checkbox-label-text">Muncul bercak hitam/hijau/abu-abu (tekstur jamur)</span>
            </div>
          </label>
        </div>
      </div>
    </div>
  </div>

  {{-- AROMA --}}
  <div class="organo-block">
    <div class="organo-header">
      <i data-lucide="wind" style="width:16px;height:16px;vertical-align:middle;margin-right:6px;color:var(--accent);"></i>
      Aroma
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Deskripsi Aroma yang Diamati</label>
        <input type="text" name="aroma" placeholder="Cth: Asam segar beraroma stroberi" required>
      </div>
      <div class="form-group">
        <label>Pilih aroma sesuai deskripsi kalian (*pilihan dpt lebih dari 1)</label>
        <div class="checkbox-group">
          <label class="checkbox-card">
            <input type="checkbox" name="aroma_opsi[]" value="asam khas yogurt">
            <div class="checkbox-content">
              <span class="checkbox-box"></span>
              <span class="checkbox-label-text">Asam khas yogurt</span>
            </div>
          </label>
          <label class="checkbox-card">
            <input type="checkbox" name="aroma_opsi[]" value="beraroma ekstrak buah/sayur">
            <div class="checkbox-content">
              <span class="checkbox-box"></span>
              <span class="checkbox-label-text">Beraroma ekstrak buah/sayur</span>
            </div>
          </label>
          <label class="checkbox-card">
            <input type="checkbox" name="aroma_opsi[]" value="busuk / tengik">
            <div class="checkbox-content">
              <span class="checkbox-box"></span>
              <span class="checkbox-label-text">Busuk / tengik</span>
            </div>
          </label>
          <label class="checkbox-card">
            <input type="checkbox" name="aroma_opsi[]" value="tidak berbau sama sekali">
            <div class="checkbox-content">
              <span class="checkbox-box"></span>
              <span class="checkbox-label-text">Tidak berbau sama sekali</span>
            </div>
          </label>
        </div>
      </div>
    </div>
  </div>

  {{-- TEKSTUR --}}
  <div class="organo-block">
    <div class="organo-header">
      <i data-lucide="activity" style="width:16px;height:16px;vertical-align:middle;margin-right:6px;color:var(--accent);"></i>
      Tekstur
    </div>
    <div class="form-row" style="grid-template-columns: 1fr;">
      <div class="form-group" style="max-width: 400px;">
        <label>Pilih Tekstur yang Diamati</label>
        <select name="tekstur" required>
          <option value="">— Pilih Tekstur —</option>
          <option value="cair/encer">Cair / encer</option>
          <option value="kental">Kental</option>
          <option value="sgt kental">Sangat kental</option>
          <option value="semi-padat">Semi-padat</option>
        </select>
      </div>
    </div>
  </div>

  {{-- RASA --}}
  <div class="organo-block">
    <div class="organo-header">
      <i data-lucide="smile" style="width:16px;height:16px;vertical-align:middle;margin-right:6px;color:var(--accent);"></i>
      Rasa
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Deskripsi Rasa yang Diamati</label>
        <input type="text" name="rasa" placeholder="Cth: Asam manis segar khas stroberi" required>
      </div>
      <div class="form-group">
        <label>Pilih rasa sesuai deskripsi kalian (*pilihan dpt lebih dari 1)</label>
        <div class="checkbox-group">
          <label class="checkbox-card">
            <input type="checkbox" name="rasa_opsi[]" value="asam khas yogurt dan ekstrak">
            <div class="checkbox-content">
              <span class="checkbox-box"></span>
              <span class="checkbox-label-text">Asam khas yogurt dan ekstrak</span>
            </div>
          </label>
          <label class="checkbox-card">
            <input type="checkbox" name="rasa_opsi[]" value="asam manis segar">
            <div class="checkbox-content">
              <span class="checkbox-box"></span>
              <span class="checkbox-label-text">Asam manis segar</span>
            </div>
          </label>
          <label class="checkbox-card">
            <input type="checkbox" name="rasa_opsi[]" value="hambar">
            <div class="checkbox-content">
              <span class="checkbox-box"></span>
              <span class="checkbox-label-text">Hambar</span>
            </div>
          </label>
          <label class="checkbox-card">
            <input type="checkbox" name="rasa_opsi[]" value="rasa asing (pahit/basi)">
            <div class="checkbox-content">
              <span class="checkbox-box"></span>
              <span class="checkbox-label-text">Rasa asing (pahit/basi)</span>
            </div>
          </label>
        </div>
      </div>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label>
        <i data-lucide="file-text" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;color:var(--accent);"></i>
        Catatan Tambahan
      </label>
      <textarea name="catatan" rows="3" placeholder="Pengamatan tambahan, perubahan yang menarik..." required></textarea>
    </div>
    <div class="form-group">
      <label>
        <i data-lucide="camera" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;color:var(--accent);"></i>
        Foto Kondisi Yogurt {{ $jamLabel }}
      </label>
      <div class="photo-upload-area-sm" onclick="document.getElementById('organo-foto-{{ $stageNum }}').click()">
        <div class="photo-upload-icon" style="display:flex;justify-content:center;margin-bottom:8px;">
          <i data-lucide="image" style="width:28px;height:28px;color:var(--text-muted);"></i>
        </div>
        <p>Klik untuk pilih foto</p>
        <input type="file" id="organo-foto-{{ $stageNum }}" name="foto" accept="image/*" style="display:none" required
               onchange="prevPhoto(this,'organo-prev-{{ $stageNum }}')">
      </div>
      <div id="organo-prev-{{ $stageNum }}" class="photo-preview hidden"></div>
    </div>
  </div>
</div>
