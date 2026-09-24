<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Test de fumée : chaque page du back-office (GET sans paramètre) s'affiche
 * sans erreur serveur pour le rôle qui y a accès, avec le layout du thème.
 */
class BackOfficePagesRenderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /** Routes qui ne renvoient pas une page HTML (exports, redirections, actions) */
    private const IGNORED = [
        '#/export#', '#/download#', '#logout#', '#/import/template#', '#/reports#',
        '#/sample#', '#/template#', '#stream#', '#/check-#', '#/run#', '#/sync#', '#/oauth#', '#/google#',
    ];

    /**
     * Pages déjà en erreur avant le changement de thème, suivies dans docs/TODO_MAINTENANCE.md.
     * Retirer une entrée dès que la page est réparée : le test la vérifiera à nouveau.
     */
    private const KNOWN_BROKEN = [
        'administrateur/dashboard/activity-user',
        'administrateur/question',
        'administrateur/question/create',
        'administrateur/stats/par-formation',
        'administrateur/stats/par-formateur',
        'administrateur/stats/par-catalogue',
        'administrateur/stats/classement',
        'administrateur/stats/affluence',
        'formateur/dashboard',
        'formateur/stats/par-formation',
        'formateur/stats/classement',
        'formateur/stagiaires/stats',
        'commercial/dashboard',
        'commercial/stats/par-formation',
        'commercial/stats/classement',
    ];

    public static function roles(): array
    {
        return [
            'administrateur' => ['admin@wizi-learn.com', 'administrateur'],
            'formateur' => ['formateur1@wizi-learn.com', 'formateur'],
            'commercial' => ['commercial1@wizi-learn.com', 'commercial'],
        ];
    }

    #[DataProvider('roles')]
    public function test_back_office_pages_render(string $email, string $prefix): void
    {
        $user = User::where('email', $email)->firstOrFail();
        $failures = [];
        $checked = 0;

        foreach ($this->pagesFor($prefix) as $uri) {
            $response = $this->actingAs($user)->get('/' . $uri);
            $checked++;

            if ($response->getStatusCode() >= 500) {
                $message = $response->exception?->getMessage() ?? 'HTTP ' . $response->getStatusCode();
                $failures[] = "/{$uri} : " . mb_strimwidth($message, 0, 160, '…');
            } elseif ($response->isOk()
                && str_contains($response->headers->get('Content-Type', ''), 'text/html')
                && !str_contains($response->getContent(), 'wizi-theme.css')) {
                $failures[] = "/{$uri} : n'utilise pas le layout du thème";
            }
        }

        $this->assertGreaterThan(0, $checked);
        $this->assertSame([], $failures, "Pages en erreur pour {$prefix} :\n" . implode("\n", $failures));
    }

    private function pagesFor(string $prefix): array
    {
        $uris = [];
        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            $matchesRole = $prefix === 'administrateur'
                ? str_starts_with($uri, 'administrateur/') || in_array($uri, ['dashboard', 'dashboard/activity'], true)
                : str_starts_with($uri, $prefix . '/') || $uri === 'dashboard';

            if (!$matchesRole || !in_array('GET', $route->methods(), true) || str_contains($uri, '{')
                || in_array($uri, self::KNOWN_BROKEN, true)) {
                continue;
            }
            foreach (self::IGNORED as $pattern) {
                if (preg_match($pattern, '/' . $uri)) {
                    continue 2;
                }
            }
            $uris[] = $uri;
        }

        return array_values(array_unique($uris));
    }
}
