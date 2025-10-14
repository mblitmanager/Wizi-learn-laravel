<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminInactivityController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'quiz'); // quiz|platform
        $days = (int) $request->get('days', 7);
        $formateurId = $request->integer('formateur_id');
        $partenaireId = $request->integer('partenaire_id');
        $platform = $request->get('platform'); // android|ios|web|null

        $threshold = now()->subDays(in_array($days, [3, 7, 30]) ? $days : 7);

        // Base stagiaires
        $base = DB::table('stagiaires')
            ->join('users', 'stagiaires.user_id', '=', 'users.id')
            ->when($formateurId, function ($q) use ($formateurId) {
                $q->join('formateur_stagiaire as fs', 'fs.stagiaire_id', '=', 'stagiaires.id')
                    ->where('fs.formateur_id', $formateurId);
            })
            ->when($partenaireId, function ($q) use ($partenaireId) {
                $q->where('stagiaires.partenaire_id', $partenaireId);
            })
            ->leftJoin(DB::raw('(SELECT user_id, MAX(completed_at) as last_quiz_at FROM quiz_participations WHERE status = "completed" GROUP BY user_id) qp'), function ($q) {
                $q->on('qp.user_id', '=', 'users.id');
            })
            ->leftJoin(DB::raw('(SELECT stagiaire_id, MAX(watched_at) as last_video_at FROM media_stagiaire GROUP BY stagiaire_id) mv'), function ($q) {
                $q->on('mv.stagiaire_id', '=', 'stagiaires.id');
            })
            ->leftJoin('user_app_usages as uau_android', function ($q) {
                $q->on('uau_android.user_id', '=', 'users.id')->where('uau_android.platform', 'android');
            })
            ->leftJoin('user_app_usages as uau_ios', function ($q) {
                $q->on('uau_ios.user_id', '=', 'users.id')->where('uau_ios.platform', 'ios');
            })
            ->select(
                'stagiaires.id as stagiaire_id',
                'users.id as user_id',
                'users.name',
                'users.email',
                'users.last_login_at',
                'users.last_activity_at',
                DB::raw('qp.last_quiz_at'),
                DB::raw('mv.last_video_at'),
                DB::raw('CASE WHEN uau_android.user_id IS NOT NULL THEN 1 ELSE 0 END as has_android'),
                DB::raw('CASE WHEN uau_ios.user_id IS NOT NULL THEN 1 ELSE 0 END as has_ios')
            );

        if ($tab === 'quiz') {
            $base->where(function ($q) use ($threshold) {
                $q->whereNull('qp.last_quiz_at')->orWhere('qp.last_quiz_at', '<', $threshold);
            });
        } else {
            // platform: connectés sur une plateforme mais pas de quiz/vidéo récents
            if ($platform === 'android') {
                $base->whereNotNull('uau_android.user_id');
            } elseif ($platform === 'ios') {
                $base->whereNotNull('uau_ios.user_id');
            } elseif ($platform === 'web') {
                $base->whereNull('uau_android.user_id')->whereNull('uau_ios.user_id');
            }
            $base->where(function ($q) use ($threshold) {
                $q->where(function ($q2) use ($threshold) {
                    $q2->whereNull('qp.last_quiz_at')->orWhere('qp.last_quiz_at', '<', $threshold);
                })->where(function ($q3) use ($threshold) {
                    $q3->whereNull('mv.last_video_at')->orWhere('mv.last_video_at', '<', $threshold);
                });
            });
        }

        $stagiaires = $base->orderByDesc('users.last_login_at')->paginate(20)->withQueryString();

        $formateurs = DB::table('formateurs')->join('users', 'formateurs.user_id', '=', 'users.id')->select('formateurs.id', 'users.name')->orderBy('users.name')->get();
        $partenaires = DB::table('partenaires')->select('id', 'identifiant')->orderBy('identifiant')->get();

        return view('admin.inactivity.index', compact('stagiaires', 'tab', 'days', 'formateurId', 'partenaireId', 'platform', 'formateurs', 'partenaires'));
    }

    public function notify(Request $request)
    {
        $userIds = $request->input('user_ids', []);
        $message = $request->input('message');
        if (!is_array($userIds) || empty($userIds) || empty($message)) {
            return back()->with('error', 'Sélectionnez au moins un stagiaire et saisissez un message.');
        }

        $users = User::whereIn('id', $userIds)->get();
        foreach ($users as $user) {
            app(\App\Services\NotificationService::class)->sendFcmToUser(
                $user,
                'Relance activité',
                $message,
                ['type' => 'inactivity']
            );
        }

        return back()->with('success', 'Notifications envoyées.');
    }
}
