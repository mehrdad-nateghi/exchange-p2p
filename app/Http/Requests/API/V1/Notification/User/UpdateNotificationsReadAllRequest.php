<?php

namespace App\Http\Requests\API\V1\Notification\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNotificationsReadAllRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'allow_update' => ['required', 'boolean', Rule::in([true])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $hasUnreadNotifications = Auth::user()->unreadNotifications()->exists();

        $this->merge([
            'allow_update' => $hasUnreadNotifications
        ]);
    }

    public function messages(): array
    {
        return [
            'allow_update.in' => trans('validation.no_unread_notifications')
        ];
    }
}
