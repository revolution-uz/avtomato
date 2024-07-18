<?php

namespace Tests\Unit\Services;

use App\Clients\AtmosBalanceClient;
use App\Services\Installment\InstallmentService;
use App\Services\User\BalanceService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InstallmentServiceTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function sqlQueriesAreWorking(): void
    {
        $this->assertIsArray((new InstallmentService(new BalanceService(new AtmosBalanceClient)))->getDashboardInfo());
    }
}
