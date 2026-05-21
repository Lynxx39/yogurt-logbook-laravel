<?php
namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\LogbookStage;
use App\Models\User;
use App\Services\EvaluatorService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    private EvaluatorService $evaluator;

    public function __construct(EvaluatorService $evaluator)
    {
        $this->evaluator = $evaluator;
    }

    public function dashboard()
    {
        $user    = User::find(session('user')['id']);
        $logbook = Logbook::with('stages')->firstOrCreate(['user_id' => $user->id]);
        $stagesData = $this->buildStagesData($logbook);
        $activeStage = request('stage', $this->getNextStage($stagesData));
        $evaluation  = $this->evaluator->evaluateFromStages($stagesData);
        $rekap       = (count($stagesData) >= 5) ? $this->evaluator->buildRekapitulasi($stagesData) : null;

        return view('student.dashboard', compact('user', 'logbook', 'stagesData', 'evaluation', 'activeStage', 'rekap'));
    }

    public function showStage(int $n)
    {
        if ($n < 1 || $n > 6) return redirect('/student');
        $user    = User::find(session('user')['id']);
        $logbook = Logbook::with('stages')->firstOrCreate(['user_id' => $user->id]);
        $stagesData = $this->buildStagesData($logbook);

        // Stage 1 is always accessible (editable). Others sequential.
        if ($n > 1 && !isset($stagesData[1])) return redirect('/student/stage/1');
        if ($n > 2 && !isset($stagesData[$n - 1])) return redirect('/student/stage/' . ($n - 1));

        $evaluation  = $this->evaluator->evaluateFromStages($stagesData);
        $rekap       = (count($stagesData) >= 5) ? $this->evaluator->buildRekapitulasi($stagesData) : null;
        $activeStage = $n;

        return view('student.dashboard', compact('user', 'logbook', 'stagesData', 'evaluation', 'activeStage', 'rekap'));
    }

    public function saveStage(Request $request, int $n)
    {
        if ($n < 1 || $n > 6) return redirect('/student');
        $user    = User::find(session('user')['id']);
        $logbook = Logbook::with('stages')->firstOrCreate(['user_id' => $user->id]);
        $stagesData = $this->buildStagesData($logbook);
        $currentStage = $stagesData[$n]['data'] ?? null;

        // Stage 1 always saveable (editable). Others: must complete previous.
        if ($n > 1 && !isset($stagesData[1])) return redirect('/student/stage/1');
        if ($n > 2 && !isset($stagesData[$n - 1])) return redirect('/student/stage/' . ($n - 1));

        // Stage 6 is auto-generated, cannot be submitted manually
        if ($n === 6) return redirect('/student/stage/6');

        $data = $this->extractStageData($request, $n, $currentStage);
        if ($data === null) {
            return back()->withErrors(['stage' => 'Data tidak lengkap. Periksa kembali isian Anda.'])->withInput();
        }

        LogbookStage::updateOrCreate(
            ['logbook_id' => $logbook->id, 'stage_number' => $n],
            ['data' => $data, 'submitted_at' => now()]
        );

        // Auto-generate stage 6 when stage 5 is submitted
        if ($n === 5) {
            LogbookStage::updateOrCreate(
                ['logbook_id' => $logbook->id, 'stage_number' => 6],
                ['data' => ['auto' => true], 'submitted_at' => now()]
            );
            return redirect('/student/stage/6')->with('success', '🎉 Pengamatan final tersimpan! Lihat laporan dan hasil evaluasi di bawah.');
        }

        $messages = [
            1 => '✅ Rencana proyek disimpan! Kamu bisa mengubahnya kapan saja.',
            2 => '✅ Data Production Day tersimpan!',
            3 => '✅ Pengamatan Jam ke-4 tersimpan!',
            4 => '✅ Pengamatan Jam ke-8 tersimpan!',
        ];

        $next = min($n + 1, 6);
        return redirect("/student/stage/{$next}")->with('success', $messages[$n] ?? '✅ Tersimpan!');
    }

    // -------------------------------------------------------
    private function buildStagesData(Logbook $logbook): array
    {
        $result = [];
        foreach ($logbook->stages as $stage) {
            $result[$stage->stage_number] = [
                'data'         => $stage->data,
                'submitted_at' => $stage->submitted_at,
            ];
        }
        return $result;
    }

    private function getNextStage(array $stagesData): int
    {
        // Stage 1 always accessible
        if (!isset($stagesData[1])) return 1;
        for ($i = 2; $i <= 6; $i++) {
            if (!isset($stagesData[$i])) return $i;
        }
        return 6;
    }

    private function extractStageData(Request $request, int $n, ?array $currentStage = null): ?array
    {
        switch ($n) {
            case 1:
                $kelompok = trim($request->input('kelompok', ''));
                $anggota  = trim($request->input('anggota', ''));
                $ekstrak  = trim($request->input('ekstrak', ''));
                $komposisi= trim($request->input('komposisi', ''));
                $alasan   = trim($request->input('alasan_inovasi', ''));
                if (!$kelompok || !$anggota || !$ekstrak || !$komposisi) return null;

                $fotoBahan = $currentStage['foto_bahan'] ?? null;
                if ($request->hasFile('foto_bahan') && $request->file('foto_bahan')->isValid()) {
                    $fotoBahan = $request->file('foto_bahan')->store('logbook-photos', 'public');
                }
                if (!$fotoBahan) return null;
                return [
                    'kelompok'      => $kelompok,
                    'anggota'       => $anggota,
                    'ekstrak'       => $ekstrak,
                    'komposisi'     => $komposisi,
                    'alasan_inovasi'=> $alasan,
                    'durasi'        => '12 jam',
                    'foto_bahan'    => $fotoBahan,
                ];

            case 2:
                $proses   = trim($request->input('proses', ''));
                $prediksi = trim($request->input('prediksi_jam', ''));
                $alasan   = trim($request->input('alasan_prediksi', ''));
                // Jam ke-0 data
                $warna   = trim($request->input('jam0_warna', ''));
                $aroma   = trim($request->input('jam0_aroma', ''));
                $rasa    = trim($request->input('jam0_rasa', ''));
                $ph      = $request->input('jam0_ph');
                $catatan = trim($request->input('jam0_catatan', ''));
                if (!$proses || !$warna) return null;

                // Preserve existing photo path if present when student resubmits
                $foto = $currentStage['jam0']['foto'] ?? null;
                if ($request->hasFile('jam0_foto') && $request->file('jam0_foto')->isValid()) {
                    $foto = $request->file('jam0_foto')->store('logbook-photos', 'public');
                }
                if (!$foto) return null;
                return [
                    'proses'          => $proses,
                    'prediksi_jam'    => $prediksi,
                    'alasan_prediksi' => $alasan,
                    'jam0'            => [
                        'warna'   => $warna,
                        'aroma'   => $aroma ?: 'Aroma susu/ekstrak',
                        'tekstur' => 'Cair (awal fermentasi)',
                        'rasa'    => $rasa ?: 'Manis/sesuai ekstrak',
                        'ph'      => $ph ? (float)$ph : null,
                        'catatan' => $catatan,
                        'foto'    => $foto,
                    ],
                ];

            case 3:
            case 4:
                return $this->extractOrganoData($request, $currentStage);

            case 5:
                $organo = $this->extractOrganoData($request, $currentStage);
                if (!$organo) return null;
                $phAkhir = $request->input('ph_akhir');
                $kesimpulan = trim($request->input('kesimpulan_awal', ''));
                $organo['ph_akhir']       = $phAkhir ? (float)$phAkhir : null;
                $organo['kesimpulan_awal'] = $kesimpulan;
                return $organo;
        }
        return null;
    }

    private function extractOrganoData(Request $request, ?array $currentStage = null): ?array
    {
        $warna          = trim($request->input('warna', ''));
        $warnaNormal    = $request->input('warna_normal') === '1';
        $aroma          = trim($request->input('aroma', ''));
        $aromaNormal    = $request->input('aroma_normal') === '1';
        $tekstur        = trim($request->input('tekstur', ''));
        $teksturNormal  = $request->input('tekstur_normal') === '1';
        $rasa           = trim($request->input('rasa', ''));
        $rasaNormal     = $request->input('rasa_normal') === '1';
        $catatan        = trim($request->input('catatan', ''));

        if (!$warna || !$aroma || !$tekstur || !$rasa) return null;

        // Preserve existing foto value if present when resubmitting
        $foto = $currentStage['foto'] ?? null;
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $foto = $request->file('foto')->store('logbook-photos', 'public');
        }

        return [
            'warna'          => $warna,
            'warna_normal'   => $warnaNormal,
            'aroma'          => $aroma,
            'aroma_normal'   => $aromaNormal,
            'tekstur'        => $tekstur,
            'tekstur_normal' => $teksturNormal,
            'rasa'           => $rasa,
            'rasa_normal'    => $rasaNormal,
            'catatan'        => $catatan,
            'foto'           => $foto,
        ];
    }
}
