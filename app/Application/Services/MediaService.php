<?php

namespace App\Application\Services;

use App\Domain\Models\Inspection;
use App\Domain\Models\InspectionMedia;
use App\Domain\Models\InspectionResult;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    private array $allowedImageTypes;
    private int $maxImageSize;

    public function __construct()
    {
        $this->allowedImageTypes = explode(',', config('vis.upload.allowed_image_types', 'jpg,jpeg,png,webp'));
        $this->maxImageSize = (int) config('vis.upload.max_image_size', 5120); // KB
    }

    public function uploadForResult(Inspection $inspection, InspectionResult $result, array $files): Collection
    {
        $uploaded = collect();

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile || !$file->isValid()) {
                continue;
            }

            $media = $this->store($inspection, $file, $result->id, $result->question_id);
            if ($media) {
                $uploaded->push($media);
            }
        }

        return $uploaded;
    }

    public function uploadForInspection(Inspection $inspection, array $files, ?string $questionId = null): Collection
    {
        $uploaded = collect();

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile || !$file->isValid()) {
                continue;
            }

            $media = $this->store($inspection, $file, null, $questionId);
            if ($media) {
                $uploaded->push($media);
            }
        }

        return $uploaded;
    }

    public function store(Inspection $inspection, UploadedFile $file, ?string $resultId = null, ?string $questionId = null): ?InspectionMedia
    {
        $type = $this->determineType($file);

        if (!$type || !$this->validateFile($file, $type)) {
            return null;
        }

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = "inspections/{$inspection->id}/{$type}s";
        $path = $file->storeAs($directory, $filename, 'public');

        return InspectionMedia::create([
            'inspection_id' => $inspection->id,
            'result_id' => $resultId,
            'question_id' => $questionId,
            'type' => $type,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    public function delete(string $mediaId): bool
    {
        $media = InspectionMedia::findOrFail($mediaId);

        Storage::disk('public')->delete($media->path);

        return $media->delete();
    }

    public function getForInspection(string $inspectionId): Collection
    {
        return InspectionMedia::where('inspection_id', $inspectionId)
            ->orderBy('sort_order')
            ->get();
    }

    private function determineType(UploadedFile $file): ?string
    {
        // تحقق من الـ MIME الحقيقي بدل الاعتماد على اسم الملف
        $realMime  = $file->getMimeType() ?? '';
        $extension = strtolower($file->getClientOriginalExtension());

        $imageMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];

        if (in_array($realMime, $imageMimes) && in_array($extension, $this->allowedImageTypes)) {
            return 'image';
        }

        return null;
    }

    private function validateFile(UploadedFile $file, string $type): bool
    {
        // تحقق من الحجم
        $sizeInKb = $file->getSize() / 1024;
        if ($sizeInKb > $this->maxImageSize) {
            return false;
        }

        // تحقق إضافي من الـ MIME الحقيقي
        $realMime     = $file->getMimeType() ?? '';
        $imageMimes   = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];

        return in_array($realMime, $imageMimes);
    }

    public function getValidationRules(): array
    {
        $imageTypes = implode(',', $this->allowedImageTypes);

        return [
            'images.*' => "file|mimes:{$imageTypes}|max:{$this->maxImageSize}",
        ];
    }
}