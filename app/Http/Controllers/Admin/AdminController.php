<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\Commercial;
use App\Models\Formateur;
use App\Models\LoginHistories;
use App\Models\PoleRelationClient;
use App\Models\Stagiaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\UserAppUsage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
    {
        $totalStagiaires = Stagiaire::count();
        $totalFormateurs = Formateur::count();
        $totalCommerciaux = Commercial::count();
        $totalPoleRelationClient = PoleRelationClient::count();

        // Statistiques quotidiennes des quiz joués
        $dailyStats = DB::table('quiz_participations')
            ->join('quizzes', 'quiz_participations.quiz_id', '=', 'quizzes.id')
            ->select(
                DB::raw('DATE(quiz_participations.completed_at) as date'),
                'quiz_participations.quiz_id',
                'quizzes.titre',
                DB::raw('COUNT(*) as total')
            )
            ->where('quiz_participations.status', 'completed')
            ->whereIn('quiz_participations.quiz_id', function ($query) {
                $query->select('quiz_id')->from('classements');
            })
            ->groupBy('date', 'quiz_participations.quiz_id', 'quizzes.titre')
            ->orderBy('date', 'desc')
            ->get();

        // Statistiques mensuelles des quiz joués
        $monthlyStats = DB::table('quiz_participations')
            ->join('quizzes', 'quiz_participations.quiz_id', '=', 'quizzes.id')
            ->select(
                DB::raw("DATE_FORMAT(quiz_participations.completed_at, '%Y-%m') as month"),
                'quiz_participations.quiz_id',
                'quizzes.titre',
                DB::raw('COUNT(*) as total')
            )
            ->where('quiz_participations.status', 'completed')
            ->whereIn('quiz_participations.quiz_id', function ($query) {
                $query->select('quiz_id')->from('classements');
            })
            ->groupBy('month', 'quiz_participations.quiz_id', 'quizzes.titre')
            ->orderBy('month', 'desc')
            ->get();

        // Récupération des listes pour les filtres
        $formateurs = Formateur::with('user')->get();
        $commerciaux = Commercial::with('user')->get();
        $poles = PoleRelationClient::with('user')->get();

        // Utilisateurs connectés
        $connectedUsers = User::where('is_online', true)
            ->leftJoin('user_app_usages', 'users.id', '=', 'user_app_usages.user_id')
            ->select('users.name', 'users.role', 'user_app_usages.platform')
            ->get();

        // Quiz récemment joués (10 derniers terminés)
        $recentQuizzes = DB::table('quiz_participations')
            ->join('quizzes', 'quiz_participations.quiz_id', '=', 'quizzes.id')
            ->join('users', 'quiz_participations.user_id', '=', 'users.id')
            ->select(
                'quiz_participations.completed_at',
                'quizzes.titre as quiz_title',
                'users.name as user_name'
            )
            ->where('quiz_participations.status', 'completed')
            ->orderByDesc('quiz_participations.completed_at')
            ->limit(10)
            ->get();

        // Quiz en cours (participations non terminées)
        $activeQuizzes = DB::table('quiz_participations')
            ->join('quizzes', 'quiz_participations.quiz_id', '=', 'quizzes.id')
            ->join('users', 'quiz_participations.user_id', '=', 'users.id')
            ->select(
                'quiz_participations.started_at',
                'quizzes.titre as quiz_title',
                'users.name as user_name'
            )
            ->where('quiz_participations.status', 'in_progress')
            ->orderByDesc('quiz_participations.started_at')
            ->get();

        // Récapitulatif usages Android/iOS
        $usageSummary = UserAppUsage::select(
            'platform',
            DB::raw('COUNT(*) as users'),
            DB::raw('SUM(CASE WHEN first_used_at IS NOT NULL THEN 1 ELSE 0 END) as first_uses'),
            DB::raw('SUM(CASE WHEN last_used_at >= NOW() - INTERVAL 30 DAY THEN 1 ELSE 0 END) as active_30d')
        )
            ->groupBy('platform')
            ->get()
            ->keyBy('platform');

        $androidUsers = (int) optional($usageSummary->get('android'))->users;
        $androidFirstUses = (int) optional($usageSummary->get('android'))->first_uses;
        $androidActive30d = (int) optional($usageSummary->get('android'))->active_30d;
        $iosUsers = (int) optional($usageSummary->get('ios'))->users;
        $iosFirstUses = (int) optional($usageSummary->get('ios'))->first_uses;
        $iosActive30d = (int) optional($usageSummary->get('ios'))->active_30d;


        return view('admin.dashboard.index', compact(
            'totalStagiaires',
            'totalFormateurs',
            'totalCommerciaux',
            'totalPoleRelationClient',
            'dailyStats',
            'monthlyStats',
            'formateurs',
            'commerciaux',
            'poles',
            'connectedUsers',
            'recentQuizzes',
            'activeQuizzes',
            'androidUsers',
            'androidFirstUses',
            'androidActive30d',
            'iosUsers',
            'iosFirstUses',
            'iosActive30d'
        ));
    }

    public function getUserActivity()
    {
        $onlineUsers = User::where('is_online', true)
            ->with(['stagiaire', 'commercial', 'formateur', 'poleRelationClient'])
            ->orderBy('last_activity_at', 'desc')
            ->get();

        $recentlyOnlineUsers = User::where('last_activity_at', '>=', now()->subHours(24))
            ->where('is_online', false)
            ->with(['stagiaire', 'commercial', 'formateur', 'poleRelationClient'])
            ->orderBy('last_activity_at', 'desc')
            ->get();

        // Statistiques de connexion
        $loginStats = [
            'today' => User::whereDate('last_login_at', today())->count(),
            'this_week' => User::whereBetween('last_login_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => User::whereBetween('last_login_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];
        return view('admin.dashboard.activity', compact(
            'onlineUsers',
            'recentlyOnlineUsers',
            'loginStats',
        ));
    }


    public function showLoginStats()
    {
        // Statistiques par pays
        $countriesData = LoginHistories::select([
            'country',
            DB::raw('count(*) as total'),
            DB::raw('MAX(login_at) as last_activity')
        ])
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderByDesc('total')
            ->get();

        $mapData = $countriesData->map(function ($item) {
            return [
                'country' => $item->country,
                'value' => $item->total,
                'last_activity' => $item->last_activity,
                'code' => $this->getCountryCode($item->country)
            ];
        });

        // Utilisateurs en ligne
        $onlineUsers = User::where('is_online', true)
            ->with(['stagiaire', 'commercial', 'formateur', 'poleRelationClient'])
            ->orderBy('last_activity_at', 'desc')
            ->get();

        // Statistiques générales
        $stats = [
            'map_data' => $mapData,
            'online_users' => User::where('is_online', true)->count(),
            'total_logins' => LoginHistories::where('country', '!=', null)->count(),
            'today_logins' => User::whereDate('last_login_at', today())->count(),
            'week_logins' => User::whereBetween('last_login_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month_logins' => User::whereBetween('last_login_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];

        return view('admin.dashboard.activity', compact('stats', 'onlineUsers'));
    }


    public function activityData()
    {
        // Statistiques par pays
        $countriesData = LoginHistories::select([
            'country',
            DB::raw('count(*) as total'),
            DB::raw('MAX(login_at) as last_activity')
        ])
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderByDesc('total')
            ->get();

        $mapData = $countriesData->map(function ($item) {
            return [
                'country' => $item->country,
                'value' => $item->total,
                'last_activity' => $item->last_activity,
                'code' => $this->getCountryCode($item->country)
            ];
        })->values();

        $onlineUsers = User::where('is_online', true)
            ->leftJoin('user_app_usages', 'users.id', '=', 'user_app_usages.user_id')
            ->orderBy('users.last_activity_at', 'desc')
            ->select('users.id', 'users.name', 'users.email', 'users.role', 'users.last_activity_at', 'users.is_online', 'users.last_login_at', 'user_app_usages.platform')
            ->get();

        $stats = [
            'map_data' => $mapData,
            'online_users' => User::where('is_online', true)->count(),
            'total_logins' => LoginHistories::where('country', '!=', null)->count(),
            'today_logins' => User::whereDate('last_login_at', today())->count(),
            'week_logins' => User::whereBetween('last_login_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month_logins' => User::whereBetween('last_login_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];

        return response()->json([
            'stats' => $stats,
            'onlineUsers' => $onlineUsers,
        ]);
    }

    // Helper pour obtenir les codes pays ISO
    private function getCountryCode($countryName)
    {
        $countries = [
            'France' => 'FR',
            'United States' => 'US',
            'Germany' => 'DE',
            'United Kingdom' => 'GB',
            'Spain' => 'ES',
            'Italy' => 'IT',
            'Canada' => 'CA',
            'Australia' => 'AU',
            'Netherlands' => 'NL',
            'Belgium' => 'BE',
            'Switzerland' => 'CH',
            'Sweden' => 'SE',
            'Norway' => 'NO',
            'Denmark' => 'DK',
            'Finland' => 'FI',
            'Poland' => 'PL',
            'Portugal' => 'PT',
            'Austria' => 'AT',
            'Ireland' => 'IE',
            'Russia' => 'RU',
            'China' => 'CN',
            'Japan' => 'JP',
            'India' => 'IN',
            'Brazil' => 'BR',
            'Mexico' => 'MX',
            'South Africa' => 'ZA',
            'South Korea' => 'KR',
            'Turkey' => 'TR',
            'Argentina' => 'AR',
            'New Zealand' => 'NZ',
            'Greece' => 'GR',
            'Czech Republic' => 'CZ',
            'Hungary' => 'HU',
            'Romania' => 'RO',
            'Madagascar' => 'MG',
            'Algeria' => 'DZ',
            'Tunisia' => 'TN',
            'Morocco' => 'MA',
            'Egypt' => 'EG',
            'United Arab Emirates' => 'AE',
            'Saudi Arabia' => 'SA',
        ];

        return $countries[$countryName] ?? strtoupper(substr($countryName, 0, 2));
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard'); // Redirige vers le tableau de bord si déjà connecté
        }
        return view('admin.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'email.max' => 'L\'email ne peut pas dépasser 255 caractères.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'administrateur',
        ]);

        return redirect()->route('login')->with('success', 'Registration successful. Please login.');
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard'); // Redirige vers le tableau de bord si déjà connecté
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email' => 'L\'adresse email est obligatoire.',
            'password' => 'Le mot de passe est obligatoire.',
        ]);

        $ip = $request->header('X-Client-IP') ?? $request->ip();

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $user->last_login_at = now();
            $user->last_login_ip = $ip;
            $user->is_online = true;
            $user->last_activity_at = now();
            $user->save();

            // Rediriger vers la route dashboard principale
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'Identifiants invalides.']);
    }


    public function logout()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $user->is_online = false;
                $user->save();
            }
            Auth::logout();
        }
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    public function showForgotPasswordForm()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email non trouvé']);
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $resetLink = url('/reset-password/' . $token . '?email=' . urlencode($user->email));
        Mail::to($user->email)->send(new PasswordResetMail($resetLink));

        return back()->with('status', 'Un lien de réinitialisation a été envoyé à votre adresse email');
    }

    public function showResetForm(Request $request, $token)
    {
        return view('admin.auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Token invalide ou expiré']);
        }

        if (now()->subMinutes(60)->gt($record->created_at)) {
            return back()->withErrors(['email' => 'Token expiré']);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/login')->with('status', 'Votre mot de passe a été réinitialisé avec succès');
    }
}
