<?php

namespace Database\Seeders\Company;

use App\Models\Company;
use App\Models\Company\Ecommerce\DeliveryPoint;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DeliveryPointSeeder extends Seeder
{
    public function run(): void
    {
        tenancy()->initialize(Company::findOrFail(Company::REVO_TENANT_ID));

        $data = $this->parseCsv(database_path('seeders/data/delivery_points.csv'));

        $existingDeliveryPoints = DeliveryPoint::query()->whereIn('name_uz', $data->pluck('name_uz'))->pluck('name_uz')->toArray();

        $newData = $data->reject(fn ($item) => in_array($item['name_uz'], $existingDeliveryPoints))
            ->map(fn ($item) => [
                'name_uz' => $item['name_uz'],
                'name_ru' => $item['name_ru'],
                'name_en' => $item['name_uz'],
                'name_oz' => $item['name_ru'],
                'description_uz' => $item['description_uz'],
                'description_ru' => $item['description_ru'],
                'description_en' => $item['description_uz'],
                'description_oz' => $item['description_ru'],
                'is_prepaid' => true,
                'phone' => $item['phone'],
                'address_uz' => $item['address_uz'],
                'address_ru' => $item['address_ru'],
                'address_en' => $item['address_uz'],
                'address_oz' => $item['address_ru'],
                'landmark_uz' => $item['landmark_uz'],
                'landmark_ru' => $item['landmark_ru'],
                'landmark_en' => $item['landmark_uz'],
                'landmark_oz' => $item['landmark_ru'],
                'location_url' => $item['location_url'],
                'lat' => $item['lat'] ? (float) $item['lat'] : null,
                'lng' => $item['lng'] ? (float) $item['lng'] : null,
                'region_id' => $item['region_id'] ? (int) $item['region_id'] : null,
                'mahalla_id' => $item['city_id'] ? (int) $item['city_id'] : null,
            ]);

        if ($newData->isNotEmpty()) {
            DeliveryPoint::query()->insert($newData->toArray());
            $this->command->info('DeliveryPoints data imported successfully!');
        } else {
            $this->command->info('No new DeliveryPoints to import.');
        }
    }

    private function parseCsv(string $filePath): Collection
    {
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            throw new Exception("File not found or not readable at: {$filePath}");
        }

        $header = null;
        $data = collect();

        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000)) !== false) {
                if (! $header) {
                    $row[0] = preg_replace('/^\x{FEFF}/u', '', $row[0]);
                    $header = $row;
                } else {
                    $data->push(array_combine($header, $row));
                }
            }
            fclose($handle);
        }

        if ($data->isEmpty()) {
            throw new Exception('No valid data found in the CSV file.');
        }

        return $data;
    }
}
