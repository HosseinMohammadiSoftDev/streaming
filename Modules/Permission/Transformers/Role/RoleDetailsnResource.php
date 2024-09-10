<?php

namespace Modules\Permission\Transformers\Role;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleDetailsnResource extends JsonResource
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
            'role' => $this->name,
            'permissions' => $this->permissions->pluck('name'),
            'users' => UserRoleDetailsnResource::collection($this->users), // استفاده از UserResource برای کاربران
        ];
    }
}
