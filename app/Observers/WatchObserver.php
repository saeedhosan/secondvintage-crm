<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Watch;
use App\Models\WatchImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WatchObserver
{
    public function creating(Watch $watch): void
    {
        $this->setUserId($watch);
        $this->generateSkuIfNeeded($watch);
    }

    public function updating(Watch $watch): void
    {
        $this->setUpdatedBy($watch);
        $this->regenerateSkuIfNeeded($watch);
    }

    public function deleting(Watch $watch): void
    {
        $this->deleteAssociatedImages($watch);
    }

    public function created(Watch $watch): void
    {
    }

    public function updated(Watch $watch): void
    {
    }

    public function deleted(Watch $watch): void
    {
    }

    public function restored(Watch $watch): void
    {
    }

    public function forceDeleted(Watch $watch): void
    {
    }

    private function setUserId(Watch $watch): void
    {
        if (empty($watch->user_id) && Auth::check()) {
            $watch->user_id = Auth::id();
        }
    }

    private function generateSkuIfNeeded(Watch $watch): void
    {
        if (empty($watch->sku) && $watch->name && $watch->brand) {
            $brandName = $watch->brand?->name ?? $watch->brand;
            $watch->sku = generateSKU($brandName, $watch->name, Watch::class);
        }
    }

    private function setUpdatedBy(Watch $watch): void
    {
        if (Auth::check()) {
            $watch->updated_by = Auth::id();
        }
    }

    private function regenerateSkuIfNeeded(Watch $watch): void
    {
        $nameChanged = $watch->isDirty('name');
        $brandChanged = $watch->isDirty('brand_id');
        $brandNameChanged = $this->hasBrandNameChanged($watch);

        if ($nameChanged || $brandChanged || $brandNameChanged) {
            $brandName = $watch->brand?->name ?? '';
            $watch->sku = generateSKU($brandName, $watch->name, Watch::class);
        }
    }

    private function hasBrandNameChanged(Watch $watch): bool
    {
        if ($watch->isDirty('brand_id')) {
            return false;
        }

        if (!$watch->relationLoaded('brand')) {
            return false;
        }

        return $watch->brand->isDirty('name');
    }

    private function deleteAssociatedImages(Watch $watch): void
    {
        if (!$watch->images) {
            return;
        }

        foreach ($watch->images as $image) {
            $this->deleteImageFile($image);
            
            if ($image instanceof WatchImage) {
                $image->delete();
            }
        }
    }

    private function deleteImageFile(mixed $image): void
    {
        $filePath = $image?->public_url ?? '';
        
        if (!empty($filePath) && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }
}
