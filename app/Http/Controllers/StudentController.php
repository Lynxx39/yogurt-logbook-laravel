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
        // If someone requests the removed stage 3, redirect to stage 4
        if ($n === 3) return redirect('/student/stage/4');
        $user    = User::find(session('user')['id']);
        $logbook = Logbook::with('stages')->firstOrCreate(['user_id' => $user->id]);
        $stagesData = $this->buildStagesData($logbook);

        // Stage 1 is always accessible (editable). Use logical previous stage (skip removed stage 3)
        if ($n > 1 && !isset($stagesData[1])) return redirect('/student/stage/1');
        $prev = $this->prevStageNumber($n);
        if ($prev !== null && !isset($stagesData[$prev])) return redirect('/student/stage/' . $prev);

        $evaluation  = $this->evaluator->evaluateFromStages($stagesData);
        $rekap       = (count($stagesData) >= 5) ? $this->evaluator->buildRekapitulasi($stagesData) : null;
        $activeStage = $n;

        return view('student.dashboard', compact('user', 'logbook', 'stagesData', 'evaluation', 'activeStage', 'rekap'));
    }

    public function saveStage(Request $request, int $n)
    {
        if ($n < 1 || $n > 6) return redirect('/student');
        // Do not allow saving for removed stage 3
        if ($n === 3) return redirect('/student/stage/4')->with('info', 'Tahap pengamatan awal telah dihapus; lanjutkan ke Jam ke-8.');
        $user    = User::find(session('user')['id']);
        $logbook = Logbook::with('stages')->firstOrCreate(['user_id' => $user->id]);
        $stagesData = $this->buildStagesData($logbook);
        $currentStage = $stagesData[$n]['data'] ?? null;

        // Stage 1 always saveable (editable). Others: must complete previous (skip removed stage 3)
        if ($n > 1 && !isset($stagesData[1])) return redirect('/student/stage/1');
        $prev = $this->prevStageNumber($n);
        if ($prev !== null && !isset($stagesData[$prev])) return redirect('/student/stage/' . $prev);

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
            return redirect('/student/stage/6')->with('success', '✨ Pengamatan final tersimpan! Lihat laporan dan hasil evaluasi di bawah.');
        }
        $messages = [
            1 => '✔️ Rencana proyek disimpan! Kamu bisa mengubahnya kapan saja.',
            2 => '✔️ Data Production Day tersimpan!',
            4 => '✔️ Pengamatan Jam ke-8 tersimpan!',
            5 => '✔️ Pengamatan Jam ke-12 tersimpan!',
        ];

        $next = $this->nextStageNumber($n);
        return redirect("/student/stage/{$next}")->with('success', $messages[$n] ?? '✔️ Tersimpan!');
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
        // Stage order (skip removed stage 3)
        $order = [1,2,4,5,6];
        foreach ($order as $s) {
            if (!isset($stagesData[$s])) return $s;
        }
        return 6;
    }

    private function orderedStages(): array
    {
        return [1,2,4,5,6];
    }

    private function nextStageNumber(int $n): int
    {
        $order = $this->orderedStages();
        $idx = array_search($n, $order, true);
        if ($idx === false) return 6;
        return $order[min($idx + 1, count($order) - 1)];
    }

    private function prevStageNumber(int $n): ?int
    {
        $order = $this->orderedStages();
        $idx = array_search($n, $order, true);
        if ($idx === false || $idx === 0) return null;
        return $order[$idx - 1];
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
                if (!$proses || !$warna || !$ph) return null;

                // Preserve existing photo path if present when student resubmits
                $foto = $currentStage['jam0']['foto'] ?? null;
                if ($request->hasFile('jam0_foto') && $request->file('jam0_foto')->isValid()) {
                    $foto = $request->file('jam0_foto')->store('logbook-photos', 'public');
                }
                if (!$foto) return null;

                $fotoPh0 = $currentStage['jam0']['ph_foto'] ?? null;
                if ($request->hasFile('jam0_ph_foto') && $request->file('jam0_ph_foto')->isValid()) {
                    $fotoPh0 = $request->file('jam0_ph_foto')->store('logbook-photos', 'public');
                }
                if (!$fotoPh0) return null;

                return [
                    'proses'          => $proses,
                    'prediksi_jam'    => $prediksi,
                    'alasan_prediksi' => $alasan,
                    'jam0'            => [
                        'warna'   => $warna,
                        'aroma'   => $aroma ?: 'Aroma susu/ekstrak',
                        'tekstur' => 'Cair (awal fermentasi)',
                        'rasa'    => $rasa ?: 'Manis/sesuai ekstrak',
                        'ph'      => (float)$ph,
                        'ph_foto' => $fotoPh0,
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
                if (!$phAkhir) return null;

                $fotoPh12 = $currentStage['ph_akhir_foto'] ?? null;
                if ($request->hasFile('ph_akhir_foto') && $request->file('ph_akhir_foto')->isValid()) {
                    $fotoPh12 = $request->file('ph_akhir_foto')->store('logbook-photos', 'public');
                }
                if (!$fotoPh12) return null;

                $organo['ph_akhir']       = (float)$phAkhir;
                $organo['ph_akhir_foto']  = $fotoPh12;
                $organo['kesimpulan_awal'] = $kesimpulan;
                return $organo;
        }
        return null;
    }

    private function extractOrganoData(Request $request, ?array $currentStage = null): ?array
    {
        $warna      = trim($request->input('warna', ''));
        $warnaOpsi  = $request->input('warna_opsi', []);
        $aroma      = trim($request->input('aroma', ''));
        $aromaOpsi  = $request->input('aroma_opsi', []);
        $tekstur    = trim($request->input('tekstur', ''));
        $rasa       = trim($request->input('rasa', ''));
        $rasaOpsi   = $request->input('rasa_opsi', []);
        $catatan    = trim($request->input('catatan', ''));

        if (!$warna || !$aroma || !$tekstur || !$rasa) return null;

        // Auto-calculate normal status based on selected descriptive options
        $warnaNormal = true;
        if (in_array('muncul bercak hitam/hijau/abu-abu (tekstur jamur)', $warnaOpsi)) {
            $warnaNormal = false;
        }

        $aromaNormal = true;
        if (array_intersect($aromaOpsi, ['busuk / tengik', 'tidak berbau sama sekali'])) {
            $aromaNormal = false;
        }

        $rasaNormal = true;
        if (array_intersect($rasaOpsi, ['hambar', 'rasa asing (pahit/basi)'])) {
            $rasaNormal = false;
        }

        $teksturNormal = $tekstur !== 'cair/encer';

        // Preserve existing foto value if present when resubmitting
        $foto = $currentStage['foto'] ?? null;
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $foto = $request->file('foto')->store('logbook-photos', 'public');
        }

        return [
            'warna'          => $warna,
            'warna_opsi'     => $warnaOpsi,
            'warna_normal'   => $warnaNormal,
            'aroma'          => $aroma,
            'aroma_opsi'     => $aromaOpsi,
            'aroma_normal'   => $aromaNormal,
            'tekstur'        => $tekstur,
            'tekstur_normal' => $teksturNormal,
            'rasa'           => $rasa,
            'rasa_opsi'      => $rasaOpsi,
            'rasa_normal'    => $rasaNormal,
            'catatan'        => $catatan,
            'foto'           => $foto,
        ];
    }
}
