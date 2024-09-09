<?php

namespace Modules\Permission\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'permission_id' => 'required|exists:permissions,id',
            'name' => 'required|string|max:255|unique:permissions,name', 
           ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

      protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $errors = $validator->errors()->all();
        $firstError = $errors[0] ?? 'خطایی رخ داد';

        throw new \Illuminate\Validation\ValidationException(
            $validator,
            response()->json([
                'success' => false,
                'message' => 'ورودی‌های شما معتبر نیستند. ' . $firstError,
                'errors' => $errors,
            ], 422)
        );
    }
}
