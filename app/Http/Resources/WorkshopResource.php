<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkshopResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'description'     => $this->description,
            'start_time'      => $this->start_time->toISOString(),
            'end_time'        => $this->end_time->toISOString(),
            'start_time_input' => $this->start_time->format('Y-m-d\TH:i'),
            'end_time_input'   => $this->end_time->format('Y-m-d\TH:i'),
            'capacity'        => $this->capacity,
            'confirmed_count' => $this->whenCounted('confirmedRegistrations', fn () => $this->confirmed_registrations_count),
            'waiting_count'   => $this->whenCounted('waitingRegistrations',   fn () => $this->waiting_registrations_count),
            'available_seats' => $this->when(
                isset($this->confirmed_registrations_count),
                fn () => max(0, $this->capacity - $this->confirmed_registrations_count)
            ),
        ];
    }
}
