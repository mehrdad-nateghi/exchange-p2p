<?php

namespace App\Http\Requests\API\V1\Notification\User;

use Illuminate\Foundation\Http\FormRequest;

class IndexNotificationRequest extends FormRequest
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
            'filter' => [
                'nullable',
                'array'
            ],

            'filter.read_status' => [
                'nullable',
                'in:read,unread'
            ],

            'sort' => [
                'nullable',
                'in:created_at,-created_at,read_at,-read_at',
            ]
        ];
    }
}
