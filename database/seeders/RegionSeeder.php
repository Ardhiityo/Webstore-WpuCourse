<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Province
        $provinces = Http::get('https://wilayah.id/api/provinces.json');
        foreach ($provinces->json('data') as $province) {
            $this->command->info('Processing provinces : ' . data_get($province, 'name'));
            Region::create([
                'code' => data_get($province, 'code'),
                'type' => 'province',
                'name' => data_get($province, 'name')
            ]);

            // Regencies
            $regencies = Http::get("https://wilayah.id/api/regencies/{$province['code']}.json");
            foreach ($regencies->json('data') as $regency) {
                $this->command->info('Processing regencies : ' . data_get($regency, 'name'));
                Region::create([
                    'code' => data_get($regency, 'code'),
                    'type' => 'regency',
                    'name' => data_get($regency, 'name'),
                    'parent_code' => data_get($province, 'code')
                ]);

                // Districts
                $districts = Http::get("https://wilayah.id/api/districts/{$regency['code']}.json");
                foreach ($districts->json('data') as $key => $district) {
                    $this->command->info('Processing districts : ' . data_get($district, 'name'));
                    Region::create([
                        'code' => data_get($district, 'code'),
                        'type' => 'district',
                        'name' => data_get($district, 'name'),
                        'parent_code' => data_get($regency, 'code')
                    ]);

                    // Villages
                    $villages = Http::get("https://wilayah.id/api/villages/{$district['code']}.json");
                    foreach ($villages->json('data') as $key => $village) {
                        $this->command->info('Processing villages : ' . data_get($village, 'name'));
                        Region::create([
                            'code' => data_get($village, 'code'),
                            'type' => 'village',
                            'name' => data_get($village, 'name'),
                            'parent_code' => data_get($district, 'code')
                        ]);
                    }
                }
            }
        }
    }
}
