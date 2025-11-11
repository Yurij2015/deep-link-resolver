<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Battle;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class DeepLinkController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = (string) config('feed.deep_link_base_url', 'https://app.example.com');
    }

    public function show(string $id): JsonResponse
    {
        try {
            $cacheKey = "dl:b:{$id}";

            $ttl = (int) config('feed.deep_link_ttl', 60);

            $payload = Cache::remember($cacheKey, $ttl, function () use ($id) {
                $battle = Battle::find($id);

                if (! $battle) {
                    throw new \RuntimeException('not_found');
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

                return [
                    'battle' => [
                        'id' => (int) $battle->id,
                        'category' => $battle->category,
                        'status' => $battle->status,
                        'started_at' => $battle->started_at ? $battle->started_at->toISOString() : null,
                        'finished_at' => $battle->finished_at ? $battle->finished_at->toISOString() : null,
                        'canonical_url' => "{$this->baseUrl}/battles/{$battle->id}",
                    ],
                    'candidates' => $normalizedCandidates,
                    'scores' => $scores,
                    'status' => $battle->status,
                ];
            });

            return response()->json(array_merge(['ok' => true], $payload));
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'not_found') {
                return response()->json([
                    'ok' => false,
                    'error' => [
                        'code' => 'ERR_NOT_FOUND',
                        'message' => 'Battle not found.',
                    ],
                ], 404);
            }

            return response()->json([
                'ok' => false,
                'error' => [
                    'code' => 'ERR_INTERNAL',
                    'message' => 'Internal error.',
                ],
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => [
                    'code' => 'ERR_INTERNAL',
                    'message' => 'Internal error.',
                ],
            ], 500);
        }
    }
}
