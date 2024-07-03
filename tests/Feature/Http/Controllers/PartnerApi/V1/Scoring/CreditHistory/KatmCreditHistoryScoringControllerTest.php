<?php

namespace Http\Controllers\PartnerApi\V1\Scoring\CreditHistory;

use App\Models\Company\District;
use App\Models\Company\Region;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\PlumFaker;
use Tests\TestCase;

class KatmCreditHistoryScoringControllerTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function index(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->getJson(route('credit-history.index'));

        $response->assertOk();

        $this->assertIsArray($response->json('data'));
    }

    //    🔽🔽🔽 NOT FINISHED YET🔽🔽🔽
    //    /**
    //     * @test
    //     */
    //    public function create(): void
    //    {
    //        $admin = $this->createAdminUser();
    //
    //        PlumFaker::fakeResponse("https://api.abrand.uz/partner/katm/create-request", '../JSON-for-testing/credit-history-create-response.json', 200);
    //
    //        $region = Region::factory()->create();
    //        $district = District::factory()->create();
    //
    //        $request = [
    //            'pinfl' => 32704997130025,
    //            'first_name' => 'Musobek',
    //            'last_name' => 'Madrimov',
    //            'middle_name' => "G'aniy o'g'li",
    //            'phone' => 998999661999,
    //            'extra_phone' => 998919992919,
    //            'region_id' => $region->id,
    //            'district_id' => $district->id,
    //            'birth_date' => '27-04-1999',
    //            'passport_series' => 'AB',
    //            'passport_number' => '1189283',
    //            'passport_given_date' => '22-09-2015',
    //        ];
    //
    //        $response = $this->actingAs($admin)->postJson('/api/v1/scoring/credit-history/create', $request);
    //        dd($response->json());
    //        $response->assertSuccessful();
    //
    //        $this->assertDatabaseHas('scoring_requests', ['ext_id' => 1482]);
    //
    //        $this->assertIsArray($response->json('data'));
    //    }
}
