<?php

namespace App\Support;

use App\Clients\BackendClient;

/**
 * Backend contract gap: `GET /journals` lives only under `role:Admin`
 * (see Backend/routes/modules/admin.php) — there is no journals listing
 * endpoint reachable by Author/Reviewer/Reader/public callers. Pages like
 * Author\CreateSubmission still need *some* way to populate a journal
 * select. This helper tries the admin endpoint first (works for
 * Editor/Admin sessions) and, if that 403s, falls back to harvesting
 * distinct journals from the public `GET /issues` and `GET /articles`
 * endpoints (each issue/article response nests a `journal` object) —
 * best-effort, so a journal with zero issues/articles published yet won't
 * appear. See final report for the suggested Backend fix (a public/
 * author-reachable `GET /journals` list).
 */
class JournalOptions
{
    /** @return array<int, array{id:int, title:string}> */
    public static function forSelect(BackendClient $backend): array
    {
        $response = $backend->get('/journals', ['per_page' => 100]);

        if ($response->successful()) {
            return collect($response->json('data') ?? [])
                ->map(fn ($j) => ['id' => $j['id'], 'title' => $j['title']])
                ->values()
                ->all();
        }

        $journals = collect();

        $issues = $backend->get('/issues', ['per_page' => 100]);
        if ($issues->successful()) {
            foreach ($issues->json('data') ?? [] as $issue) {
                if (isset($issue['journal']['id'])) {
                    $journals->put($issue['journal']['id'], [
                        'id' => $issue['journal']['id'],
                        'title' => $issue['journal']['title'],
                    ]);
                }
            }
        }

        $articles = $backend->get('/articles', ['per_page' => 100]);
        if ($articles->successful()) {
            foreach ($articles->json('data') ?? [] as $article) {
                $journal = $article['issue']['journal'] ?? null;
                if ($journal) {
                    $journals->put($journal['id'], ['id' => $journal['id'], 'title' => $journal['title']]);
                }
            }
        }

        return $journals->values()->all();
    }
}
