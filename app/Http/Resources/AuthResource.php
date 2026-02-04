<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'email'      => $this->email,
            'role_id'    => $this->role_id,
            'is_active'  => $this->is_active,
            'token'      => $this->token,
            'profile'    => $this->profile,

            // Add other fields as needed
        ];
    }
}
