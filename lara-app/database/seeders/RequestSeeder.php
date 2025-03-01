<?php

namespace Database\Seeders;

use App\Enums\PaymentMethodTypeEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequestSeeder extends Seeder
{
    private const REQUESTS_PER_TYPE = 1000;
    private const REQUEST_TYPES = [RequestTypeEnum::BUY->value, RequestTypeEnum::SELL->value];

    const PRICE_BOUNDS = [
        'min' => 40000,
        'max' => 70000
    ];

    const VOLUME_BOUNDS = [
        'min' => 100,
        'max' => 300
    ];
/*    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }*/

    public function run()
    {
        DB::beginTransaction();

        try {
            $requesterUsers = $this->getRequesterUsers();

            foreach (self::REQUEST_TYPES as $type) {
                $this->createRequestsForUsers($requesterUsers, $type);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error seeding requests: " . $e->getMessage());
        }
    }

    private function getRequesterUsers()
    {
        return User::where('email', 'like', '%requester%')->get();
    }

    private function createRequestsForUsers($users, $type)
    {
        foreach ($users as $user) {
            $this->createRequestsForUser($user, $type);
        }
    }

    private function createRequestsForUser($user, $type)
    {
        for ($i = 0; $i < self::REQUESTS_PER_TYPE; $i++) {
            $request = Request::create([
                'user_id' => $user->id,
                'volume' => fake()->numberBetween(self::VOLUME_BOUNDS['min'], self::VOLUME_BOUNDS['max']),
                'price' => fake()->numberBetween(self::PRICE_BOUNDS['min'], self::PRICE_BOUNDS['max']),
                'min_allowed_price' => self::PRICE_BOUNDS['min'],
                'max_allowed_price' => self::PRICE_BOUNDS['max'],
                'type' => $type,
                'status' => RequestStatusEnum::TRADING->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->attachPaymentMethods($request,$user);
        }
    }

    private function attachPaymentMethods($request, $user)
    {
        $paymentMethods = $user->paymentMethods()->whereNot('type', PaymentMethodTypeEnum::RIAL_BANK->value)->get();
        $request->paymentMethods()->attach($paymentMethods);
    }
}
