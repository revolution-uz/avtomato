<?php

namespace App\Http\Controllers\PartnerApi\V1;

use App\Enums\Company\AttachmentTypeEnum;
use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\AttachmentRequest;
use App\Http\Resources\AttachmentResource;
use App\Models\Company\Attachment;
use Illuminate\Http\JsonResponse;
use Image;
use OpenApi\Attributes as OA;
use Storage;
use Str;

class AttachmentController extends Controller
{
    #[OA\Post(
        path: '/api/v1/attachments/upload',
        description: 'Attachment upload route',
        requestBody: new OA\RequestBody(
            description: 'Attachment upload body. Send as multipart/form-data',
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/AttachmentRequest')
            ),
        ),
        tags: ['Attachment'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'AttachmentResource'),
        ]
    )]
    public function upload(AttachmentRequest $request): JsonResponse
    {
        $photo = $request->file('file');
        $file_name =  uniqid() . '.' . $photo->extension();
        $path = $photo->storeAs(Attachment::PATH, $file_name, ['disk' => 'public']);

        //todo create thumb by different types of files
        switch ((int) $request->type) {
            case AttachmentTypeEnum::AVATAR->value:
                 $thumbnail = Image::make($photo)->resize(300, 300);
                 $previewPath = Attachment::THUMBNAIL_PATH . '/' . $file_name;
                 Storage::disk('public')->put($previewPath, $thumbnail->encode());
                 break;
        }

        $attachment = Attachment::create([
            'type' => $request->type,
            'path' => $path,
            'preview_path' => $previewPath,
            'name' => $request->name ?? $photo->getFilename(),
            'original_name' => $photo->getClientOriginalName(),
            'mime' => $photo->getMimeType(),
        ]);

        return ApiResponse::success(AttachmentResource::make($attachment));
    }

    #[OA\Get(
        path: '/api/v1/attachments/{id}',
        description: 'Attachment get route',
        tags: ['Attachment'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Attachment')
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'File content',
            ),
        ]
    )]
    public function getFile(Attachment $attachment)
    {
        return response()->file(storage_path('app/public/' . $attachment->path));
    }

    #[OA\Get(
        path: '/api/v1/attachments/{id}/preview',
        description: 'Preview of the attachment get route',
        tags: ['Attachment'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Attachment')
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'File content',
            ),
        ]
    )]
    public function getPreview(Attachment $attachment)
    {
        return response()->file(storage_path('app/public/' . $attachment->preview_path));
    }
}
