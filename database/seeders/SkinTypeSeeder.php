<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SkinType;
use Illuminate\Support\Str;

class SkinTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skinTypes = [
            'Normal' => 'Kulit normal dengan sebum seimbang',
            'Oily' => 'Kulit berminyak dengan produksi sebum berlebih',
            'Dry' => 'Kulit kering yang membutuhkan hidrasi ekstra',
            'Combination' => 'Kulit kombinasi dengan area berminyak dan kering',
            'Sensitive' => 'Kulit sensitif yang mudah iritasi',
        ];

        foreach ($skinTypes as $name => $description) {
            SkinType::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $description,
            ]);
        }
    }
}
