<?php
namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\User;
use App\Services\EvaluatorService;

class TeacherController extends Controller
{
    private EvaluatorService $evaluator;

    public function __construct(EvaluatorService $evaluator)
    {
        $this->evaluator = $evaluator;
    }

    public function dashboard()
    {
        $students = User::where('role', 'siswa')->with(['logbook.stages'])->latest()->get();

        $stats = ['total' => $students->count(), 'selesai' => 0, 'berhasil' => 0, 'kurang' => 0];

        $studentData = $students->map(function ($student) use (&$stats) {
            $logbook    = $student->logbook;
            $stagesData = $this->buildStagesData($logbook);
            $done       = count($stagesData);
            $evaluation = $this->evaluator->evaluateFromStages($stagesData);
            $total = count($this->evaluator->stagesDef());
            if ($done >= $total) $stats['selesai']++;
            if ($evaluation) {
                $evaluation['result'] === 'berhasil' ? $stats['berhasil']++ : $stats['kurang']++;
            }

            return [
                'user'       => $student,
                'stagesData' => $stagesData,
                'done'       => $done,
                'pct'        => round($done / ($total ?: 1) * 100),
                'evaluation' => $evaluation,
                'kelompok'   => $stagesData[1]['data']['kelompok'] ?? null,
            ];
        });

        return view('teacher.dashboard', compact('studentData', 'stats'));
    }

    public function detail(string $username)
    {
        $student    = User::where('username', $username)->where('role', 'siswa')->firstOrFail();
        $logbook    = Logbook::with('stages')->where('user_id', $student->id)->first();
        $stagesData = $this->buildStagesData($logbook);
        $evaluation = $this->evaluator->evaluateFromStages($stagesData);
        $total = count($this->evaluator->stagesDef());
        $rekap      = (count($stagesData) >= $total) ? $this->evaluator->buildRekapitulasi($stagesData) : null;

        return view('teacher.student-detail', compact('student', 'logbook', 'stagesData', 'evaluation', 'rekap'));
    }

    private function buildStagesData(?Logbook $logbook): array
    {
        if (!$logbook) return [];
        $result = [];
        foreach ($logbook->stages as $stage) {
            $result[$stage->stage_number] = [
                'data'         => $stage->data,
                'submitted_at' => $stage->submitted_at,
            ];
        }
        return $result;
    }
}
