<?php

use App\Models\CentralCustomer;
use App\Models\CentralUniredCard;
use App\Models\Company\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::beginTransaction();

        try {
            DB::table('unired_cards')->orderBy('id')->chunk(100, fn ($uniredCards) => $this->syncUniredCardsToCentral($uniredCards));
            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function down(): void
    {
        //
    }

    private function syncUniredCardsToCentral($uniredCards): void
    {
        $customerPinflMap = Customer::query()
            ->whereIn('id', $uniredCards->pluck('customer_id'))
            ->get()
            ->pluck('pinfl', 'id');

        $centralCustomers = CentralCustomer::query()
            ->whereIn('pinfl', $customerPinflMap->values())
            ->get()
            ->keyBy('pinfl');

        $data = [];

        foreach ($uniredCards as $uniredCard) {
            $customerPinfl = $customerPinflMap[$uniredCard->customer_id] ?? null;
            $customer = $centralCustomers[$customerPinfl] ?? null;

            if (! $customer) {
                continue;
            }

            $data[] = [
                'uuid' => $uniredCard->uuid,
                'customer_id' => $customer->id,
                'pinfl' => $uniredCard->pinfl,
                'auto' => $uniredCard->auto,
                'is_verified' => $uniredCard->is_verified,
                'is_blocked' => $uniredCard->is_blocked,
                'block_reason' => $uniredCard->block_reason,
                'blocked_at' => $uniredCard->blocked_at,
                'owner' => $uniredCard->owner,
                'pan' => $uniredCard->pan,
                'phone' => $uniredCard->phone,
                'type' => $uniredCard->type,
            ];
        }

        CentralUniredCard::upsert(
            $data,
            'uuid',
            ['customer_id', 'pinfl', 'auto', 'is_verified', 'is_blocked', 'block_reason', 'blocked_at', 'owner', 'pan', 'phone', 'type'],
        );
    }
};
