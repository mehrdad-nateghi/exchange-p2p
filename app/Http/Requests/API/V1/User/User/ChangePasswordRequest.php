<?php

namespace App\Http\Requests\API\V1\User\User;

use App\Enums\TradeStepsStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'old_password' => 'required|string|min:8',
            'new_password' => [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->numbers(),
                'different:old_password'
            ],
            'new_password_confirmation' => 'required|same:new_password'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!Hash::check($this->old_password, auth()->user()->password)) {
                $validator->errors()->add('old_password', trans('validation.curren_password_incorrect'));
            }
        });
    }
}
