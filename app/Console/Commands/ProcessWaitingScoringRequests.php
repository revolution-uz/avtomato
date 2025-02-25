<?php

namespace App\Console\Commands;

use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Models\Company;
use App\Models\Company\ScoringRequest;
use Illuminate\Console\Command;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ProcessWaitingScoringRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:waiting-scoring-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process waiting scoring requests';

    /**
     * Execute the console command.
     *
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function handle(): int
    {
        foreach (Company::get() as $company) {
            tenancy()->initialize($company);

            ScoringRequest::query()
                ->where('status', ScoringRequestStatusEnum::WAITING)
                ->update([
                    'status' => ScoringRequestStatusEnum::FAILED,
                ]);
        }

        return CommandAlias::SUCCESS;
    }
}
