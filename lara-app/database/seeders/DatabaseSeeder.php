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
     */
    public function run()
    {

        DB::beginTransaction();

        try{
            $this->call([
                UserSeeder::class,
                FinancialSeeder::class,
                TradeConstraintSeeder::class,
                FrequentQuestionSeeder::class,
                SocialNetworkSeeder::class,
                CountrySeeder::class,
                PaymentMethodSeeder::class,
                MethodAttributeSeeder::class,
                LinkedMethodSeeder::class,
                LinkedmethodMethodattributeSeeder::class,
                NotificationSeeder::class,
                UserVerifySeeder::class,
                AuthenticationLogSeeder::class,
                RequestSeeder::class,
                RequestPaymentMethodSeeder::class,
                BidSeeder::class,
                TradeSeeder::class,
                InvoiceSeeder::class,
                TransactionSeeder::class,
                TransactionMethodSeeder::class,
                EmailTemplateSeeder::class,
                EmailSeeder::class,
                FileSeeder::class
            ]);

            DB::commit();

        } catch (\Exception $e){
            DB::rollback();
            throw $e;
        }

    }
}
