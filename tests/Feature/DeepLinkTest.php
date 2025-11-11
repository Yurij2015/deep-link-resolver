<?php

namespace Tests\Feature;

use App\Models\Battle;
use Illuminate\Support\Facades\Cache;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeepLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_battle_returns_payload(): void
    {
        Battle::factory()->create([
            'id' => 123,
            'category' => 'miss_fit',
            'status' => 'open',
            'started_at' => now(),
            'finished_at' => null,
            'candidates' => [
                ['id' => 10, 'name' => 'Akiko8', 'country' => 'JP', 'score' => 42],
                ['id' => 77, 'name' => 'Sara', 'country' => 'FR', 'score' => 38],
            ],
        ]);

        $base = config('feed.deep_link_base_url', 'https://app.example.com');

        $response = $this->getJson('/api/v1/b/123');

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($base) {
                $json->where('ok', true)
                    ->has('battle')
                    ->has('candidates', 2)
                    ->has('scores')
                    ->where('status', 'open')
                    ->where('battle.canonical_url', $base.'/battles/123')
                    ->etc();
            });
    }

    public function test_closed_battle_resolves_final_state(): void
    {
        Battle::factory()->create([
            'id' => 200,
            'status' => 'closed',
            'finished_at' => now(),
            'candidates' => [
                ['id' => 1, 'name' => 'Alpha', 'country' => 'US', 'score' => 100],
            ],
        ]);

        $response = $this->getJson('/api/v1/b/200');

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->where('ok', true)
                    ->where('status', 'closed')
                    ->whereType('battle.finished_at', 'string')
                    ->etc();
            });
    }

    public function test_missing_returns_404(): void
    {
        $response = $this->getJson('/api/v1/b/9999');

        $response->assertStatus(404)
            ->assertJson([
                'ok' => false,
                'error' => [
                    'code' => 'ERR_NOT_FOUND',
                ],
            ]);
    }

    public function test_respects_cache_ttl(): void
    {
        Cache::flush();

        Battle::factory()->create([
            'id' => 300,
            'status' => 'open',
            'candidates' => [
                ['id' => 20, 'name' => 'Lina', 'country' => 'SE', 'score' => 15],
            ],
        ]);

        $this->getJson('/api/v1/b/300')->assertStatus(200);
        $this->assertTrue(Cache::has('dl:b:300'));
        Cache::flush();
        $this->getJson('/api/v1/b/300')->assertStatus(200);
    }
}
