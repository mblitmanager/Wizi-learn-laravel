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

        // Utilisateurs connectés (sessions actives dans la dernière heure)
        $connectedUsers = User::whereIn('id', function ($query) {
            $query->select('user_id')
                ->from('sessions')
                ->where('last_activity', '>=', now()->subHour()->getTimestamp());
        })->get();

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
            'activeQuizzes'
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
        ]);

        $ip = $request->header('X-Client-IP') ?? $request->ip();

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = auth()->user(); // Maintenant connecté
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $ip,
                'is_online' => true,
                'last_activity_at' => now()
            ]);

            if ($user->role === 'administrateur') {
                return redirect()->route('dashboard');
            } else {
                Auth::logout(); // Déconnecter si ce n'est pas un admin
                return redirect()->route('login')->with('error', 'Access denied.');
            }
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }


    public function logout()
    {
        if (auth()->check()) {
            auth()->user()->update(['is_online' => false]);
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

        $resetLink = url('/reset-password/'.$token.'?email='.urlencode($user->email));
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
