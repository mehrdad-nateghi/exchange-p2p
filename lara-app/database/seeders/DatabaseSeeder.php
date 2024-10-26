<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\EmailTemplate;
use App\Models\TradeConstraint;
use App\Models\TransactionMethod;
use App\Models\UserVerify;
use Illuminate\Database\Seeder;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {

        DB::beginTransaction();

        try{
            $this->call([
                RoleSeeder::class,
                UserSeeder::class,
                StepSeeder::class,
                DepositReasonSeeder::class,
                RequestSeeder::class,
                BidSeeder::class,
            ]);

            DB::commit();

        } catch (\Exception $e){
            DB::rollback();
            throw $e;
        }

    }
}
