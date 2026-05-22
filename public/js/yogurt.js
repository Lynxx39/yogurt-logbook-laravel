/* YogurtLog — Public JS for Laravel version */
'use strict';

function setTheme(theme) {
  const isValid = theme === 'dark' || theme === 'light';
  const resolvedTheme = isValid ? theme : 'light';
  document.documentElement.setAttribute('data-theme', resolvedTheme);
  localStorage.setItem('yogurt-theme', resolvedTheme);
  syncThemeToggleUI(resolvedTheme);
}

function getCurrentTheme() {
  const active = document.documentElement.getAttribute('data-theme');
  if (active === 'dark' || active === 'light') return active;
  const stored = localStorage.getItem('yogurt-theme');
  if (stored === 'dark' || stored === 'light') return stored;
  return (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
}

function syncThemeToggleUI(theme) {
  const btn = document.getElementById('theme-toggle');
  const icon = document.getElementById('theme-toggle-icon');
  const text = document.getElementById('theme-toggle-text');
  if (!btn || !icon || !text) return;

  const isDark = theme === 'dark';
  icon.textContent = isDark ? '◐' : '◑';
  text.textContent = isDark ? 'Dark' : 'Light';
  btn.setAttribute('aria-label', isDark ? 'Ganti ke tema terang' : 'Ganti ke tema gelap');
  btn.setAttribute('title', isDark ? 'Ganti ke tema terang' : 'Ganti ke tema gelap');
}

function initThemeToggle() {
  const btn = document.getElementById('theme-toggle');
  if (!btn) return;

  const current = getCurrentTheme();
  setTheme(current);

  btn.addEventListener('click', () => {
    const next = getCurrentTheme() === 'dark' ? 'light' : 'dark';
    setTheme(next);
  });
}

// ---- Tab switch (login/register) ----
function switchTab(tab) {
  document.getElementById('tab-login')?.classList.toggle('active', tab === 'login');
  document.getElementById('tab-register')?.classList.toggle('active', tab === 'register');
  document.getElementById('form-login')?.classList.toggle('hidden', tab !== 'login');
  document.getElementById('form-register')?.classList.toggle('hidden', tab !== 'register');
  // update buttons if they exist
  document.querySelectorAll('.auth-tab').forEach((btn, i) => {
    btn.classList.toggle('active', (i === 0 && tab === 'login') || (i === 1 && tab === 'register'));
  });
}

// ---- Password toggle ----
function togglePw(id) {
  const el = document.getElementById(id);
  if (el) el.type = el.type === 'password' ? 'text' : 'password';
}

// ---- Photo preview ----
function prevPhoto(input, previewId) {
  if (!input.files[0]) return;
  const file = input.files[0];
  if (file.size > 5 * 1024 * 1024) {
    alert('File terlalu besar. Maksimal 5MB.');
    input.value = '';
    return;
  }
  const reader = new FileReader();
  reader.onload = e => {
    const prev = document.getElementById(previewId);
    if (!prev) return;
    prev.innerHTML = `<img src="${e.target.result}" alt="Preview foto">
      <button type="button" class="photo-remove-btn" onclick="rmPhoto('${input.id}','${previewId}')">✖ Hapus Foto</button>`;
    prev.classList.remove('hidden');
  };
  reader.readAsDataURL(file);
}
function rmPhoto(inputId, prevId) {
  const inp = document.getElementById(inputId);
  if (inp) inp.value = '';
  const prev = document.getElementById(prevId);
  if (prev) { prev.innerHTML = ''; prev.classList.add('hidden'); }
}

// ---- Dynamic bahan rows ----
let bahanCount = 3;
function addBahan() {
  const list = document.getElementById('bahan-list');
  if (!list) return;
  const div = document.createElement('div');
  div.className = 'bahan-row';
  div.id = 'br-' + bahanCount;
  div.innerHTML = `
    <input type="text" name="bahan_nama[]" placeholder="Nama bahan" class="bahan-nama" required>
    <input type="text" name="bahan_jumlah[]" placeholder="Jumlah" class="bahan-jumlah" required>
    <input type="text" name="bahan_satuan[]" placeholder="Satuan" class="bahan-satuan">
    <button type="button" class="btn-icon-remove" onclick="this.parentElement.remove()" title="Hapus">✖</button>`;
  list.appendChild(div);
  bahanCount++;
}

// ---- Dynamic langkah rows ----
let langkahCount = 3;
function addLangkah() {
  const list = document.getElementById('langkah-list');
  if (!list) return;
  const div = document.createElement('div');
  div.className = 'langkah-row';
  div.id = 'lr-' + langkahCount;
  div.innerHTML = `
    <span class="langkah-num">${langkahCount + 1}</span>
    <textarea name="langkah[]" class="langkah-text" rows="2" placeholder="Langkah ke-${langkahCount + 1}..." required></textarea>
    <button type="button" class="btn-icon-remove" onclick="this.parentElement.remove();refreshNums()" title="Hapus">✖</button>`;
  list.appendChild(div);
  langkahCount++;
}
function refreshNums() {
  document.querySelectorAll('.langkah-num').forEach((el, i) => el.textContent = i + 1);
}

// ---- Suhu live hint ----
document.addEventListener('input', e => {
  if (e.target.id !== 's3-suhu') return;
  const hint = document.getElementById('s3-suhu-hint');
  if (!hint) return;
  const v = parseFloat(e.target.value);
  if (isNaN(v)) { hint.innerHTML = ''; return; }
  if (v >= 37 && v <= 45) {
    hint.innerHTML = '<span class="hint-block hint-optimal">✔️ Suhu optimal untuk fermentasi yogurt (37–45°C)</span>';
  } else if (v > 45) {
    hint.innerHTML = '<span class="hint-block hint-warning">❗ Terlalu panas — bakteri bisa mati di atas 45°C</span>';
  } else {
    hint.innerHTML = '<span class="hint-block hint-warning">❗ Terlalu dingin — bakteri kurang aktif di bawah 37°C</span>';
  }
});

// ---- Student filter (teacher dashboard) ----
function filterStudents() {
  const search = (document.getElementById('s-search')?.value || '').toLowerCase();
  const filter = document.getElementById('s-filter')?.value || '';
  document.querySelectorAll('.student-card').forEach(card => {
    const nm = card.dataset.name || '';
    const st = card.dataset.status || '';
    card.style.display = ((!search || nm.includes(search)) && (!filter || st === filter)) ? '' : 'none';
  });
}

// ---- pH final hint (Jam ke-12) ----
function showPhHint(val) {
  const hint = document.getElementById('ph-hint');
  if (!hint) return;
  const v = parseFloat(val);
  if (isNaN(v) || val === '') { hint.innerHTML = ''; return; }
  if (v >= 3.8 && v <= 4.5) {
    hint.innerHTML = '<span class="hint-block hint-optimal">✔️ pH optimal yogurt berhasil (3,8–4,5)</span>';
  } else if (v < 3.8) {
    hint.innerHTML = '<span class="hint-block hint-warning">❗ pH terlalu asam — di bawah 3,8</span>';
  } else {
    hint.innerHTML = '<span class="hint-block hint-warning">❗ pH masih tinggi — fermentasi belum optimal (di atas 4,5)</span>';
  }
}

// ---- Flash message auto-dismiss ----
document.addEventListener('DOMContentLoaded', () => {
  initThemeToggle();
  const flash = document.querySelector('.flash-success');
  if (flash) setTimeout(() => flash.remove(), 4000);
});

// Legacy shim: if the modular validator is available (resources/js build), call it
document.addEventListener('DOMContentLoaded', () => {
  if (typeof window.initStageValidation === 'function') {
    try { window.initStageValidation(); } catch (err) { console.error('initStageValidation (legacy) error', err); }
  }
});
