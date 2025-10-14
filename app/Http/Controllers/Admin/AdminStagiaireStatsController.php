<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminStagiaireStatsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $platform = $request->get('platform'); // android|ios
        $days = $request->integer('inactive_days'); // 3|7|30
        $formateurId = $request->integer('formateur_id');
        $partenaireId = $request->integer('partenaire_id');
        $lastLoginFrom = $request->get('last_login_from');
        $lastLoginTo = $request->get('last_login_to');

        $baseQuery = DB::table('stagiaires')
            ->join('users', 'stagiaires.user_id', '=', 'users.id')
            ->when($formateurId, function ($q) use ($formateurId) {
                $q->join('formateur_stagiaire as fs', 'fs.stagiaire_id', '=', 'stagiaires.id')
                    ->where('fs.formateur_id', $formateurId);
            })
            ->when($partenaireId, function ($q) use ($partenaireId) {
                $q->where('stagiaires.partenaire_id', $partenaireId);
            })
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

        // KPIs inactifs
        $now = now();
        $inactive3 = $this->countInactiveSince($now->copy()->subDays(3));
        $inactive7 = $this->countInactiveSince($now->copy()->subDays(7));
        $inactive30 = $this->countInactiveSince($now->copy()->subDays(30));

        // listes pour filtres
        $formateurs = DB::table('formateurs')->join('users', 'formateurs.user_id', '=', 'users.id')->select('formateurs.id', 'users.name')->orderBy('users.name')->get();
        $partenaires = DB::table('partenaires')->select('id', 'identifiant')->orderBy('identifiant')->get();

        return view('admin.stagiaires.stats', compact(
            'stagiaires',
            'search',
            'platform',
            'days',
            'inactive3',
            'inactive7',
            'inactive30',
            'formateurs',
            'partenaires',
            'formateurId',
            'partenaireId',
            'lastLoginFrom',
            'lastLoginTo'
        ));
    }

    public function export(Request $request)
    {
        $search = $request->get('search');
        $platform = $request->get('platform');
        $days = $request->integer('inactive_days');

        $query = DB::table('stagiaires')
            ->join('users', 'stagiaires.user_id', '=', 'users.id')
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

        $rows = $query->orderByDesc('users.last_login_at')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stagiaires_stats.csv"',
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
        // Reutiliser la même logique de filtre
        $requestCsv = $request->merge([]); // clone inputs
        // Construire la requête comme dans export()
        $search = $request->get('search');
        $platform = $request->get('platform');
        $days = $request->integer('inactive_days');
        $formateurId = $request->integer('formateur_id');
        $partenaireId = $request->integer('partenaire_id');
        $lastLoginFrom = $request->get('last_login_from');
        $lastLoginTo = $request->get('last_login_to');

        $query = DB::table('stagiaires')
            ->join('users', 'stagiaires.user_id', '=', 'users.id')
            ->when($formateurId, function ($q) use ($formateurId) {
                $q->join('formateur_stagiaire as fs', 'fs.stagiaire_id', '=', 'stagiaires.id')
                    ->where('fs.formateur_id', $formateurId);
            })
            ->when($partenaireId, function ($q) use ($partenaireId) {
                $q->where('stagiaires.partenaire_id', $partenaireId);
            })
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
        $filename = 'stagiaires_stats.xlsx';
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
    }

    private function countInactiveSince($threshold)
    {
        return DB::table('stagiaires')
            ->join('users', 'stagiaires.user_id', '=', 'users.id')
            ->leftJoin(DB::raw('(SELECT user_id, MAX(completed_at) as last_quiz_at FROM quiz_participations WHERE status = "completed" GROUP BY user_id) qp'), function ($q) {
                $q->on('qp.user_id', '=', 'users.id');
            })
            ->where(function ($q) use ($threshold) {
                $q->whereNull('qp.last_quiz_at')
                    ->orWhere('qp.last_quiz_at', '<', $threshold);
            })
            ->count();
    }
}
