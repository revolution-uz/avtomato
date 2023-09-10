<?php

namespace Database\Seeders\Company;

use App\Models\Company\District;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        if (!File::exists(database_path('seeders/data/regions.csv'))
            || !File::exists(database_path('seeders/data/districts.csv'))
            || !File::exists(database_path('seeders/data/mahallas.csv'))) {
            $this->regenerateCsvFiles();
        }

        $this->createOrUpdateRegions();
        $this->createOrUpdateDistricts();
        $this->createOrUpdateMahallas();
    }

    private function createOrUpdateRegions(): void
    {
        if (($handle = fopen(database_path('seeders/data/regions.csv'), "r")) !== FALSE) {
            while (($row = fgetcsv($handle)) !== FALSE) {
                Region::query()->updateOrCreate(
                    ['oilakredit_id' => $row[0]],
                    [
                        'name_uz' => str_replace(' ', ' ', $row[1]),
                        'name_oz' => str_replace(' ', ' ', $row[2]),
                        'name_ru' => str_replace(' ', ' ', $row[3]),
                        'name_en' => str_replace(' ', ' ', $row[1]),
                    ],
                );
            }
            fclose($handle);
        }
    }

    private function createOrUpdateDistricts(): void
    {
        $regionsMap = Region::query()->pluck('id', 'oilakredit_id')->toArray();
        if (($handle = fopen(database_path('seeders/data/districts.csv'), "r")) !== FALSE) {
            while (($row = fgetcsv($handle)) !== FALSE) {
                District::query()->updateOrCreate(
                    ['oilakredit_id' => $row[0]],
                    [
                        'region_id' => $regionsMap[$row[1]],
                        'name_uz' => str_replace(' ', ' ', $row[2]),
                        'name_oz' => str_replace(' ', ' ', $row[3]),
                        'name_ru' => str_replace(' ', ' ', $row[4]),
                        'name_en' => str_replace(' ', ' ', $row[2]),
                    ],
                );
            }
            fclose($handle);
        }
    }

    private function createOrUpdateMahallas(): void
    {
        $districtsMap = District::query()->pluck('id', 'oilakredit_id')->toArray();
        if (($handle = fopen(database_path('seeders/data/mahallas.csv'), "r")) !== FALSE) {
            while (($row = fgetcsv($handle)) !== FALSE) {
                Mahalla::query()->updateOrCreate(
                    ['oilakredit_id' => $row[0]],
                    [
                        'district_id' => $districtsMap[$row[1]],
                        'name_uz' => str_replace(' ', ' ', $row[2]),
                        'name_oz' => str_replace(' ', ' ', $row[3]),
                        'name_ru' => str_replace(' ', ' ', $row[4]),
                        'name_en' => str_replace(' ', ' ', $row[2]),
                    ],
                );
            }
            fclose($handle);
        }
    }

    private function regenerateCsvFiles(): void
    {
        set_time_limit(0);
        $regionsList = [];
        $districtsList = [];
        foreach (['uz' => 'oz', 'la' => 'uz', 'ru' => 'ru'] as $remoteLanguage => $localLanguage) {
            $response = Http::withHeaders([
                'Accept' => 'application/json, text/plain, */*',
                'Accept-Language' => $remoteLanguage,
                'Connection' => 'keep-alive',
                'Origin' => 'https://oilakredit.uz',
                'Referer' => 'https://oilakredit.uz/',
                'Sec-Fetch-Dest' => 'empty',
                'Sec-Fetch-Mode' => 'cors',
                'Sec-Fetch-Site' => 'same-site',
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
                'sec-ch-ua' => '"Not/A)Brand";v="99", "Google Chrome";v="115", "Chromium";v="115"',
                'sec-ch-ua-mobile' => '?0',
                'sec-ch-ua-platform' => '"macOS"',
            ])->get('https://api.oilakredit.uz/web/v1/public/forms/application', ['locale' => $remoteLanguage])->json();

            foreach ($response['elements'][6]['children'][0]['data'] as $region) {
                $regionsList[$region['id']] = $regionsList[$region['id']] ?? [];
                $regionsList[$region['id']]['id'] = $region['id'];
                $regionsList[$region['id']]['name_' . $localLanguage] = $region['full_name'];
            }

            foreach ($response['elements'][6]['children'][1]['data'] as $district) {
                $districtsList[$district['id']] = $districtsList[$district['id']] ?? [];
                $districtsList[$district['id']]['id'] = $district['id'];
                $districtsList[$district['id']]['region_id'] = $district['region_id'];
                $districtsList[$district['id']]['name_' . $localLanguage] = $district['full_name'];
            }
        }

        $regionsFile = fopen(database_path("seeders/data/regions.csv"), 'w');
        foreach ($regionsList as $_ => $region) {
            fputcsv($regionsFile, [$region['id'], $region['name_uz'], $region['name_oz'], $region['name_ru']]);
        }

        $districtsFile = fopen(database_path("seeders/data/districts.csv"), 'w');
        foreach ($districtsList as $_ => $district) {
            fputcsv($districtsFile, [$district['id'], $district['region_id'], $district['name_uz'], $district['name_oz'], $district['name_ru']]);
        }

        $mahallasList = [];
        foreach (array_keys($districtsList) as $districtId) {
            foreach (['uz' => 'oz', 'la' => 'uz', 'ru' => 'ru'] as $remoteLanguage => $localLanguage) {
                $response = Http::withHeaders([
                    'Accept' => 'application/json, text/plain, */*',
                    'Accept-Language' => $remoteLanguage,
                    'Connection' => 'keep-alive',
                    'Origin' => 'https://oilakredit.uz',
                    'Referer' => 'https://oilakredit.uz/',
                    'Sec-Fetch-Dest' => 'empty',
                    'Sec-Fetch-Mode' => 'cors',
                    'Sec-Fetch-Site' => 'same-site',
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
                    'sec-ch-ua' => '"Not/A)Brand";v="99", "Google Chrome";v="115", "Chromium";v="115"',
                    'sec-ch-ua-mobile' => '?0',
                    'sec-ch-ua-platform' => '"macOS"'
                ])->get('https://api.oilakredit.uz/api/v1/public/lists/mahalla', ['locale' => $remoteLanguage, 'parent_id' => $districtId])->json();
                foreach ($response['data'] as $mahalla) {
                    $mahallasList[$mahalla['id']] = $mahallasList[$mahalla['id']] ?? [];
                    $mahallasList[$mahalla['id']]['id'] = $mahalla['id'];
                    $mahallasList[$mahalla['id']]['district_id'] = $mahalla['district_id'];
                    $mahallasList[$mahalla['id']]['name_' . $localLanguage] = $mahalla['fullname'];
                }
            }
        }

        $mahallasFile = fopen(database_path("seeders/data/mahallas.csv"), 'w');
        foreach ($mahallasList as $_ => $mahalla) {
            fputcsv($mahallasFile, [$mahalla['id'], $mahalla['district_id'], $mahalla['name_uz'], $mahalla['name_oz'], $mahalla['name_ru']]);
        }
    }
}
