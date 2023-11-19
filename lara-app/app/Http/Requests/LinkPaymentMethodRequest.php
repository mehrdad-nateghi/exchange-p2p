<?php

namespace App\Http\Requests;

use App\Models\LinkedMethod;
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
            //'payment_method_id' => 'required_if:link_payment_method,true|exists:App\Models\PaymentMethod,id',
            'payment_method_attributes' => 'required|array|min:1'
        ];

        // Check if validation will be run for Link Payment Method functionality
        if($this->route('paymentMethodId') !== null && PaymentMethod::where('id', $this->route('paymentMethodId'))->exists()){
            $payment_method = PaymentMethod::find($this->route('paymentMethodId'));
            $rules = $this->generatePaymentMethodRules($rules, $payment_method);
        }
        // Check if validation will be run for Update Linked Payment Method functionality
        elseif($this->route('linkedMethodId') !== null && PaymentMethod::where('id', $this->route('linkedMethodId'))->exists()){
            $linked_method = LinkedMethod::find($this->route('linkedMethodId'));
            $payment_method = $linked_method->paymentMethod;
            $rules = $this->generatePaymentMethodRules($rules, $payment_method);
        }

        return $rules;
    }

    protected function generatePaymentMethodRules($rules, $payment_method){
        // Prepare apprpriate rules based on the input payment method
        $country = $payment_method->country;

        if ($country->name === 'DE' && $payment_method->name === 'Paypal') {
            $rules = $this->addPaypalRulesForDE($rules);
        } elseif ($country->name === 'DE' && $payment_method->name === 'Bank Transfer') {
            $rules = $this->addBankTransferRulesForDE($rules);
        } elseif ($country->name === 'IR' && $payment_method->name === 'Bank Transfer') {
            $rules = $this->addBankTransferRulesForIR($rules);
        }

        return $rules;
    }

    protected function addPaypalRulesForDE($rules)
    {
        $rules['payment_method_attributes.email'] = 'required|email';
        $rules['payment_method_attributes.holder_name'] = 'required|string';

        return $rules;
    }

    protected function addBankTransferRulesForDE($rules)
    {
        $rules['payment_method_attributes.bank_name'] = 'required|string';
        $rules['payment_method_attributes.holder_name'] = 'required|string';
        $rules['payment_method_attributes.iban'] = 'required|string';
        $rules['payment_method_attributes.bic'] = 'required|string';

        return $rules;
    }

    protected function addBankTransferRulesForIR($rules)
    {
        $rules['payment_method_attributes.bank_name'] = 'required|string';
        $rules['payment_method_attributes.holder_name'] = 'required|string';
        $rules['payment_method_attributes.account_number'] = 'string';
        $rules['payment_method_attributes.card_number'] = 'required|string';
        $rules['payment_method_attributes.shaba_number'] = 'required|string';

        return $rules;
    }
}
