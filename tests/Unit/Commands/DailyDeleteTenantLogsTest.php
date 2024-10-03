<?php

namespace Tests\Unit\Commands;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DailyDeleteTenantLogsTest extends TestCase
{
    #[Test]
    public function dailyManagementForTenantLogs(): void
    {
        $company = Company::query()->first() ?? Company::factory()->create([
            'name' => 'Test Company',
            'owner_phone' => '0123456789',
            'owner_name' => 'Test Owner',
            'status' => CompanyStatusEnum::ACTIVE,
        ]);
        Storage::fake();

        $logPath = storage_path("company_{$company->id}/logs/");
        if (! File::exists($logPath)) {
            File::makeDirectory($logPath, 0777, true);
        }

        // File older than 14 days
        $oldLogFile = $logPath . 'test.log';
        File::put($oldLogFile, 'Test content');
        touch($oldLogFile, Carbon::now()->subDays(15)->timestamp); // 15 days old

        // File newer than 14 days
        $newLogFile = $logPath . 'new_test.log';
        File::put($newLogFile, 'New test content');
        touch($newLogFile, Carbon::now()->subDays(10)->timestamp); // 10 days old

        $this->artisan('daily:delete-logs')->assertExitCode(0);

        $this->assertFalse(File::exists($oldLogFile));

        $this->assertTrue(File::exists($newLogFile));
    }
}
