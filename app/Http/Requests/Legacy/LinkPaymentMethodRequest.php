<?php

namespace App\Http\Requests\Legacy;

use App\Models\LinkedMethod;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;


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

        if ($country->name === config('config_default_DE.country') && $payment_method->name === config('config_default_DE.payment_methods.paypal.name')) {
            $rules = $this->addPaypalRulesForDE($rules);
        } elseif ($country->name === config('config_default_DE.country') && $payment_method->name === config('config_default_DE.payment_methods.bank_transfer.name')) {
            $rules = $this->addBankTransferRulesForDE($rules);
        } elseif ($country->name === config('config_default_IR.country') && $payment_method->name === config('config_default_IR.payment_methods.bank_transfer.name')) {
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
