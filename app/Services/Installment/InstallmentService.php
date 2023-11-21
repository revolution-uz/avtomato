<?php

namespace App\Services\Installment;

use App\Enums\Company\AttachmentTypeEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Http\Requests\Installment\InstallmentCommentRequest;
use App\Http\Requests\Installment\InstallmentCommentsDeleteRequest;
use App\Http\Requests\Installment\InstallmentDocumentUploadRequest;
use App\Http\Requests\Installment\InstallmentGuarantorRequest;
use App\Http\Requests\Installment\InstallmentGuarantorUpdateRequest;
use App\Http\Requests\Installment\InstallmentImageUploadRequest;
use App\Http\Requests\Installment\InstallmentRequest;
use App\Http\Requests\Installment\InstallmentsDeleteRequest;
use App\Http\Requests\Installment\InstallmentUpdateRequest;
use App\Models\Company\Attachment;
use App\Models\Company\Card;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentCard;
use App\Models\Company\InstallmentComment;
use App\Models\Company\InstallmentGuarantor;

class InstallmentService
{
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
            $installment->updateOrFail($request->validated());
        }

        return $installment;
    }

    public function deleteInstallments(InstallmentsDeleteRequest $request): void
    {
        Installment::query()->whereIn('status', InstallmentStatusEnum::getStatusListByStatus())->whereIn('id', $request->ids)->delete();
    }

    public function uploadAttachment(Installment $installment, InstallmentDocumentUploadRequest $request): Installment
    {
        Attachment::upload($request->file('file'), AttachmentTypeEnum::INSTALLMENT_DOCUMENT, $installment);
        $installment->refresh();

        return $installment;
    }

    public function uploadImage(Installment $installment, InstallmentImageUploadRequest $request): Installment
    {
        Attachment::upload($request->file('file'), AttachmentTypeEnum::INSTALLMENT_IMAGE, $installment);
        $installment->refresh();

        return $installment;
    }

    public function deleteAttachment(array $ids): void
    {
        Attachment::query()->whereIn('id', $ids)->delete();
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
        $installmentGuarantor->updateOrFail($request->validated());

        return $installmentGuarantor;
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
        InstallmentComment::query()->whereIn('id', $request->ids)->delete();
    }
}
