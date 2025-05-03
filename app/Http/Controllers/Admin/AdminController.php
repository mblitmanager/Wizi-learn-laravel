<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commercial;
use App\Models\Formateur;
use App\Models\PoleRelationClient;
use App\Models\Stagiaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

        return view('admin.dashboard.index', compact(
            'totalStagiaires',
            'totalFormateurs',
            'totalCommerciaux',
            'totalPoleRelationClient',
            'dailyStats',
            'monthlyStats'
        ));
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

        if (Auth::attempt($request->only('email', 'password'))) {
            if (Auth::user()->role === 'administrateur') {
                return redirect()->route('dashboard');
            } else {
                Auth::logout(); // Cela déconnecte l'utilisateur si ce n'est pas un admin
                return redirect()->route('login')->with('error', 'Access denied.');
            }
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}
