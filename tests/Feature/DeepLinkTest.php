<?php

use App\Models\Battle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

uses( RefreshDatabase::class);
beforeEach(function () {
    Config::set('feed.deep_link_ttl', 60);
    Config::set('feed.deep_link_base_url', 'https://app.example.com'); // deterministic canonical_url
    Cache::flush();
});
it('valid_battle_returns_payload', function () {
    Battle::factory()->create([
        'id' => 123,
        'category' => 'miss_fit',
        'started_at' => now(),
        'finished_at' => null,
        'candidates' => [
            ['id' => 10, 'name' => 'Akiko8', 'country' => 'JP', 'score' => 42],
            ['id' => 77, 'name' => 'Sara', 'country' => 'FR', 'score' => 38],
        ],
    ]);
    $base = config('feed.deep_link_base_url');
    $this->getJson('/api/v1/b/123')
        ->assertStatus(200)
        ->assertJsonPath('ok', true)
        ->assertJsonPath('battle.canonical_url', $base.'/b/123')
        ->assertJsonPath('status', 'open');
});
it('closed_battle_resolves_final_state', function () {
    Battle::factory()->create([
        'id' => 200,
        'category' => 'miss_fit',
        'started_at' => now()->subHour(),

        'finished_at' => now(),
        'candidates' => [
            ['id' => 1, 'name' => 'Alpha', 'country' => 'US', 'score' => 100],
        ],
    ]);
    $this->getJson('/api/v1/b/200')
        ->assertStatus(200)
        ->assertJsonPath('ok', true)
        ->assertJsonPath('status', 'closed')
        ->assertJson(fn($json) => $json->whereType('battle.finished_at', 'string')->etc());
});
it('missing_returns_404', function () {
    $this->getJson('/api/v1/b/999999')
        ->assertStatus(404)
        ->assertExactJson([
            'ok' => false,
            'error' => 'BATTLE_NOT_FOUND',
        ]);
});
it('respects_cache_ttl', function () {
    $b = Battle::factory()->create([
        'id' => 300,
        'category' => 'miss_fit',
        'started_at' => now(),
        'finished_at' => null,
    ]);
    $first = $this->getJson('/api/v1/b/300')->assertStatus(200)->json();
// change DB; should not reflect until TTL expires
    $b->update(['category' => 'changed_in_db']);
    $second = $this->getJson('/api/v1/b/300')->assertStatus(200)->json();
    expect($first['battle']['category'])->toBe($second['battle']['category']);
});
