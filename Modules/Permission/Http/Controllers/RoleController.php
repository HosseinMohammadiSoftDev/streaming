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
use Modules\Permission\Transformers\Role\RoleDetailsnResource;
use Modules\Permission\Transformers\Role\RoleResource;
use Modules\Permission\Transformers\User\UserResource;

class RoleController extends ApiController
{
    
            // crud roles
    public function getAllRoles(Request $request)
    {
         try {

            $roles = Role::with('permissions')->get();

            return $this->respondSuccess('نقش‌ها با موفقیت دریافت شدند.', RoleResource::collection($roles));
         } catch (\Throwable $th) {
            return $this->respondInternalError('خطایی درخ داده است');
        }
    }
    

    public function getRoleDetails($roleId)
     {
        try {  

            $role = Role::with(['permissions', 'users'])->findOrFail($roleId);

            return $this->respondSuccess('جزئیات نقش با موفقیت دریافت شد.', new RoleDetailsnResource($role));
        } catch (\Throwable $th) {
            return $this->respondInternalError('{اطمینان حاصل کنید ورودی درست است}:خطایی درخ داده است');
        }
    }


    public function getUserRoles($userId)
    { 
    try {
            $user = User::findOrFail($userId);

            return $this->respondSuccess('مقام ها و دسترسی ها کاربر با موفقیت پیدا شد.', new UserResource($user));
        } catch (\Throwable $th) {
            return $this->respondInternalError('{اطمینان حاصل کنید ورودی درست است}:خطایی درخ داده است');
        }
    }

    
    public function createRole(CreateRoleRequest $request)
    {
       try {

            $roleName = $request->input('name');
            $roleCrate = Role::create(['name' => $roleName]);

            return $this->respondSuccess('نقش با موفقیت ساخته شد', $roleCrate);
       } catch (\Throwable $th) {
            return $this->respondInternalError('خطایی درخ داده است');
       }
    }

    
   public function updateRoleName(UpdateRoleRequest $request)
    {
    try {
            $roleId = $request->input('role_id');
            $newName = $request->input('name');

            $role = Role::findOrFail($roleId);
            $role->name = $newName;
            $role->save();

            return $this->respondSuccess('نام نقش با موفقیت ابدیت شد', $role);
        } catch (\Throwable $th) {
            return $this->respondInternalError('{نام نقش باید یونیک باشد}:خطایی درخ داده است');
        }
    }
  
    
   public function deleteRole($roleId)
    {
    try {

            $role = Role::findOrFail($roleId);
            $role->delete();

            return $this->respondSuccess('نقش با موفقیت حذف شد', $role);
        } catch (\Throwable $th) {
            return $this->respondInternalError('{اطمینان حاصل کنید ورودی درست است}:خطایی درخ داده است');
        }
    }


            // assign role 

     public function assignRoleToUser(AssignRoleToUserRequeste $request)
    {
    try {
            $userId = $request->input('user_id');
            $roleId = $request->input('role_id');

            $user = User::findOrFail($userId);
            $role = Role::findOrFail($roleId);
            $user->assignRole($role);

            return $this->respondSuccess('نقش با موفقیت به کاربر مورد نظر اضافه گردید', $role);
        } catch (\Throwable $th) {
            return $this->respondInternalError('خطایی درخ داده است');
        }
    }


     public function removeRoleFromUser(RemoveRoleFromUserRequest $request)
    {
        try {
            $userId = $request->input('user_id');
            $roleId = $request->input('role_id');

            $user = User::findOrFail($userId);
            $role = Role::findOrFail($roleId);

            $user->removeRole($role);

        } catch (\Throwable $th) {
                return $this->respondInternalError('خطایی درخ داده است');
        }   
    }
}
    
        

