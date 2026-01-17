<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'serial_number' => $this->serial_number,
            'reference' => $this->reference,
            'case_size' => $this->case_size,
            'wrist_size' => $this->wrist_size,
            'caliber' => $this->caliber,
            'timegrapher' => $this->timegrapher,
            'cost_original' => $this->cost_original,
            'cost_euro' => $this->cost_euro,
            'cost_currency' => $this->cost_currency,
            'cost_currency_rate' => $this->cost_currency_rate,
            'cost_currency_rate_date' => $this->cost_currency_rate_date,
            'cost_purchase_date' => $this->cost_purchase_date,
            'notes' => $this->notes,
            'description' => $this->description,
            'ai_instructions' => $this->ai_instructions,
            'ai_thread_id' => $this->ai_thread_id,
            'ai_status' => $this->ai_status,
            'ai_message' => $this->ai_message,
            'platform' => $this->platform,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'route_key' => $this->getRouteKey(),
            'brand' => $this->whenLoaded('brand', $this->brand?->name),
            'batch' => $this->whenLoaded('batch', $this->batch?->name),
            'status' => $this->whenLoaded('status', $this->status),
            'location' => $this->whenLoaded('location', $this->location?->name),

            'images' => $this->whenLoaded('images', function () {
                return WatchImageResource::collection($this->images);
            }),

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),

            'updated_by' => $this->whenLoaded('updatedBy', function () {
                return [
                    'id' => $this->updatedBy->id,
                    'name' => $this->updatedBy->name,
                ];
            }),

            'seller' => $this->whenLoaded('seller', function () {
                return [
                    'id' => $this->seller->id,
                    'name' => $this->seller->name,
                ];
            }),

            'agent' => $this->whenLoaded('agent', function () {
                return [
                    'id' => $this->agent->id,
                    'name' => $this->agent->name,
                ];
            }),
        ];
    }
}