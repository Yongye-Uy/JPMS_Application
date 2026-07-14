<?php

namespace App\Support;

use App\Clients\BackendClient;

/**
 * Backend contract gap: there is no `GET /review-invitations` (list-mine)
 * or `GET /review-invitations/{id}` (show) endpoint — see
 * Backend/routes/modules/reviews.php, which only exposes POST actions for
 * review-invitations plus `GET /reviews/{id}` for a *submitted* review.
 * A Reviewer therefore has no server-side way to discover which
 * manuscripts/invitations are theirs.
 *
 * Workaround used throughout the Reviewer.* pages: `GET /manuscripts/{id}`
 * has no role restriction (any authenticated user may fetch a manuscript
 * by id — see Backend/app/Http/Controllers/Manuscripts/ManuscriptController
 * ::show) and nests the full `review_invitations` array (each with its
 * `review` if submitted). We use a manuscript-id hint when the caller has
 * one (e.g. passed via a `?manuscript_id=` query string from a page that
 * already loaded the manuscript) and otherwise fall back to a small
 * bounded scan of manuscript ids. This is a pragmatic, read-only,
 * Frontend-only workaround — see the final report for the suggested
 * Backend fix (a reviewer-scoped listing endpoint).
 */
class ReviewInvitationLookup
{
    /** Upper bound for the fallback scan — fine for a demo dataset, not for production. */
    private const SCAN_LIMIT = 60;

    /** @return array{manuscript: array, invitation: array}|null */
    public static function findInvitation(BackendClient $backend, int $invitationId, ?int $hintManuscriptId = null): ?array
    {
        if ($hintManuscriptId) {
            $manuscript = $backend->get("/manuscripts/{$hintManuscriptId}");
            if ($manuscript->successful()) {
                foreach ($manuscript->json('review_invitations') ?? [] as $invitation) {
                    if ((int) $invitation['id'] === $invitationId) {
                        return ['manuscript' => $manuscript->json(), 'invitation' => $invitation];
                    }
                }
            }
        }

        for ($id = 1; $id <= self::SCAN_LIMIT; $id++) {
            if ($id === $hintManuscriptId) {
                continue;
            }
            $manuscript = $backend->get("/manuscripts/{$id}");
            if (! $manuscript->successful()) {
                continue;
            }
            foreach ($manuscript->json('review_invitations') ?? [] as $invitation) {
                if ((int) $invitation['id'] === $invitationId) {
                    return ['manuscript' => $manuscript->json(), 'invitation' => $invitation];
                }
            }
        }

        return null;
    }

    /** @return array{manuscript: array, invitation: array, review: array}|null */
    public static function findReview(BackendClient $backend, int $reviewId, ?int $hintManuscriptId = null): ?array
    {
        $search = function (array $manuscript) use ($reviewId) {
            foreach ($manuscript['review_invitations'] ?? [] as $invitation) {
                if (($invitation['review']['id'] ?? null) === $reviewId) {
                    return ['manuscript' => $manuscript, 'invitation' => $invitation, 'review' => $invitation['review']];
                }
            }

            return null;
        };

        if ($hintManuscriptId) {
            $manuscript = $backend->get("/manuscripts/{$hintManuscriptId}");
            if ($manuscript->successful() && $found = $search($manuscript->json())) {
                return $found;
            }
        }

        for ($id = 1; $id <= self::SCAN_LIMIT; $id++) {
            if ($id === $hintManuscriptId) {
                continue;
            }
            $manuscript = $backend->get("/manuscripts/{$id}");
            if (! $manuscript->successful()) {
                continue;
            }
            if ($found = $search($manuscript->json())) {
                return $found;
            }
        }

        return null;
    }

    /**
     * Best-effort "my invitations" list for ReviewerDashboard/ReviewHistory —
     * scans manuscripts 1..SCAN_LIMIT and collects invitations addressed to
     * $reviewerId. Returns each with its parent manuscript attached.
     *
     * @return array<int, array{manuscript: array, invitation: array}>
     */
    public static function myInvitations(BackendClient $backend, int $reviewerId): array
    {
        $found = [];

        for ($id = 1; $id <= self::SCAN_LIMIT; $id++) {
            $manuscript = $backend->get("/manuscripts/{$id}");
            if (! $manuscript->successful()) {
                continue;
            }
            $data = $manuscript->json();
            foreach ($data['review_invitations'] ?? [] as $invitation) {
                if ((int) $invitation['reviewer_id'] === $reviewerId) {
                    $found[] = ['manuscript' => $data, 'invitation' => $invitation];
                }
            }
        }

        return $found;
    }
}
