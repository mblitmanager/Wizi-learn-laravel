# Maintenance — chantiers restants

Suite à l'audit de septembre 2026 (sécurisation API Platform, routes cassées, seeders).

- [ ] **Réparer les 4 tests en échec** — `ExampleTest`, `QuizControllerTest` (×2), `SendScheduledNotificationsTest`. C'est le filet de sécurité à avoir avant tout gros refactor.
- [ ] **Formater avec Pint** — 277 fichiers non conformes. À faire dans un commit séparé (diff purement cosmétique).
- [ ] **Découper les très gros contrôleurs** — `QuizController` (≈1 900 lignes), `Admin/QuizController` (≈1 600), `FormateurController` (≈1 200) → services + FormRequests.
- [ ] **Contrôles de rôle trompeurs** — dans `Stagiaire/ContactController`, `Stagiaire/RankingController`, `Stagiaire/FormationStagiaireController`, `Stagiaire/QuizStagiaireController`, les tests `role != 'formateur' && role != 'admin'` veulent en fait dire « doit être stagiaire ». Laissés tels quels : les passer sur `User::isAdmin()` ferait planter les admins en 500 (`$user->stagiaire->id` sur null).
- [ ] **Code mort dans `ParrainageService`** — méthodes de l'ancien parrainage (`acceptParrainage`, `getParrainageRewards`, `getParrainageHistory`, `generateParrainageLink`…), plus appelées depuis la refonte par token (commit `8038652`).
