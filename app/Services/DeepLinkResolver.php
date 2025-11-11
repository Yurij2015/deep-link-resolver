<?php

namespace App\Services;

use App\Models\Battle;
use Illuminate\Support\Facades\Cache;

class DeepLinkResolver
{
    public function resolveBattle(int $id, string $baseUrl): array
    {
        $cacheKey = "dl:b:{$id}";

        $ttl = (int) config('feed.deep_link_ttl', 60);

        return Cache::remember($cacheKey, $ttl, static function () use ($id, $baseUrl) {
            $battle = Battle::find($id);

            if (! $battle) {
                throw new \RuntimeException('BATTLE_NOT_FOUND');
            }

            $candidates = $battle->candidates ?? [];

            $normalizedCandidates = array_map(static function ($c) {
                return [
                    'id' => isset($c['id']) ? (int) $c['id'] : null,
                    'name' => $c['name'] ?? null,
                    'country' => $c['country'] ?? null,
                    'score' => isset($c['score']) ? (int) $c['score'] : 0,
                ];
            }, is_array($candidates) ? $candidates : []);

            $scores = [];
            foreach ($normalizedCandidates as $candidate) {
                if ($candidate['id'] !== null) {
                    $scores[$candidate['id']] = $candidate['score'];
                }
            }

            $status = $battle->finished_at ? 'closed' : 'open';

            return [
                'battle' => [
                    'id' => (int) $battle->id,
                    'category' => $battle->category,
                    'status' => $status,
                    'started_at' => $battle->started_at ? $battle->started_at->toIso8601String() : null,
                    'finished_at' => $battle->finished_at ? $battle->finished_at->toIso8601String() : null,
                    'canonical_url' => "{$baseUrl}/b/{$battle->id}",
                ],
                'candidates' => $normalizedCandidates,
                'scores' => $scores,
                'status' => $status,
            ];
        });
    }
}

