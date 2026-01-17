<?php

declare(strict_types=1);

namespace App\Actions\Watch;

use App\Models\Batch;
use App\Models\Brand;
use App\Models\Location;
use App\Models\Watch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UpdateOrCreateAction
{
    public function __invoke(array $attributes, array $values = [], mixed $routeKey = null): Model
    {
        return DB::transaction(function () use ($attributes, $values) {
            $this->processBatchId($values);
            $this->processBrandId($values);
            $this->setDefaultLocation($values);

            $watch = Watch::query()->updateOrCreate($attributes, $values);

            $this->syncImagesIfNeeded($watch, $values);

            return $watch;
        });
    }

    private function processBatchId(array &$values): void
    {
        if (!isset($values['batch'])) {
            return;
        }

        $batch = Batch::firstOrCreate(['name' => $values['batch']]);
        $values['batch_id'] = $batch->id;
        unset($values['batch']);
    }

    private function processBrandId(array &$values): void
    {
        if (!isset($values['brand'])) {
            return;
        }

        $brand = Brand::firstOrCreate(['name' => $values['brand']]);
        $values['brand_id'] = $brand->id;
        unset($values['brand']);
    }

    private function setDefaultLocation(array &$values): void
    {
        if (!isset($values['location']) && defined(Location::class . '::DEFAULT_COUNTRY')) {
            $values['location'] = Location::DEFAULT_COUNTRY;
        }
    }

    private function syncImagesIfNeeded(Watch $watch, array $values): void
    {
        if (!isset($values['images'])) {
            return;
        }

        if (class_exists(WatchImageSyncAction::class)) {
            (new WatchImageSyncAction)($watch, $values['images']);
        }
    }
}
