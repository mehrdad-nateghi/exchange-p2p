<?php

namespace App\Http\Requests;

use App\Models\PaymentMethod;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\UnauthorizedException;

class LinkPaymentMethodRequest extends FormRequest
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
        $rules = [
            'payment_method_id' => 'required|exists:App\Models\PaymentMethod,id',
            'payment_method_attributes' => 'required|array|min:1'
        ];

        $rules = $this->generatePaymentMethodRules($rules);

        Log::info('rules:'.json_encode($rules));

        return $rules;
    }

    protected function generatePaymentMethodRules($rules){

        $payment_method_id = $this->has('payment_method_id') ? $this->input('payment_method_id'):'';
        if($payment_method_id && PaymentMethod::where('id', $payment_method_id)->exists()) {

            // Prepare apprpriate rules based on the input payment method
            $payment_method = PaymentMethod::find($payment_method_id);
            $country = $payment_method->country;

            if ($country->name === 'DE' && $payment_method->name === 'Paypal') {
                $rules = $this->addPaypalRules($rules);
            } elseif ($country->name === 'DE' && $payment_method->name === 'Bank Transfer') {
                Log::alert('DE-Bank Transfer');
            } elseif ($country->name === 'IR' && $payment_method->name === 'Bank Transfer') {
                Log::alert('IR-Bank Transfer');
            }
        }

        return $rules;

    }

    protected function addPaypalRulesForDE($rules)
    {
        $rules['payment_method_attributes.email'] = 'required|email';

        return $rules;
    }

    protected function addBankTransferRulesForDE($rules)
    {
        $rules['payment_method_attributes.email'] = 'required|email';

        return $rules;
    }
}
