<?php

namespace Tests\Feature;

use App\Clients\BackendClient;
use Tests\TestCase;

/**
 * Smoke test: every GET route in routes/web.php renders without a server
 * error for the role(s) allowed to see it. Doesn't assert on page content —
 * just that mount()/render() don't throw. Requires Backend/API/Central-Service
 * running (see each app's .env for the local dev ports) and the seeded demo
 * users (see Central-Service/database/seeders/DatabaseSeeder.php).
 */
class RouteSmokeTest extends TestCase
{
    private function sessionFor(string $email): array
    {
        static $cache = [];

        if (isset($cache[$email])) {
            return $cache[$email];
        }

        $backend = new BackendClient;
        $response = $backend->post('/login', ['email' => $email, 'password' => 'password']);
        $this->assertTrue($response->successful(), "login failed for {$email}: ".$response->body());

        return $cache[$email] = [
            'backend_token' => $response->json('token'),
            'auth_user' => [
                'id' => $response->json('user.id'),
                'email' => $response->json('user.email'),
                'full_name' => $response->json('user.full_name'),
                'roles' => array_map(
                    fn ($r) => ['role_name' => $r['role_name'], 'journal_id' => $r['pivot']['journal_id'] ?? null],
                    $response->json('user.roles') ?? []
                ),
            ],
        ];
    }

    public function test_public_pages_render(): void
    {
        foreach (['/', '/browse-issues', '/access-denied', '/auth/login', '/auth/register'] as $path) {
            $this->get($path)->assertStatus(200);
        }
    }

    public function test_author_pages_render(): void
    {
        $session = $this->sessionFor('author@jpms.com');

        // /dashboard is a role router, not a page — it redirects to the
        // caller's primary-role dashboard (DashboardHomeController).
        $this->withSession($session)->get('/dashboard')->assertRedirect(route('author.dashboard'));

        foreach ([
            '/dashboard/profile', '/dashboard/reader',
            '/dashboard/author', '/dashboard/author/submissions', '/dashboard/author/submissions/create',
            '/dashboard/author/co-author-invitations', '/dashboard/author/co-authored-submissions',
            '/dashboard/author/metrics',
        ] as $path) {
            $this->withSession($session)->get($path)->assertStatus(200, "GET {$path} did not return 200");
        }
    }

    public function test_reviewer_pages_render(): void
    {
        $session = $this->sessionFor('reviewer@jpms.com');

        foreach (['/dashboard/reviewer', '/dashboard/reviewer/history'] as $path) {
            $this->withSession($session)->get($path)->assertStatus(200);
        }
    }

    public function test_editor_pages_render(): void
    {
        $session = $this->sessionFor('editor@jpms.com');

        foreach ([
            '/dashboard/editor', '/dashboard/editor/reviewers/search',
            '/dashboard/production', '/dashboard/production/issues', '/dashboard/production/issues/create',
            '/dashboard/analytics',
        ] as $path) {
            $this->withSession($session)->get($path)->assertStatus(200);
        }
    }

    public function test_admin_pages_render(): void
    {
        $session = $this->sessionFor('admin@jpms.com');

        foreach ([
            '/dashboard/admin', '/dashboard/admin/users', '/dashboard/admin/roles',
            '/dashboard/admin/journals', '/dashboard/admin/audit',
        ] as $path) {
            $this->withSession($session)->get($path)->assertStatus(200);
        }
    }

    public function test_role_gate_blocks_wrong_role(): void
    {
        $session = $this->sessionFor('author@jpms.com');

        $this->withSession($session)->get('/dashboard/admin/users')->assertRedirect(route('access-denied'));
    }

    public function test_unauthenticated_redirects_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('auth.login'));
    }
}
