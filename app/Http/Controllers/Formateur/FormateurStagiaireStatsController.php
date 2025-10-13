<?php

namespace App\Http\Controllers\Formateur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FormateurStagiaireStatsController extends Controller
{
    public function index(Request $request)
    {
        $formateur = Auth::user()?->formateur;
        if (!$formateur) {
            abort(403);
        }

        $search = $request->get('search');
        $platform = $request->get('platform');
        $days = $request->integer('inactive_days');
        $lastLoginFrom = $request->get('last_login_from');
        $lastLoginTo = $request->get('last_login_to');

        $baseQuery = DB::table('stagiaires')
            ->join('users', 'stagiaires.user_id', '=', 'users.id')
            ->join('formateur_stagiaire', 'formateur_stagiaire.stagiaire_id', '=', 'stagiaires.id')
            ->where('formateur_stagiaire.formateur_id', $formateur->id)
            ->leftJoin('user_app_usages as uau_android', function ($q) {
                $q->on('uau_android.user_id', '=', 'users.id')->where('uau_android.platform', '=', 'android');
            })
            ->leftJoin('user_app_usages as uau_ios', function ($q) {
                $q->on('uau_ios.user_id', '=', 'users.id')->where('uau_ios.platform', '=', 'ios');
            })
            ->leftJoin(DB::raw('(SELECT user_id, MAX(completed_at) as last_quiz_at FROM quiz_participations WHERE status = "completed" GROUP BY user_id) qp'), function ($q) {
                $q->on('qp.user_id', '=', 'users.id');
            })
            ->select(
                'stagiaires.id as stagiaire_id',
                'users.id as user_id',
                'users.name',
                'users.email',
                'users.last_login_at',
                'users.last_activity_at',
                DB::raw('CASE WHEN uau_android.user_id IS NOT NULL THEN 1 ELSE 0 END as has_android'),
                DB::raw('CASE WHEN uau_ios.user_id IS NOT NULL THEN 1 ELSE 0 END as has_ios'),
                DB::raw('qp.last_quiz_at')
            );

        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%$search%")
                    ->orWhere('users.email', 'like', "%$search%")
                    ->orWhere('stagiaires.prenom', 'like', "%$search%")
                    ->orWhere('stagiaires.telephone', 'like', "%$search%");
            });
        }

        if ($platform === 'android') {
            $baseQuery->whereNotNull('uau_android.user_id');
        } elseif ($platform === 'ios') {
            $baseQuery->whereNotNull('uau_ios.user_id');
        }

        if (in_array($days, [3, 7, 30], true)) {
            $threshold = now()->subDays($days);
            $baseQuery->where(function ($q) use ($threshold) {
                $q->whereNull('qp.last_quiz_at')
                    ->orWhere('qp.last_quiz_at', '<', $threshold);
            });
        }

        if (!empty($lastLoginFrom)) {
            $baseQuery->where('users.last_login_at', '>=', $lastLoginFrom);
        }
        if (!empty($lastLoginTo)) {
            $baseQuery->where('users.last_login_at', '<=', $lastLoginTo);
        }

        $stagiaires = $baseQuery
            ->orderByDesc('users.last_login_at')
            ->paginate(20)
            ->withQueryString();

        // KPIs inactifs pour le formateur
        $now = now();
        $inactive3 = $this->countInactiveSinceForFormateur($formateur->id, $now->copy()->subDays(3));
        $inactive7 = $this->countInactiveSinceForFormateur($formateur->id, $now->copy()->subDays(7));
        $inactive30 = $this->countInactiveSinceForFormateur($formateur->id, $now->copy()->subDays(30));

        return view('formateur.stagiaires.stats', compact(
            'stagiaires',
            'search',
            'platform',
            'days',
            'lastLoginFrom',
            'lastLoginTo',
            'inactive3',
            'inactive7',
            'inactive30'
        ));
    }

    private function countInactiveSinceForFormateur(int $formateurId, $threshold)
    {
        return DB::table('stagiaires')
            ->join('users', 'stagiaires.user_id', '=', 'users.id')
            ->join('formateur_stagiaire', 'formateur_stagiaire.stagiaire_id', '=', 'stagiaires.id')
            ->where('formateur_stagiaire.formateur_id', $formateurId)
            ->leftJoin(DB::raw('(SELECT user_id, MAX(completed_at) as last_quiz_at FROM quiz_participations WHERE status = "completed" GROUP BY user_id) qp'), function ($q) {
                $q->on('qp.user_id', '=', 'users.id');
            })
            ->where(function ($q) use ($threshold) {
                $q->whereNull('qp.last_quiz_at')
                    ->orWhere('qp.last_quiz_at', '<', $threshold);
            })
            ->count();
    }

    public function export(Request $request)
    {
        $formateur = Auth::user()?->formateur;
        if (!$formateur) abort(403);

        $search = $request->get('search');
        $platform = $request->get('platform');
        $days = $request->integer('inactive_days');
        $lastLoginFrom = $request->get('last_login_from');
        $lastLoginTo = $request->get('last_login_to');

        $query = DB::table('stagiaires')
            ->join('users', 'stagiaires.user_id', '=', 'users.id')
            ->join('formateur_stagiaire', 'formateur_stagiaire.stagiaire_id', '=', 'stagiaires.id')
            ->where('formateur_stagiaire.formateur_id', $formateur->id)
            ->leftJoin('user_app_usages as uau_android', function ($q) {
                $q->on('uau_android.user_id', '=', 'users.id')->where('uau_android.platform', '=', 'android');
            })
            ->leftJoin('user_app_usages as uau_ios', function ($q) {
                $q->on('uau_ios.user_id', '=', 'users.id')->where('uau_ios.platform', '=', 'ios');
            })
            ->leftJoin(DB::raw('(SELECT user_id, MAX(completed_at) as last_quiz_at FROM quiz_participations WHERE status = "completed" GROUP BY user_id) qp'), function ($q) {
                $q->on('qp.user_id', '=', 'users.id');
            })
            ->select(
                'users.name',
                'users.email',
                'users.last_login_at',
                'users.last_activity_at',
                DB::raw('CASE WHEN uau_android.user_id IS NOT NULL THEN 1 ELSE 0 END as has_android'),
                DB::raw('CASE WHEN uau_ios.user_id IS NOT NULL THEN 1 ELSE 0 END as has_ios'),
                DB::raw('qp.last_quiz_at')
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%$search%")
                    ->orWhere('users.email', 'like', "%$search%")
                    ->orWhere('stagiaires.prenom', 'like', "%$search%")
                    ->orWhere('stagiaires.telephone', 'like', "%$search%");
            });
        }
        if ($platform === 'android') {
            $query->whereNotNull('uau_android.user_id');
        } elseif ($platform === 'ios') {
            $query->whereNotNull('uau_ios.user_id');
        }
        if (in_array($days, [3, 7, 30], true)) {
            $threshold = now()->subDays($days);
            $query->where(function ($q) use ($threshold) {
                $q->whereNull('qp.last_quiz_at')
                    ->orWhere('qp.last_quiz_at', '<', $threshold);
            });
        }
        if (!empty($lastLoginFrom)) {
            $query->where('users.last_login_at', '>=', $lastLoginFrom);
        }
        if (!empty($lastLoginTo)) {
            $query->where('users.last_login_at', '<=', $lastLoginTo);
        }

        $rows = $query->orderByDesc('users.last_login_at')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="mes_stagiaires_stats.csv"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['name', 'email', 'last_login_at', 'last_activity_at', 'has_android', 'has_ios', 'last_quiz_at']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->name,
                    $r->email,
                    $r->last_login_at,
                    $r->last_activity_at,
                    $r->has_android ? '1' : '0',
                    $r->has_ios ? '1' : '0',
                    $r->last_quiz_at,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportXlsx(Request $request)
    {
        $formateur = Auth::user()?->formateur;
        if (!$formateur) abort(403);

        $search = $request->get('search');
        $platform = $request->get('platform');
        $days = $request->integer('inactive_days');
        $lastLoginFrom = $request->get('last_login_from');
        $lastLoginTo = $request->get('last_login_to');

        $query = DB::table('stagiaires')
            ->join('users', 'stagiaires.user_id', '=', 'users.id')
            ->join('formateur_stagiaire', 'formateur_stagiaire.stagiaire_id', '=', 'stagiaires.id')
            ->where('formateur_stagiaire.formateur_id', $formateur->id)
            ->leftJoin('user_app_usages as uau_android', function ($q) {
                $q->on('uau_android.user_id', '=', 'users.id')->where('uau_android.platform', '=', 'android');
            })
            ->leftJoin('user_app_usages as uau_ios', function ($q) {
                $q->on('uau_ios.user_id', '=', 'users.id')->where('uau_ios.platform', '=', 'ios');
            })
            ->leftJoin(DB::raw('(SELECT user_id, MAX(completed_at) as last_quiz_at FROM quiz_participations WHERE status = "completed" GROUP BY user_id) qp'), function ($q) {
                $q->on('qp.user_id', '=', 'users.id');
            })
            ->select(
                'users.name',
                'users.email',
                'users.last_login_at',
                'users.last_activity_at',
                DB::raw('CASE WHEN uau_android.user_id IS NOT NULL THEN 1 ELSE 0 END as has_android'),
                DB::raw('CASE WHEN uau_ios.user_id IS NOT NULL THEN 1 ELSE 0 END as has_ios'),
                DB::raw('qp.last_quiz_at')
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%$search%")
                    ->orWhere('users.email', 'like', "%$search%")
                    ->orWhere('stagiaires.prenom', 'like', "%$search%")
                    ->orWhere('stagiaires.telephone', 'like', "%$search%");
            });
        }
        if ($platform === 'android') {
            $query->whereNotNull('uau_android.user_id');
        } elseif ($platform === 'ios') {
            $query->whereNotNull('uau_ios.user_id');
        }
        if (in_array($days, [3, 7, 30], true)) {
            $threshold = now()->subDays($days);
            $query->where(function ($q) use ($threshold) {
                $q->whereNull('qp.last_quiz_at')
                    ->orWhere('qp.last_quiz_at', '<', $threshold);
            });
        }
        if (!empty($lastLoginFrom)) {
            $query->where('users.last_login_at', '>=', $lastLoginFrom);
        }
        if (!empty($lastLoginTo)) {
            $query->where('users.last_login_at', '<=', $lastLoginTo);
        }

        $rows = $query->orderByDesc('users.last_login_at')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['name', 'email', 'last_login_at', 'last_activity_at', 'has_android', 'has_ios', 'last_quiz_at'];
        $sheet->fromArray($headers, null, 'A1');
        $r = 2;
        foreach ($rows as $row) {
            $sheet->setCellValue('A' . $r, $row->name);
            $sheet->setCellValue('B' . $r, $row->email);
            $sheet->setCellValue('C' . $r, (string) $row->last_login_at);
            $sheet->setCellValue('D' . $r, (string) $row->last_activity_at);
            $sheet->setCellValue('E' . $r, $row->has_android ? '1' : '0');
            $sheet->setCellValue('F' . $r, $row->has_ios ? '1' : '0');
            $sheet->setCellValue('G' . $r, (string) $row->last_quiz_at);
            $r++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'mes_stagiaires_stats.xlsx';
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
    }
}
