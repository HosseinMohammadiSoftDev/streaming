<?php

namespace Modules\Permission\Transformers\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'image' => $this->image,
            'roles' => $this->roles->map(function($role) {
                return [
                    'role' => $role->name,
                    'permissions' => $role->permissions->pluck('name')
                ];
            }),
        ];
    }    
}

