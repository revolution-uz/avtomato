<?php

namespace App\Services\Installment;

use App\Enums\Company\AttachmentTypeEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\FeatureEnum;
use App\Exceptions\BalanceIsNotEnoughException;
use App\Http\Requests\Installment\InstallmentCardAttachRequest;
use App\Http\Requests\Installment\InstallmentCardDetachRequest;
use App\Http\Requests\Installment\InstallmentCommentRequest;
use App\Http\Requests\Installment\InstallmentCommentsDeleteRequest;
use App\Http\Requests\Installment\InstallmentDocumentUploadRequest;
use App\Http\Requests\Installment\InstallmentGuarantorRequest;
use App\Http\Requests\Installment\InstallmentGuarantorUpdateRequest;
use App\Http\Requests\Installment\InstallmentRequest;
use App\Http\Requests\Installment\InstallmentsDeleteRequest;
use App\Http\Requests\Installment\InstallmentToggleRequest;
use App\Http\Requests\Installment\InstallmentUpdateRequest;
use App\Models\Company\Attachment;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentCard;
use App\Models\Company\InstallmentComment;
use App\Models\Company\InstallmentGraph;
use App\Models\Company\InstallmentGuarantor;
use App\Models\Company\Tariff;
use App\Services\User\BalanceService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Throwable;

class InstallmentService
{
    private BalanceService $balanceService;

    public function __construct(BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public function create(InstallmentRequest $request): Installment
    {
        $attributes = $request->validated();
        $attributes['status'] = InstallmentStatusEnum::DRAFT;
        $attributes['sms_notify'] = false;
        $attributes['any_card_charge'] = false;
        $attributes['type'] = InstallmentTypeEnum::WITHOUT_CARD;

        return Installment::create($attributes);
    }

    public function updateDetails(Installment $installment, InstallmentUpdateRequest $request): Installment
    {
        if ($installment->status == InstallmentStatusEnum::DRAFT) {
            $attributes = $request->validated();
            $attributes['starts_in'] = $request->starts_in->toDateString();
            $installment->updateOrFail($attributes);
        }

        return $installment;
    }

    /**
     * @throws Throwable
     * @throws BalanceIsNotEnoughException
     */
    public function toggleAutoCharge(Installment $installment, InstallmentToggleRequest $request): Installment
    {
        if ($request->auto_charge) {
            $installment->type = InstallmentTypeEnum::WITH_CARD;
            if (! $installment->withdrawn_auto_charge) {
                $this->balanceService->chargeAmount(FeatureEnum::INSTALLMENT_AUTO_CHARGE, $installment);
                $installment->withdrawn_auto_charge = true;
            }
        } else {
            $installment->type = InstallmentTypeEnum::WITHOUT_CARD;
        }
        $installment->saveOrFail();

        return $installment;
    }

    public function deleteInstallments(InstallmentsDeleteRequest $request): void
    {
        Installment::query()->whereIn('status', InstallmentStatusEnum::getStatusListByStatus())->whereIn('id', $request->ids)->delete();
    }

    public function uploadAttachment(Installment $installment, InstallmentDocumentUploadRequest $request): Installment
    {
        Attachment::upload($request->file('file'), AttachmentTypeEnum::INSTALLMENT_DOCUMENT, $installment, $request->name);
        $installment->refresh();

        return $installment;
    }

    public function deleteAttachment(array $ids): void
    {
        Attachment::query()->whereIn('id', $ids)->delete();
    }

    public function attachCard(int $installmentId, InstallmentCardAttachRequest $request): InstallmentCard
    {
        $attributes = $request->validated();
        $attributes['installment_id'] = $installmentId;

        return InstallmentCard::create($attributes);
    }

    public function detachCards(InstallmentCardDetachRequest $request): void
    {
        foreach ($request->ids as $id) {
            $this->detachCard($id);
        }
    }

    public function detachCard(int $installmentCardId): void
    {
        $installmentCard = InstallmentCard::findOrFail($installmentCardId);
        $installmentCard->delete();
    }

    public function attachGuarantor(int $installmentId, InstallmentGuarantorRequest $request): InstallmentGuarantor
    {
        $attributes = $request->validated();
        $attributes['installment_id'] = $installmentId;
        $attributes['any_card_charge'] = false;

        return InstallmentGuarantor::create($attributes);
    }

    public function updateGuarantor(int $id, InstallmentGuarantorUpdateRequest $request): InstallmentGuarantor
    {
        $installmentGuarantor = InstallmentGuarantor::findOrFail($id);
        if ($installmentGuarantor->customer_id == $request->customer_id) {
            $installmentGuarantor->updateOrFail($request->validated());
        }

        return $installmentGuarantor;
    }

    /**
     * @param  int[]  $guarantorIds
     *
     * @throws Throwable
     */
    public function detachGuarantors(array $guarantorIds, $installmentId): void
    {
        InstallmentCard::query()
            ->whereHas('installment', function ($query) use ($guarantorIds) {
                $query->whereHas('guarantors', function ($query) use ($guarantorIds) {
                    $query->whereIn('id', $guarantorIds);
                });
            })
            ->where('installment_id', $installmentId)
            ->delete();
        //        delete guarantor
        InstallmentGuarantor::query()
            ->whereIn('customer_id', $guarantorIds)
            ->where('installment_id', $installmentId)
            ->delete();
    }

    public function createComment(int $installmentId, InstallmentCommentRequest $request): InstallmentComment
    {
        $attributes = $request->validated();
        $attributes['installment_id'] = $installmentId;
        $attributes['user_id'] = auth()->id();

        return InstallmentComment::create($attributes);
    }

    public function updateComment(int $id, InstallmentCommentRequest $request): InstallmentComment
    {
        $installmentComment = InstallmentComment::findOrFail($id);
        $installmentComment->updateOrFail($request->validated());

        return $installmentComment;
    }

    public function deleteComment(InstallmentCommentsDeleteRequest $request): void
    {
        foreach ($request->ids as $id) {
            $installmentComment = InstallmentComment::findOrFail($id);
            $installmentComment->delete();
        }
    }

    /**
     * @throws Throwable
     * @throws BalanceIsNotEnoughException
     */
    public function changeStatus(int $installmentId, InstallmentStatusEnum $status): bool
    {
        $installment = Installment::query()
            ->where('id', $installmentId)
            ->firstOrFail();

        if ($status == InstallmentStatusEnum::RUNNING) {
            $this->running($installment);
        }

        $installment->status = $status;

        return $installment->save();
    }

    /**
     * @throws BalanceIsNotEnoughException
     */
    private function running(Installment $installment): void
    {
        $this->balanceService->chargeAmount(PermissionEnum::getFeature(PermissionEnum::INSTALLMENT_START), $installment);
        /** @var Tariff $tariff */
        $tariff = Tariff::findOrFail($installment->tariff_id);
        $amount = $installment->amount;
        $amount -= $installment->initial_fee;
        $portion = $amount * $tariff->percent / 100;

        $currentDate = new Carbon;
        $currentDate->timestamp = $installment->starts_in->timestamp;
        $periods = CarbonPeriod::create($currentDate, '1 month', $currentDate->copy()->addMonths($installment->period - 1));

        foreach ($periods as $period) {
            $installmentGraph = new InstallmentGraph;
            $installmentGraph->installment_id = $installment->id;
            $installmentGraph->amount = $amount / $installment->period + $portion;
            $installmentGraph->starts_in = Carbon::make($period->timestamp);
            $installmentGraph->status = InstallmentStatusEnum::RUNNING;
            $installmentGraph->save();
        }
    }
}
