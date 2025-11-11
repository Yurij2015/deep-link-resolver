<?php

namespace Database\Seeders;

use App\Models\Battle;
use Illuminate\Database\Seeder;

class BattleSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'id' => 123,
                'category' => 'miss_fit',
                'status' => 'open',
                'started_at' => '2025-11-09T12:00:00Z',
                'finished_at' => null,
                'candidates' => [
                    ['id' => 10, 'name' => 'Akiko', 'country' => 'JP', 'score' => 42],
                    ['id' => 77, 'name' => 'Sara', 'country' => 'FR', 'score' => 38],
                ],
            ],

            [
                'id' => 200,
                'category' => 'miss_fit',
                'status' => 'closed',
                'started_at' => '2025-11-09T10:00:00Z',
                'finished_at' => '2025-11-09T13:00:00Z',
                'candidates' => [
                    ['id' => 1, 'name' => 'Alpha', 'country' => 'US', 'score' => 100],
                    ['id' => 2, 'name' => 'Beta', 'country' => 'GB', 'score' => 80],
                ],
            ],

            [
                'id' => 300,
                'category' => 'casual',
                'status' => 'open',
                'started_at' => '2025-11-10T08:00:00Z',
                'finished_at' => null,
                'candidates' => [
                    ['id' => 20, 'name' => 'Lina', 'country' => 'SE', 'score' => 15],
                ],
            ],

            [
                'id' => 400,
                'category' => 'empty_case',
                'status' => 'open',
                'started_at' => '2025-01-01T00:00:00Z',
                'finished_at' => null,
                'candidates' => [],
            ],

            [
                'id' => 401,
                'category' => 'null_case',
                'status' => 'open',
                'started_at' => '2025-06-01T00:00:00Z',
                'finished_at' => null,
                'candidates' => [],
            ],

            [
                'id' => 500,
                'category' => 'large',
                'status' => 'closed',
                'started_at' => '2025-09-01T00:00:00Z',
                'finished_at' => '2025-09-02T00:00:00Z',
                'candidates' => array_map(static function ($i) {
                    return ['id' => $i, 'name' => "Cand{$i}", 'country' => 'US', 'score' => $i * 3];
                }, range(1, 25)),
            ],

            [
                'id' => 600,
                'category' => 'future',
                'status' => 'open',
                'started_at' => '2026-01-01T00:00:00Z',
                'finished_at' => null,
                'candidates' => [
                    ['id' => 9001, 'name' => 'FutureOne', 'country' => 'RU', 'score' => 0],
                ],
            ],

            [
                'id' => 700,
                'category' => 'archive',
                'status' => 'closed',
                'started_at' => '2020-01-01T00:00:00Z',
                'finished_at' => '2020-01-02T00:00:00Z',
                'candidates' => [
                    ['id' => 55, 'name' => 'OldOne', 'country' => 'DE', 'score' => 5],
                ],
            ],

            [
                'id' => 800,
                'category' => 'special_category',
                'status' => 'open',
                'started_at' => '2025-11-01T00:00:00Z',
                'finished_at' => null,
                'candidates' => [
                    ['id' => 101, 'name' => 'Zed', 'country' => 'JP', 'score' => 12],
                ],
            ],
        ];

        Battle::unguard();

        foreach ($samples as $row) {
            Battle::updateOrCreate(['id' => $row['id']], $row);
        }

        Battle::reguard();
    }
}
