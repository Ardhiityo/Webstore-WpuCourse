<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Provinsi
        $provinces = Http::get('https://wilayah.id/api/provinces.json');

        foreach ($provinces->json('data') as $province) {

            $this->command->info('Seeding province: ' . data_get($province, 'name'));

            Region::create([
                'code' => data_get($province, 'code'),
                'name' => data_get($province, 'name'),
                'type' => 'province',
                'parent_code' => null,
            ]);

            // Kota/Kabupaten
            $regencies = Http::get("https://wilayah.id/api/regencies/{$province['code']}.json");

            foreach ($regencies->json('data') as $regency) {

                $this->command->info('Seeding regency: ' . data_get($regency, 'name'));

                Region::create([
                    'code' => data_get($regency, 'code'),
                    'name' => data_get($regency, 'name'),
                    'type' => 'regency',
                    'parent_code' => data_get($province, 'code')
                ]);

                // Kecamatan
                $districts = Http::get("https://wilayah.id/api/districts/{$regency['code']}.json");

                foreach ($districts->json('data') as $district) {

                    $this->command->info('Seeding district: ' . data_get($district, 'name'));

                    Region::create([
                        'code' => data_get($district, 'code'),
                        'name' => data_get($district, 'name'),
                        'type' => 'district',
                        'parent_code' => data_get($regency, 'code')
                    ]);

                    // Kelurahan
                    $villages = Http::get("https://wilayah.id/api/villages/{$district['code']}.json");

                    foreach ($villages->json('data') as $village) {

                        $this->command->info('Seeding village: ' . data_get($village, 'name'));

                        Region::create([
                            'code' => data_get($village, 'code'),
                            'name' => data_get($village, 'name'),
                            'type' => 'village',
                            'parent_code' => data_get($district, 'code')
                        ]);
                    }
                }
            }
        }
    }
}
