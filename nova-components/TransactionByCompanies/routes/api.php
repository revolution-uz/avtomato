<?php

use App\Models\Company;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $companies = Company::query()
        ->when($request->company_status !== null, fn ($q) => $q
            ->where('status', $request->company_status))
        ->get();

    $resources = [];
    foreach ($companies as $company) {
        tenancy()->initialize($company);

        $transactionQuery = Transaction::query()
            ->when($request->status !== null, fn ($q) => $q
                ->where('status', $request->status))
            ->when($request->from, fn ($q) => $q
                ->whereDate('paid_at', '>=', $request->from))
            ->when($request->to, fn ($q) => $q
                ->whereDate('paid_at', '<=', $request->to));

        $resources[] = [
            'id' => $company->id,
            'name' => $company->name,
            'status' => $company->status->name,
            'atmos_with_card' => (clone $transactionQuery)->where('transaction_type_id', TransactionType::ATMOS_WITH_CARD)->sum('amount'),
            'atmos_with_pinfl' => (clone $transactionQuery)->where('transaction_type_id', TransactionType::ATMOS_WITH_PINFL)->sum('amount'),
            'unired_with_pinfl' => (clone $transactionQuery)->where('transaction_type_id', TransactionType::UNIRED_WITH_PINFL)->sum('amount'),
            'other' => (clone $transactionQuery)->whereNotIn('transaction_type_id', [TransactionType::ATMOS_WITH_CARD, TransactionType::ATMOS_WITH_PINFL, TransactionType::UNIRED_WITH_PINFL])->sum('amount'),
        ];
    }

    return response()->json($resources);
});

Route::get('{companyId}', function (Request $request) {
    tenancy()->initialize($request->companyId);

    $perPage = $request->input('per_page', 25);
    $page = $request->input('page', 1);

    $transactionQuery = Transaction::query()
        ->when($request->status !== null, fn ($q) => $q
            ->where('status', $request->status))
        ->when($request->from, fn ($q) => $q
            ->whereDate('paid_at', '>=', $request->from))
        ->when($request->to, fn ($q) => $q
            ->whereDate('paid_at', '<=', $request->to));

    $resources = (clone $transactionQuery)
        ->when($request->type, fn ($q) => $q
            ->where('transaction_type_id', $request->type))
        ->orderBy('id')
        ->with('branch', 'transactionType')
        ->skip($perPage * ($page - 1))
        ->limit($perPage)
        ->get()
        ->map(fn (Transaction $transaction) => [
            'id' => $transaction->id,
            'installment_id' => $transaction->installment_id,
            'branch' => $transaction->branch?->name,
            'transaction_type' => $transaction->transactionType->name_en,
            'amount' => $transaction->amount,
            'paid_at' => $transaction->paid_at->format('Y-m-d H:i'),
            'status' => $transaction->status->name,
        ]);

    return response()->json([
        'company' => [
            'id' => tenant('id'),
            'name' => tenant('name'),
            'status' => tenant('status')?->name,
            'atmos_with_card' => (clone $transactionQuery)->where('transaction_type_id', TransactionType::ATMOS_WITH_CARD)->sum('amount'),
            'atmos_with_pinfl' => (clone $transactionQuery)->where('transaction_type_id', TransactionType::ATMOS_WITH_PINFL)->sum('amount'),
            'unired_with_pinfl' => (clone $transactionQuery)->where('transaction_type_id', TransactionType::UNIRED_WITH_PINFL)->sum('amount'),
            'other' => (clone $transactionQuery)->whereNotIn('transaction_type_id', [TransactionType::ATMOS_WITH_CARD, TransactionType::ATMOS_WITH_PINFL, TransactionType::UNIRED_WITH_PINFL])->sum('amount'),
        ],
        'resources' => $resources,
        'meta' => [
            'total' => (clone $transactionQuery)->count(),
            'per_page' => $perPage,
            'page' => $page,
        ],
    ]);
});
