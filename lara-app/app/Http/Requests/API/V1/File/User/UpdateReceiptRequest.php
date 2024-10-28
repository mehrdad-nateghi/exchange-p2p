<?php

namespace App\Http\Requests\API\V1\File\User;

use App\Enums\FileStatusEnum;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateReceiptRequest extends FormRequest
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
            'status' => [
                'required',
                'in:accept,reject',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if($this->file->status === FileStatusEnum::ACCEPT_BY_BUYER->value){
            throw ValidationException::withMessages([
                'allow_update' => ['This receipt has already been accepted by the buyer and cannot be updated.'],
            ]);
        }

        if($this->file->status === FileStatusEnum::REJECT_BY_BUYER->value){
            throw ValidationException::withMessages([
                'allow_update' => ['This receipt has already been rejected by the buyer and cannot be updated.'],
            ]);
        }

        if($this->status === 'reject'){
            $step = $this->file->fileable;
            $expireAt = Carbon::parse($step->expire_at);
            $now = Carbon::now();
            $createdAt = Carbon::parse($step->created_at);
            $totalLifetime = $expireAt->diffInSeconds($createdAt);
            $timeUntilExpire = $expireAt->diffInSeconds($now);
            $timeElapsed = $totalLifetime - $timeUntilExpire;
            $percentageElapsed = ($timeElapsed / $totalLifetime) * 100;
            $allowUpdate = $percentageElapsed >= 70 && $now->lessThan($expireAt);
            if (!$allowUpdate) {
                throw ValidationException::withMessages([
                    'allow_update' => ['You can reject the receipt only after 70% of the expiration time has passed.'],
                ]);
            }
        }
    }

    protected function passedValidation(): void
    {
        $status =  $this->status === 'accept' ? FileStatusEnum::ACCEPT_BY_BUYER->value : FileStatusEnum::REJECT_BY_BUYER->value;

        $this->replace([
            'status' => $status,
        ]);
    }
}
