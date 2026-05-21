// Module: yogurt-validation.js
// Provides client-side validation for student stage forms.
export function initStageValidation() {
  function showInlineError(form, msg) {
    const stage = form.dataset.stage || '';
    const stageWarn = stage ? document.getElementById('s' + stage + '-warning') : null;
    if (stageWarn) {
      stageWarn.textContent = msg;
      stageWarn.classList.remove('hidden');
      return;
    }
    let err = form.querySelector('.form-error.inline-validation');
    if (!err) {
      err = document.createElement('div');
      err.className = 'form-error inline-validation';
      const actions = form.querySelector('.form-actions');
      if (actions) actions.parentNode.insertBefore(err, actions);
      else form.appendChild(err);
    }
    err.textContent = msg;
    err.classList.remove('hidden');
  }

  function clearInlineErrors(form) {
    const stage = form.dataset.stage || '';
    const stageWarn = stage ? document.getElementById('s' + stage + '-warning') : null;
    if (stageWarn) stageWarn.classList.add('hidden');
    const err = form.querySelector('.form-error.inline-validation');
    if (err) err.classList.add('hidden');
  }

  document.querySelectorAll('.stage-form').forEach(form => {
    form.addEventListener('submit', e => {
      clearInlineErrors(form);

      // check required non-file inputs
      const requiredInputs = Array.from(form.querySelectorAll('[required]')).filter(i => i.type !== 'file');
      for (const inp of requiredInputs) {
        if ((inp.type === 'radio' || inp.type === 'checkbox')) {
          const name = inp.name;
          const any = Array.from(form.querySelectorAll('[name="' + name + '"]')).some(el => el.checked);
          if (!any) { e.preventDefault(); showInlineError(form, 'Mohon lengkapi semua bidang wajib sebelum menyimpan.'); inp.focus(); return; }
        } else if (!inp.value || inp.value.toString().trim() === '') {
          e.preventDefault(); showInlineError(form, 'Mohon lengkapi semua bidang wajib sebelum menyimpan.'); inp.focus(); return;
        }
      }

      // check required file inputs (explicit required or helper class)
      const fileInputs = Array.from(form.querySelectorAll('input[type="file"]')).filter(fi => fi.required || fi.classList.contains('required-on-empty'));
      for (const fi of fileInputs) {
        if (!fi.files || fi.files.length === 0) {
          e.preventDefault();
          const stage = form.dataset.stage || '';
          const msg = stage === '1' ? 'Foto bahan wajib untuk Tahap 1. Silakan upload foto.' : 'Foto wajib belum diunggah.';
          showInlineError(form, msg);
          const clickable = form.querySelector('.photo-upload-area') || fi;
          if (clickable) clickable.scrollIntoView({behavior:'smooth', block:'center'});
          return;
        }
      }
    });
  });
}

// expose for legacy public scripts that may call window.initStageValidation()
window.initStageValidation = window.initStageValidation || initStageValidation;
