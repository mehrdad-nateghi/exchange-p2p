<?php

namespace App\Listeners;

use App\Enums\PaymentMethodTypeEnum;
use App\Events\SignUpEvent;
use App\Services\API\V1\ForeignBankAccountService;
use App\Services\API\V1\PaypalAccountService;
use App\Services\API\V1\RialBankAccountService;

class AssignDefaultPaymentMethodToUserListener
{
    public RialBankAccountService $rialBankAccountService;
    public ForeignBankAccountService $foreignBankAccountService;
    public PaypalAccountService $paypalAccountService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        RialBankAccountService $rialBankAccountService,
        ForeignBankAccountService $foreignBankAccountService,
        PaypalAccountService $paypalAccountService
    )
    {
        $this->rialBankAccountService = $rialBankAccountService;
        $this->foreignBankAccountService = $foreignBankAccountService;
        $this->paypalAccountService = $paypalAccountService;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(SignUpEvent $event)
    {
        $userID = $event->user->id;
        $userEmail = $event->user->email;

        // RIAL ACCOUNT
        $rialBankAccount = $this->rialBankAccountService->create([
            'holder_name' => '',
            'bank_name' => '',
            'card_number' => '',
            'sheba' => '',
            'account_no' => '',
            'is_active' => false,
        ]);
        $this->rialBankAccountService->createPaymentMethod($rialBankAccount,[
            'user_id' => $userID,
            'type' => PaymentMethodTypeEnum::RIAL_BANK->value
        ]);

        // FOREIGN ACCOUNT
        $foreignBankAccount = $this->foreignBankAccountService->create([
            'holder_name' => '',
            'bank_name' => '',
            'iban' => '',
            'bic' => '',
            'is_active' => false,
        ]);
        $this->foreignBankAccountService->createPaymentMethod($foreignBankAccount,[
            'user_id' => $userID,
            'type' => PaymentMethodTypeEnum::FOREIGN_BANK->value
        ]);

        // PAYPAL ACCOUNT
        $paypalAccount = $this->paypalAccountService->create([
            'holder_name' => '',
            'email' => $userEmail,
            'is_active' => false,
        ]);
        $this->paypalAccountService->createPaymentMethod($paypalAccount,[
            'user_id' => $userID,
            'type' => PaymentMethodTypeEnum::PAYPAL->value
        ]);
    }
}
