{{-- Lab Report auto-generated (Stage 5) --}}
@php use App\Services\EvaluatorService; @endphp

<div class="lab-report">
  {{-- EVALUATION CARD --}}
  @if($evaluation)
    @include('partials.evaluation-card', compact('evaluation'))
  @endif

  {{-- REKAPITULASI TABLE --}}
  @if($rekap)
  <div class="rekap-section">
    <div class="rekap-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
      <div>
        <h2>
          <i data-lucide="bar-chart-3" style="width:20px;height:20px;vertical-align:middle;margin-right:6px;color:var(--accent);"></i>
          Tabel Rekapitulasi Logbook Kelompok
        </h2>
        <p>Data lengkap seluruh pengamatan fermentasi yogurt</p>
      </div>
      <button type="button" id="btn-download-rekap" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
        <i data-lucide="download" style="width: 14px; height: 14px;"></i> Unduh Gambar
      </button>
    </div>
    <div class="rekap-table-wrap">
      <table class="rekap-table">
        <thead>
          <tr>
            <th>Waktu</th>
            <th>Warna</th>
            <th>Aroma</th>
            <th>Rasa</th>
            <th>Tekstur</th>
            <th>pH</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rekap as $row)
          <tr class="{{ $row['waktu'] === 12 ? 'row-final' : '' }}">
            <td class="td-waktu">
              <strong>{{ $row['label'] }}</strong>
              @if($row['waktu'] === 0)<div class="td-sub">Baseline</div>@endif
              @if($row['waktu'] === 12)
                <div class="td-sub">
                  <i data-lucide="sparkles" style="width:12px;height:12px;vertical-align:middle;margin-right:4px;color:var(--accent);"></i>Data Evaluasi
                </div>
              @endif
            </td>
            <td>
              <div class="rekap-val">{{ $row['warna'] }}</div>
              @if($row['warna_normal'] !== null)
                <div class="rekap-status {{ EvaluatorService::normalClass($row['warna_normal']) }}">
                  {{ EvaluatorService::normalLabel($row['warna_normal']) }}
                </div>
              @endif
            </td>
            <td>
              <div class="rekap-val">{{ $row['aroma'] }}</div>
              @if($row['aroma_normal'] !== null)
                <div class="rekap-status {{ EvaluatorService::normalClass($row['aroma_normal']) }}">
                  {{ EvaluatorService::normalLabel($row['aroma_normal']) }}
                </div>
              @endif
            </td>
            <td>
              <div class="rekap-val">{{ $row['rasa'] }}</div>
              @if($row['rasa_normal'] !== null)
                <div class="rekap-status {{ EvaluatorService::normalClass($row['rasa_normal']) }}">
                  {{ EvaluatorService::normalLabel($row['rasa_normal']) }}
                </div>
              @endif
            </td>
            <td>
              <div class="rekap-val">{{ $row['tekstur'] }}</div>
              @if($row['tekstur_normal'] !== null)
                <div class="rekap-status {{ EvaluatorService::normalClass($row['tekstur_normal']) }}">
                  {{ EvaluatorService::normalLabel($row['tekstur_normal']) }}
                </div>
              @endif
            </td>
            <td class="td-ph">
              @if($row['ph'] !== null)
                <span class="ph-badge">{{ $row['ph'] }}</span>
              @else
                <span class="ph-empty">—</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="rekap-hint">
      <i data-lucide="sparkles" style="width:14px;height:14px;vertical-align:middle;margin-right:6px;color:var(--gold);"></i>
      Gunakan tabel ini sebagai bahan referensi untuk membuat poster di Canva dan presentasi kelompok.
    </div>
  </div>
  @endif

  {{-- KESIMPULAN AWAL SISWA --}}
  @php $finalData = $stagesData[4]['data'] ?? null; @endphp
  @if(!empty($finalData['kesimpulan_awal']))
  <div class="student-conclusion">
    <h3>
      <i data-lucide="file-text" style="width:18px;height:18px;vertical-align:middle;margin-right:6px;color:var(--accent);"></i>
      Kesimpulan Awal Kelompok
    </h3>
    <div class="conclusion-text">{{ $finalData['kesimpulan_awal'] }}</div>
  </div>
  @endif
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btn-download-rekap');
    if (btn) {
        btn.addEventListener('click', function () {
            const tableWrap = document.querySelector('.rekap-section');
            if (!tableWrap) return;
            
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i data-lucide="loader-2" style="width:14px;height:14px;margin-right:6px;display:inline-block;animation:spin 1s linear infinite;"></i> Mengunduh...';
            if (window.lucide) window.lucide.createIcons();
            
            html2canvas(tableWrap, {
                backgroundColor: null,
                scale: 2,
                useCORS: true,
                logging: false,
                windowWidth: 1200,
                onclone: function (clonedDoc) {
                    const style = clonedDoc.createElement('style');
                    style.innerHTML = `
                        *, *::before, *::after {
                            animation: none !important;
                            transition: none !important;
                            transform: none !important;
                        }
                    `;
                    clonedDoc.head.appendChild(style);

                    const clonedSection = clonedDoc.querySelector('.rekap-section');
                    if (clonedSection) {
                        clonedSection.style.width = '1000px';
                        clonedSection.style.padding = '24px';
                        clonedSection.style.borderRadius = '16px';
                        clonedSection.style.margin = '0';
                        if (document.documentElement.getAttribute('data-theme') === 'dark') {
                            clonedSection.style.background = '#1e293b';
                            clonedSection.style.color = '#f8fafc';
                            clonedSection.style.border = '1px solid #334155';
                        } else {
                            clonedSection.style.background = '#ffffff';
                            clonedSection.style.color = '#0f172a';
                            clonedSection.style.border = '1px solid #e2e8f0';
                        }
                    }
                    const clonedBtn = clonedDoc.getElementById('btn-download-rekap');
                    if (clonedBtn) clonedBtn.style.display = 'none';
                    
                    const clonedHint = clonedDoc.querySelector('.rekap-hint');
                    if (clonedHint) clonedHint.style.display = 'none';
                }
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'rekapitulasi-logbook-yogurt.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                
                btn.innerHTML = originalText;
                if (window.lucide) window.lucide.createIcons();
            }).catch(err => {
                console.error(err);
                alert('Gagal mengunduh gambar');
                btn.innerHTML = originalText;
                if (window.lucide) window.lucide.createIcons();
            });
        });
    }
});
</script>
<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endpush
