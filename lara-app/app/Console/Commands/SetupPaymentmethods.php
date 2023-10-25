<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupPaymentmethods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:paymentmethods {argument?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup contry-specific default payment methods on the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $argument = $this->argument('argument');

        if ($argument === null) {
            $this->setupPaymentmethodsForIran();
            $this->setupPaymentmethodsForGermany();
        }
        elseif ($argument === 'setupPaymentmethodsForIran') {
            $this->setupPaymentmethodsForIran();
        }
        elseif ($argument === 'setupPaymentmethodsForGermany') {
            $this->setupPaymentmethodsForGermany();
        }
        else {
            $this->error('Invalid argument provided. Use a valid option or provide no argument to execute all methods.');
        }
    }

    public function setupPaymentmethodsForIran(){
        DB::beginTransaction();

        try{
            // Clear associated record to 'Iran' and its payment methods and attributes if exist
            $countries = Country::where('name', 'Iran')->get();
            foreach ($countries as $country) {
                $country->delete();
            }

            // Create country
            $country = Country::create([
                'name'=>'Iran'
            ]);

            // Create payment methods for the country
            $payment_methods_info = [
                ['name' => 'Bank Account'],
            ];
            $country->paymentmethods()->createMany($payment_methods_info);

            // Create attributes for respective payment method
            $bank_account_attributes = [
                ['name' => 'bank_name'],
                ['name' => 'holder_name'],
                ['name' => 'account_number'],
                ['name' => 'card_number'],
                ['name' => 'shaba_number']
            ];
            $bank_account_payment_method = $country->paymentmethods()->where('name','Bank Account')->first();
            $bank_account_payment_method->attributes()->createMany($bank_account_attributes);

            DB::commit();

            $this->info('setupPaymentmethodsForIran is completed!');
            return Command::SUCCESS;
        } catch (\Exception $e) {

            DB::rollBack();

            $this->error('setupPaymentmethodsForIran is failed!');
            return Command::FAILURE;
        }
    }

    public function setupPaymentmethodsForGermany(){
        DB::beginTransaction();

        try {
            // Clear associated record to 'Germany' and its payment methods and attributes if exist
            $countries = Country::where('name', 'Germany')->get();
            foreach ($countries as $country) {
                $country->delete();
            }

            // Create country
            $country = Country::create([
                'name'=>'Germany'
            ]);

            // Create payment methods for the country
            $payment_methods_info = [
                ['name' => 'Bank Account'],
                ['name' => 'Paypal']
            ];
            $country->paymentmethods()->createMany($payment_methods_info);

            // Create attributes for respective payment method
            $bank_account_attributes = [
                ['name' => 'bank_name'],
                ['name' => 'holder_name'],
                ['name' => 'iban'],
                ['name' => 'bic']
            ];
            $bank_account_payment_method = $country->paymentmethods()->where('name','Bank Account')->first();
            $bank_account_payment_method->attributes()->createMany($bank_account_attributes);

            $paypal_attributes = [
                ['name' => 'email']
            ];
            $paypal_payment_method = $country->paymentmethods()->where('name','Paypal')->first();
            $paypal_payment_method->attributes()->createMany($paypal_attributes);

            DB::commit();

            $this->info('setupPaymentmethodsForGermany is completed!');
            return Command::SUCCESS;

        } catch (\Exception $e) {

            DB::rollBack();

            $this->error('setupPaymentmethodsForGermany is failed!');
            return Command::FAILURE;
        }
    }
}
