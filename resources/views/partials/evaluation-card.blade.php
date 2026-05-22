@php $ok = $evaluation['result'] === 'berhasil'; @endphp
<div class="evaluation-card {{ $ok ? 'eval-success' : 'eval-fail' }}">
  <div class="eval-header">
    <div class="eval-emoji">{{ $ok ? '🚀' : '🧪' }}</div>
    <div>
      <div class="eval-title">
        @if($ok) Fermentasi Yogurt Kelompokmu <span style="text-decoration:underline">BERHASIL!</span>
        @else Fermentasi Belum Optimal
        @endif
      </div>
      <div class="eval-subtitle">{{ $evaluation['score'] }} dari {{ $evaluation['total'] }} indikator terpenuhi</div>
    </div>
    <div class="eval-score-ring {{ $ok ? 'success' : 'fail' }}">
      <span class="eval-score-num">{{ $evaluation['score'] }}</span>
      <span class="eval-score-den">/{{ $evaluation['total'] }}</span>
    </div>
  </div>

  <div class="eval-indicators">
    @foreach($evaluation['indicators'] as $ind)
    <div class="indicator-row {{ $ind['passed'] ? 'passed' : 'failed' }}">
      <span class="ind-check">{{ $ind['passed'] ? '✔️' : '✖️' }}</span>
      <div class="ind-content">
        <span class="ind-label">{{ $ind['label'] }}</span>
        <span class="ind-actual">{{ $ind['actual'] }}</span>
        <span class="ind-desc">{{ $ind['desc'] }}</span>
      </div>
    </div>
    @endforeach
  </div>

  <div class="eval-verdict">
    @if($ok)
      ✨ <strong>Selamat!</strong> Yogurt yang baik memiliki pH 3,8–4,5 dan tekstur semi-padat.
      Fermentasi laktat oleh bakteri <em>Lactobacillus</em> berjalan dengan baik!
    @else
      @php
        $failed = array_filter($evaluation['indicators'], fn($i) => !$i['passed']);
        $reasons = [];
        foreach ($failed as $f) {
          if (str_contains($f['label'], 'pH')) $reasons[] = 'pH akhir yogurtmu masih di luar rentang 3,8–4,5';
          if (str_contains($f['label'], 'Tekstur')) $reasons[] = 'tekstur masih cair (fermentasi belum optimal)';
          if (str_contains($f['label'], 'Aroma')) $reasons[] = 'aroma tidak normal (kemungkinan kontaminasi)';
          if (str_contains($f['label'], 'Rasa')) $reasons[] = 'rasa tidak normal';
          if (str_contains($f['label'], 'Warna')) $reasons[] = 'warna menunjukkan tanda kontaminasi';
        }
      @endphp
      ❖ Fermentasi belum optimal karena: <strong>{{ implode(', ', $reasons) }}</strong>.
      Kemungkinan penyebab: starter kurang aktif, suhu terlalu rendah, atau terjadi kontaminasi.
      Analisis lebih lanjut di tahap evaluasi poster!
    @endif
  </div>
</div>
