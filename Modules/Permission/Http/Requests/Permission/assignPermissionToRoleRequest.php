<?php

namespace Modules\Permission\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class assignPermissionToRoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
           'role_id' => ['required', 'integer'],
           'permission_id' => ['required', 'array'],
           'permission_id.*' => ['required', 'exists:permissions,id']
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
