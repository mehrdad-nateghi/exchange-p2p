<?php

namespace App\Http\Requests\API\V1\File\Admin;

use App\Enums\FileStatusEnum;
use App\Enums\TradeStepsStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFileStatusAcceptRequest extends FormRequest
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
        $fileStatus = $this->file->status->value;
        $step = $this->file->fileable;

        $canUpdate = ($fileStatus === FileStatusEnum::UPLOADED->value && $step->isExpired()
                || $fileStatus === FileStatusEnum::REJECT_BY_BUYER->value)
            && $step->status === TradeStepsStatusEnum::DOING->value;

        $this->merge([
            'allow_update' => $canUpdate
        ]);
    }

    public function messages(): array
    {
        return [
            'allow_update.in' => 'File can only be updated when step is doing and it is either uploaded and expired, or rejected by buyer.'
        ];
    }
}
