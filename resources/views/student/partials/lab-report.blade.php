{{-- Lab Report auto-generated (Stage 6) --}}
@php use App\Services\EvaluatorService; @endphp

<div class="lab-report">
  {{-- EVALUATION CARD --}}
  @if($evaluation)
    @include('partials.evaluation-card', compact('evaluation'))
  @endif

  {{-- REKAPITULASI TABLE --}}
  @if($rekap)
  <div class="rekap-section">
    <div class="rekap-header">
      <h2>📊 Tabel Rekapitulasi Logbook Kelompok</h2>
      <p>Data lengkap seluruh pengamatan fermentasi yogurt</p>
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
              @if($row['waktu'] === 12)<div class="td-sub">⭐ Data Evaluasi</div>@endif
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
      💡 Gunakan tabel ini sebagai bahan referensi untuk membuat poster di Canva dan presentasi kelompok.
    </div>
  </div>
  @endif

  {{-- KESIMPULAN AWAL SISWA --}}
  @php $s5Data = $stagesData[5]['data'] ?? null; @endphp
  @if(!empty($s5Data['kesimpulan_awal']))
  <div class="student-conclusion">
    <h3>📝 Kesimpulan Awal Kelompok</h3>
    <div class="conclusion-text">{{ $s5Data['kesimpulan_awal'] }}</div>
  </div>
  @endif
</div>
