@extends('layouts.admin')
@section('title', 'Guide Interactif Administrateur')
@section('content')
    <div class="flex h-screen">
        <aside class="w-64 bg-[#FFF8E1] p-4 flex flex-col shrink-0">
            <h1 class="text-2xl font-bold text-[#000000] mb-8">Wizi Learn Admin</h1>
            <nav class="flex flex-col space-y-2">
                <a href="#dashboard" class="nav-link active text-lg font-medium p-3 rounded-lg flex items-center gap-3"><span>📊</span> Tableau de bord</a>
                <a href="#trainees" class="nav-link text-lg font-medium p-3 rounded-lg flex items-center gap-3"><span>👥</span> Gestion Stagiaires</a>
                <a href="#trainers" class="nav-link text-lg font-medium p-3 rounded-lg flex items-center gap-3"><span>👨‍🏫</span> Gestion Formateurs</a>
                <a href="#contacts" class="nav-link text-lg font-medium p-3 rounded-lg flex items-center gap-3"><span>📞</span> Gestion Contacts</a>
                <a href="#formations" class="nav-link text-lg font-medium p-3 rounded-lg flex items-center gap-3"><span>📚</span> Gestion Formations</a>
                <a href="#quizzes" class="nav-link text-lg font-medium p-3 rounded-lg flex items-center gap-3"><span>❓</span> Gestion Quiz</a>
                <a href="#questions" class="nav-link text-lg font-medium p-3 rounded-lg flex items-center gap-3"><span>📝</span> Gestion Questions</a>
                <a href="#ranking" class="nav-link text-lg font-medium p-3 rounded-lg flex items-center gap-3"><span>🏆</span> Classement & Points</a>
                <a href="#statistics" class="nav-link text-lg font-medium p-3 rounded-lg flex items-center gap-3"><span>📈</span> Réinitialisation Statistiques</a>
                <a href="#media" class="nav-link text-lg font-medium p-3 rounded-lg flex items-center gap-3"><span>🖼️</span> Gestion Médias</a>
            </nav>
            <div class="mt-auto p-2 text-center text-xs text-[#4a4a4a]">
                <p>Manuel Interactif v1.0</p>
                <p>&copy; AOPIA 2025</p>
            </div>
        </aside>
        <main class="flex-1 p-6 lg:p-10 overflow-y-auto">
            @include('admin.manual.sections')
        </main>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.0/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            const contentSections = document.querySelectorAll('.content-section');
            function updateContent(hash) {
                const targetHash = hash || '#dashboard';
                navLinks.forEach(link => {
                    link.classList.toggle('active', link.hash === targetHash);
                });
                contentSections.forEach(section => {
                    section.classList.toggle('active', section.id === targetHash.substring(1));
                });
            }
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    history.pushState(null, '', this.hash);
                    updateContent(this.hash);
                });
            });
            window.addEventListener('popstate', () => {
                updateContent(window.location.hash);
            });
            updateContent(window.location.hash);
        });
    </script>
@endsection
