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
use App\Http\Requests\Installment\InstallmentGuarantorDetachRequest;
use App\Http\Requests\Installment\InstallmentGuarantorRequest;
use App\Http\Requests\Installment\InstallmentGuarantorUpdateRequest;
use App\Http\Requests\Installment\InstallmentRequest;
use App\Http\Requests\Installment\InstallmentsDeleteRequest;
use App\Http\Requests\Installment\InstallmentToggleRequest;
use App\Http\Requests\Installment\InstallmentUpdateRequest;
use App\Models\Company\Attachment;
use App\Models\Company\Card;
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

    public function detachGuarantors(InstallmentGuarantorDetachRequest $request)
    {
        foreach ($request->ids as $id) {
            $this->detachGuarantor($id);
        }
    }

    public function detachGuarantor(int $installmentGuarantorId): void
    {
        /** @var InstallmentGuarantor $installmentGuarantor */
        $installmentGuarantor = InstallmentGuarantor::findOrFail($installmentGuarantorId);
        //        deleting guarantor all attached cards
        $cardIds = Card::query()->where('customer_id', $installmentGuarantor->customer_id)->pluck('id')->toArray();
        InstallmentCard::query()->where('installment_id', $installmentGuarantor->installment_id)->whereIn('card_id', $cardIds)->delete();
        //        delete guarantor
        $installmentGuarantor->delete();
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

    public function start(Installment $installment): bool
    {

        if ($installment->status == InstallmentStatusEnum::FROZEN) {
            return $this->changeStatus($installment, InstallmentStatusEnum::RUNNING);
        }
        if ($this->changeStatus($installment, InstallmentStatusEnum::RUNNING)) {
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

            return true;
        }

        return false;
    }

    public function cancel(Installment $installment)
    {
        return $this->changeStatus($installment, InstallmentStatusEnum::CANCELED);
    }

    public function freeze(Installment $installment)
    {
        return $this->changeStatus($installment, InstallmentStatusEnum::FROZEN);
    }

    public function finish(Installment $installment)
    {
        return $this->changeStatus($installment, InstallmentStatusEnum::FINISHED);
    }

    private function changeStatus(Installment $installment, InstallmentStatusEnum $statusEnum)
    {
        if ($this->possibleToConvertIntoStatus($installment, $statusEnum)) {
            $installment->status = $statusEnum;

            return $installment->save();
        }

        return false;
    }

    private function possibleToConvertIntoStatus(Installment $installment, InstallmentStatusEnum $statusEnum)
    {
        return in_array($installment->status, InstallmentStatusEnum::getStatusListByStatus($statusEnum));
    }
}
