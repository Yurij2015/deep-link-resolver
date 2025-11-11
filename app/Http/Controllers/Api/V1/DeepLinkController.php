<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DeepLinkResolver;
use Illuminate\Http\JsonResponse;

class DeepLinkController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = (string) config('feed.deep_link_base_url', url(''));
    }

    public function show(string $id, DeepLinkResolver $resolver): JsonResponse
    {
        try {
            $payload = $resolver->resolveBattle((int) $id, $this->baseUrl);

            return response()->json(['ok' => true] + $payload);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'BATTLE_NOT_FOUND') {
                return response()->json(['ok' => false, 'error' => 'BATTLE_NOT_FOUND'], 404);
            }

            return response()->json(['ok' => false, 'error' => 'UNKNOWN_ERROR'], 500);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => 'UNKNOWN_ERROR'], 500);
        }
    }
}
