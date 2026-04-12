<?php

namespace App\Http\Resources;

use App\Models\Registration;
use Illuminate\Http\Request;

class WorkshopWithRegistrationResource extends WorkshopResource
{
    public function __construct($resource, private readonly ?Registration $registration = null)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'registration' => $this->registration
                ? RegistrationResource::make($this->registration)
                : null,
        ]);
    }
}
