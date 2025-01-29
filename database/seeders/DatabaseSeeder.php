<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\Ura;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            '12345678' => [
                'https://test1.example.org',
            ],
            '87654321' => [
                'https://test2.example.org',
            ],
            '12348765' => [
                'https://test3.example.org',
            ],
            '87651234' => [
                'https://test4.example.org',
            ]
        ];

        foreach ($data as $ura => $endpoints) {
            $ura = Ura::create(['ura' => $ura]);
            foreach ($endpoints as $endpoint) {
                $ura->suppliers()->create(['endpoint' => $endpoint]);
            }
            $ura->save();
        }
    }
}
