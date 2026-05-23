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
  icon.innerHTML = isDark ? '<i data-lucide="moon" style="width:14px;height:14px;transition:transform 0.3s ease;"></i>' : '<i data-lucide="sun" style="width:14px;height:14px;transition:transform 0.3s ease;"></i>';
  text.textContent = isDark ? 'Dark' : 'Light';
  btn.setAttribute('aria-label', isDark ? 'Ganti ke tema terang' : 'Ganti ke tema gelap');
  btn.setAttribute('title', isDark ? 'Ganti ke tema terang' : 'Ganti ke tema gelap');
  if (window.lucide) {
    window.lucide.createIcons();
  }
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
  if (!el) return;
  const button = el.parentElement ? el.parentElement.querySelector('.toggle-pass') : null;
  const isPassword = el.type === 'password';
  el.type = isPassword ? 'text' : 'password';
  if (button) {
    button.innerHTML = isPassword ? '<i data-lucide="eye-off" style="width:18px;height:18px;"></i>' : '<i data-lucide="eye" style="width:18px;height:18px;"></i>';
    button.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
    button.setAttribute('title', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
    if (window.lucide) {
      window.lucide.createIcons();
    }
  }
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
      <button type="button" class="photo-remove-btn" onclick="rmPhoto('${input.id}','${previewId}')"><i data-lucide="trash-2" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Hapus Foto</button>`;
    prev.classList.remove('hidden');
    if (window.lucide) {
      window.lucide.createIcons();
    }
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
    <button type="button" class="btn-icon-remove" onclick="this.parentElement.remove()" title="Hapus"><i data-lucide="trash-2" style="width:16px;height:16px;"></i></button>`;
  list.appendChild(div);
  bahanCount++;
  if (window.lucide) {
    window.lucide.createIcons();
  }
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
    <button type="button" class="btn-icon-remove" onclick="this.parentElement.remove();refreshNums()" title="Hapus"><i data-lucide="trash-2" style="width:16px;height:16px;"></i></button>`;
  list.appendChild(div);
  langkahCount++;
  if (window.lucide) {
    window.lucide.createIcons();
  }
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
  initGlobalFormValidation();
  const flash = document.querySelector('.flash-success');
  if (flash) setTimeout(() => flash.remove(), 4000);
  if (window.lucide) {
    window.lucide.createIcons();
  }

  // ---- Nav scroll hint for mobile stage tabs ----
  initNavScrollHint();
});

function initNavScrollHint() {
  const nav    = document.getElementById('sidebar-nav-scroll');
  const hint   = document.getElementById('nav-scroll-hint');
  if (!nav || !hint) return;

  function updateHint() {
    // Only show on mobile (when sidebar is a topbar)
    const isMobile = window.innerWidth <= 900;
    if (!isMobile) {
      hint.classList.add('hidden');
      return;
    }
    // Check if there's overflow to scroll
    const hasOverflow = nav.scrollWidth > nav.clientWidth + 4;
    const scrolledEnough = nav.scrollLeft > 32;
    if (!hasOverflow || scrolledEnough) {
      hint.classList.add('hidden');
    } else {
      hint.classList.remove('hidden');
    }
  }

  nav.addEventListener('scroll', updateHint, { passive: true });
  window.addEventListener('resize', updateHint);
  // Run once after icons are rendered (slight delay for layout)
  setTimeout(updateHint, 300);
}


// Legacy shim: if the modular validator is available (resources/js build), call it
document.addEventListener('DOMContentLoaded', () => {
  if (typeof window.initStageValidation === 'function') {
    try { window.initStageValidation(); } catch (err) { console.error('initStageValidation (legacy) error', err); }
  }
});

function initGlobalFormValidation() {
  document.querySelectorAll('form').forEach(form => {
    if (form.dataset.validationBound === '1') return;
    form.dataset.validationBound = '1';

    const escapeSelector = value => {
      if (window.CSS && typeof window.CSS.escape === 'function') return window.CSS.escape(value);
      return String(value).replace(/\\/g, '\\\\').replace(/"/g, '\\"');
    };

    form.noValidate = true;

    const clearFieldState = field => {
      field.classList.remove('validation-field-error');
      field.removeAttribute('aria-invalid');
      const group = field.closest('.form-group, .organo-block, .photo-upload-area, .photo-upload-area-sm');
      if (group) group.classList.remove('validation-group-error');
    };

    const markFieldState = field => {
      field.classList.add('validation-field-error');
      field.setAttribute('aria-invalid', 'true');
      const group = field.closest('.form-group, .organo-block, .photo-upload-area, .photo-upload-area-sm');
      if (group) group.classList.add('validation-group-error');
    };

    const getFieldLabel = field => {
      const id = field.id;
      if (id) {
        const label = form.querySelector('label[for="' + escapeSelector(id) + '"]');
        if (label) return label.textContent.replace(/\s+/g, ' ').trim();
      }

      const groupLabel = field.closest('.form-group, .organo-block, .photo-upload-area, .photo-upload-area-sm')?.querySelector('label, .organo-header, .current-photo-label, h4, h3, p');
      if (groupLabel) {
        return groupLabel.textContent.replace(/\s+/g, ' ').trim();
      }

      if (field.placeholder) return field.placeholder.replace(/\s+/g, ' ').trim();
      if (field.name) return field.name.replace(/_/g, ' ');
      return 'kolom ini';
    };

    const isFieldMissing = field => {
      if (field.disabled) return false;
      if (field.type === 'radio' || field.type === 'checkbox') {
        const checked = form.querySelectorAll('input[name="' + escapeSelector(field.name) + '"]:checked');
        return checked.length === 0;
      }
      if (field.type === 'file') return !field.files || field.files.length === 0;
      if (field.tagName === 'SELECT') return !field.value;
      return !field.value || field.value.toString().trim() === '';
    };

    const showValidationToast = (title, details) => {
      const container = document.getElementById('toast-container');
      if (!container) return;

      const oldToast = container.querySelector('.validation-toast');
      if (oldToast) oldToast.remove();

      const toast = document.createElement('div');
      toast.className = 'toast validation-toast show';
      toast.innerHTML = `
        <div class="toast-icon"><i data-lucide="alert-triangle" style="width:20px;height:20px;color:var(--warning);"></i></div>
        <div class="toast-body">
          <div class="toast-title">${title}</div>
          <div class="toast-message">${details}</div>
        </div>
        <button type="button" class="toast-close" aria-label="Tutup">×</button>
      `;
      toast.querySelector('.toast-close')?.addEventListener('click', () => toast.remove());
      container.appendChild(toast);
      if (window.lucide) {
        window.lucide.createIcons();
      }
      window.setTimeout(() => toast.remove(), 5500); // slightly longer to read lists
    };

    const focusFirstInvalid = fields => {
      const first = fields[0];
      if (!first) return;
      first.scrollIntoView({ behavior: 'smooth', block: 'center' });
      first.focus({ preventScroll: true });
    };

    const getMissingFields = () => {
      const requiredFields = Array.from(form.querySelectorAll('[required]'));
      const seen = new Set();
      const missing = [];

      requiredFields.forEach(field => {
        const key = field.type === 'radio' || field.type === 'checkbox' ? field.name : field;
        if (seen.has(key)) return;
        if (isFieldMissing(field)) {
          missing.push(field);
          markFieldState(field);
        }
        seen.add(key);
      });

      return missing;
    };

    form.addEventListener('input', e => {
      const field = e.target;
      if (!field.matches?.('[required]')) return;
      if (!isFieldMissing(field)) clearFieldState(field);
    });

    form.addEventListener('change', e => {
      const field = e.target;
      if (!field.matches?.('[required]')) return;
      if (!isFieldMissing(field)) clearFieldState(field);
    });

    form.addEventListener('submit', e => {
      const missingFields = getMissingFields();
      if (!missingFields.length) return;

      e.preventDefault();

      const fieldNames = missingFields.slice(0, 3).map(getFieldLabel);
      const extraCount = missingFields.length - fieldNames.length;
      
      let detailHtml = '';
      if (fieldNames.length > 0) {
        detailHtml = '<ul class="toast-field-list">';
        fieldNames.forEach(name => {
          detailHtml += `<li><i data-lucide="chevron-right" style="width:12px;height:12px;color:var(--warning);margin-right:4px;"></i>${name}</li>`;
        });
        if (extraCount > 0) {
          detailHtml += `<li class="toast-field-more">+ ${extraCount} kolom lainnya...</li>`;
        }
        detailHtml += '</ul>';
      } else {
        detailHtml = 'Silakan lengkapi semua kolom yang bertanda wajib.';
      }

      showValidationToast('Isian Belum Lengkap', detailHtml);
      focusFirstInvalid(missingFields);
    });
  });
}

window.initStageValidation = initGlobalFormValidation;
window.initGlobalFormValidation = initGlobalFormValidation;
