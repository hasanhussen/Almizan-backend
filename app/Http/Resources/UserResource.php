<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            "name" => $this->name,
            "email" => $this->email,
            "avatar" => $this->image,
            "updated_at" => $this->updated_at->format('Y-m-d'),
            "created_at" => $this->created_at->format('Y-m-d'),
            "ban_reason" => $this->ban_reason ?? null,
            "ban_until" => $this->ban_until,
            "id" => $this->id,
            "email_verified_at" => $this->email_verified_at,
            "roles" => $this->getRoleNames(),
            "api_token" => $this->when(isset($this->api_token), $this->api_token),
        ];
    }
}
