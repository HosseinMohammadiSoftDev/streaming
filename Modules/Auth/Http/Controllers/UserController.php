<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Contract\ApiController;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Auth\Http\Requests\User\UpdateUserRequest;

class UserController extends ApiController
{
   public function index(Request $request)
    {
        try {
            $paginate = $request->input('paginate') ?? 10;
            $sortColumn = $request->input('sort', 'id');
            $sortDirection = Str::startsWith($sortColumn, '-') ? 'desc' : 'asc';
            $sortColumn = ltrim($sortColumn, '-');
    
            $users = User::orderBy($sortColumn, $sortDirection)->simplePaginate($paginate);
            return $this->respondSuccess('لیست کاربران با موفقیت دریافت شد', $users);
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در دریافت لیست کاربران رخ داده است');
        }
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return $this->respondSuccess('کاربر با موفقیت پیدا شد', $user);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound('کاربر مورد نظر یافت نشد');
        } catch (\Exception $e) {   
            return $this->respondInternalError('خطایی در نمایش اطلاعات کاربر رخ داده است');
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {        
        // try {
            $validated = $request->validated();
    
            if (Auth::id() != $id) {
                return $this->respondInternalError('شما مجاز به به‌روزرسانی این کاربر نیستید');
            }
    
            $user = User::findOrFail($id);
    
            if (User::where('email', $validated['email'])->where('id', '!=', $id)->exists()) {
                return $this->respondInternalError('ایمیل وارد شده قبلاً استفاده شده است');
            }
    
            $user->update($validated);
    
            return $this->respondSuccess('کاربر با موفقیت به‌روزرسانی شد', $user);
        // } catch (ModelNotFoundException $e) {
        //     return $this->respondNotFound('کاربر مورد نظر برای به‌روزرسانی یافت نشد');
        // } catch (\Exception $e) {
        //     return $this->respondInternalError('خطایی در به‌روزرسانی کاربر رخ داده است');
        // }
    }
}
