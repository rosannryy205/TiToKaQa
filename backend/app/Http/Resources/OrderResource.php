<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $items = $this->whenLoaded('details', function () {
            return $this->details->map(function ($d) {
                $name = optional($d->foods)->name ?? optional($d->combos)->name ?? 'Món ăn';
                $image = optional($d->foods)->image ?? optional($d->combos)->image ?? null;
                return [
                    'name'          => $name,
                    'quantity'      => (int) $d->quantity,
                    'price'         => (int) $d->price,
                    'image'         => $image,
                    'is_flash_sale' => (bool) $d->is_flash_sale,
                ];
            })->values();
        });

        return [
            'id'           => $this->id,
            'code'         => $this->order_code ?? $this->reservation_code,
            'status'       => $this->order_status,
            'order_time'   => $this->order_time,
            'shipping_fee' => (int) ($this->ship_cost ?? 0),
            'total_price'  => (int) ($this->total_price ?? 0),
            'table_fee'    => (int) ($this->table_fee ?? 0),
            'details'      => $items ?? [],
        ];
    }
}
