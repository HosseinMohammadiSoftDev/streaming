<?php

namespace Modules\Permission\Http\Controllers;

use App\Http\Controllers\Contract\ApiController;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Permission\Http\Requests\Permission\assignPermissionToRoleRequest;
use Modules\Permission\Http\Requests\Permission\CreatePermissionRequest;
use Modules\Permission\Http\Requests\Role\RemovePermissionFromRoleRequest;
use Modules\Permission\Http\Requests\Role\UpdatePermissionRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends ApiController
{
         // اضافه کردن یک دسترسی به نقش 
    public function assignPermissionsToRole(assignPermissionToRoleRequest $request)
    {
       try {
            $roleId = $request->json('role_id');
            $permissionIds = $request->json('permission_id');
            $permissionIds;
            $role = Role::findOrFail($roleId);

            $permissions = Permission::whereIn('id', $permissionIds)->get();

            $role->givePermissionTo($permissions);

            return $this->respondSuccess('دسترسی با موفقیت اضافه شد', []);
       } catch (\Throwable $th) {
            $this->respondInternalError('خطایی زخ داده است');
       }
    }


        // حذف یک دسترسی از نقش
    public function removePermissionFromRole(RemovePermissionFromRoleRequest $request)
    {
        $roleId = $request->input('role_id');
        $permissionId = $request->input('permission_id');

        $role = Role::findOrFail($roleId);
        $permission = Permission::findOrFail($permissionId);

        $role->revokePermissionTo($permission);

        return response()->json([
            'message' => 'دسترسی با موفقیت از نقش مورد نظر پاک شد',
            'role' => $role,
            'permission' => $permission
        ]);
    }



    public function getAllPermissions(Request $request)
    {
        $paginate = $request->input('paginate') ?? 10;

        $permissions = Permission::simplePaginate($paginate);

        return $this->respondSuccess('دسترسی ها با موفقیت نمایش پیدا کردند', $permissions);
    }




        // ساختن دسترسی 
    public function createPermission(CreatePermissionRequest $request)
    {
        $permissionName = $request->input('name');
        $permission = Permission::create(['name' => $permissionName]);

        return $this->respondSuccess('دسترسی با موفقیت ساخته شد.', $permission);
    }


        // حذف دسترسی
    public function deletePermission($permissionId)
    {
        $permission = Permission::findOrFail($permissionId);

        $permission->delete();

        return $this->respondSuccess('دسترسی با موفقیت حذف شد.', $permission);
    }


            // ابدیت نام دسترسی
     public function updatePermissionName(UpdatePermissionRequest $request)
    {
        $permissionId = $request->input('permission_id');
        $newName = $request->input('name');

        $permission = Permission::findOrFail($permissionId);
        $permission->name = $newName;
        $permission->save();

        return $this->respondSuccess('دسترسی با موفقیت ابدیت شد', $permission);
    }
}
