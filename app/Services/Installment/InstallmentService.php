<?php

namespace App\Services\Installment;

use App\Enums\Company\AttachmentTypeEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Http\Requests\Installment\InstallmentDocumentUploadRequest;
use App\Http\Requests\Installment\InstallmentImageUploadRequest;
use App\Http\Requests\Installment\InstallmentRequest;
use App\Http\Requests\Installment\InstallmentUpdateRequest;
use App\Models\Company\Attachment;
use App\Models\Company\Installment;

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
            $attributes = $request->validated();
            $installment->updateOrFail($attributes);
        }

        return $installment;
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
}
