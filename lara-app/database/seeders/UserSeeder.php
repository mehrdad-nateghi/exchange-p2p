<?php

namespace Database\Seeders;

use App\Enums\RoleNameEnum;
use App\Enums\PaymentMethodTypeEnum;
use App\Models\ForeignBankAccount;
use App\Models\PaypalAccount;
use App\Models\RialBankAccount;
use App\Models\User;
use App\Services\API\V1\ForeignBankAccountService;
use App\Services\API\V1\PaypalAccountService;
use App\Services\API\V1\RialBankAccountService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    private const DEFAULT_PASSWORD = '12345678';
    private const USER_TYPES = [
        'buy-bidder', 'sell-bidder',
        'buy-requester', 'sell-requester',
        'bidder-saleh', 'requester-saleh',
        'bidder-sajjad', 'requester-sajjad',
        'bidder-mehrdad', 'requester-mehrdad',
        'bidder-maryam', 'requester-maryam',
        'bidder-navid', 'requester-navid'
    ];

    protected RialBankAccountService $rialBankAccountService;
    protected ForeignBankAccountService $foreignBankAccountService;
    protected PaypalAccountService $paypalAccountService;

    public function __construct(
        RialBankAccountService $rialBankAccountService,
        ForeignBankAccountService $foreignBankAccountService,
        PaypalAccountService $paypalAccountService
    ) {
        $this->rialBankAccountService = $rialBankAccountService;
        $this->foreignBankAccountService = $foreignBankAccountService;
        $this->paypalAccountService = $paypalAccountService;
    }

    public function run(): void
    {
        foreach (self::USER_TYPES as $type) {
            $this->createUserWithAccounts($type, RoleNameEnum::APPLICANT);
        }

        $this->createUser('admin', RoleNameEnum::ADMIN);
    }

    private function createUserWithAccounts(string $type, RoleNameEnum $role): void
    {
        $user = $this->createUser($type, $role);
        $this->createAccounts($user);
    }

    private function createUser(string $type, RoleNameEnum $role): User
    {
        $user = User::factory()->create([
            'first_name' => "$type first name",
            'last_name' => "$type last name",
            'email' => "$type@paylibero.com",
            'password' => Hash::make(self::DEFAULT_PASSWORD),
            'email_verified_at' => Carbon::now(),
        ]);

        $user->assignRole($role->value);

        return $user;
    }

    private function createAccounts(User $user): void
    {
        $this->createRialAccount($user);
        $this->createForeignAccount($user);
        $this->createPaypalAccount($user);
    }

    private function createRialAccount(User $user): void
    {
        $rialBankAccount = RialBankAccount::factory()->create();

        $this->rialBankAccountService->createPaymentMethod($rialBankAccount, [
            'user_id' => $user->id,
            'type' => PaymentMethodTypeEnum::RIAL_BANK->value
        ]);
    }

    private function createForeignAccount(User $user): void
    {
        $foreignBankAccount = ForeignBankAccount::factory()->create();

        $this->foreignBankAccountService->createPaymentMethod($foreignBankAccount, [
            'user_id' => $user->id,
            'type' => PaymentMethodTypeEnum::FOREIGN_BANK->value
        ]);
    }

    private function createPaypalAccount(User $user): void
    {
        $paypalAccount = PaypalAccount::factory()->create([
            'email' => $user->email,
        ]);

        $this->paypalAccountService->createPaymentMethod($paypalAccount, [
            'user_id' => $user->id,
            'type' => PaymentMethodTypeEnum::PAYPAL->value
        ]);
    }
}
