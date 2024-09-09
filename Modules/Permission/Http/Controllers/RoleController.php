<?php

namespace Modules\Permission\Http\Controllers;

use App\Http\Controllers\Contract\ApiController;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Permission\Entities\Role;
use Modules\Permission\Http\Requests\Role\AssignRoleToUserRequeste;
use Modules\Permission\Http\Requests\Role\CreateRoleRequest;
use Modules\Permission\Http\Requests\Role\RemoveRoleFromUserRequest;
use Modules\Permission\Http\Requests\Role\UpdateRoleRequest;
use Modules\Permission\Transformers\User\UserResource;

class RoleController extends ApiController
{
    
            // crud roles
    public function getAllRoles()
    {
        $roles = Role::with(['permissions'])->get();
        return $this->respondSuccess('نقش ها باموفقیت دریافت شدند.', $roles);
    }


     public function getRoleDetails($roleId)
     {
        $role = Role::with(['permissions', 'users'])->findOrFail($roleId);

        return $this->respondSuccess('جزعیات نقش با موفقیت دریافت شد', [
            'role' => $role->name,
            'permissions' => $role->permissions->pluck('name'),
            'users' => $role->users->pluck('name'),
        ]);
    }


    public function getUserRoles($userId)
    {
        $user = User::findOrFail($userId);

        return $this->respondSuccess('مقام ها و دسترسی ها کاربر با موفقیت پیدا شد.', new UserResource($user));
    }

    
    public function createRole(CreateRoleRequest $request)
    {
        $roleName = $request->input('name');
        $roleCrate = Role::create(['name' => $roleName]);
        return $this->respondSuccess('نقش با موفقیت ساخته شد', $roleCrate);
    }

    
   public function updateRoleName(UpdateRoleRequest $request)
    {
        $roleId = $request->input('role_id');
        $newName = $request->input('name');

        $role = Role::findOrFail($roleId);
        $role->name = $newName;
        $role->save();

        return $this->respondSuccess('نام نقش با موفقیت ابدیت شد', $role);
    }
  
    
   public function deleteRole($roleId)
    {
        $role = Role::findOrFail($roleId);
        $role->delete();
        return $this->respondSuccess('نقش با موفقیت حذف شد', $role);
    }


            // assign role 

     public function assignRoleToUser(AssignRoleToUserRequeste $request)
    {
        $userId = $request->input('user_id');
        $roleId = $request->input('role_id');

        $user = User::findOrFail($userId);
        $role = Role::findOrFail($roleId);
        $user->assignRole($role);

        return $this->respondSuccess('نقش با موفقیت به کاربر مورد نظر اضافه گردید', $user);
    }


     public function removeRoleFromUser(RemoveRoleFromUserRequest $request)
    {
        $userId = $request->input('user_id');
        $roleId = $request->input('role_id');

        $user = User::findOrFail($userId);
        $role = Role::findOrFail($roleId);

        $user->removeRole($role);

        return response()->json([
            'message' => 'نقش با موفقیت از کاربر مورد نظر حذف شد',
            'user' => $user
        ]);
    }
}
