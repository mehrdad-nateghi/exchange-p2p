<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SetupPaymentMethods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:payment-methods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup contry-specific default payment methods on the database';

    protected function configure()
    {
        $this->addOption('all', null, null, $description = 'Option for setting up payment methods of all countries.');
        $this->addOption('IR', null, null, $description = 'Option for setting up payment methods of IR country.');
        $this->addOption('DE', null, null, $description = 'Option for setting up payment methods of DE country.');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if($this->option('all')) {
            $this->setupPaymentMethodsForIR();
            $this->setupPaymentMethodsForDE();
        }
        elseif($this->option('DE')) {
            $this->setupPaymentMethodsForDE();
        }
        elseif($this->option('IR')) {
            $this->setupPaymentMethodsForIR();
        }
        else {
            $this->error('Invalid option provided. Use a valid option.');
            return Command::FAILURE;
        }
    }


    public function setupPaymentMethodsForDE()
    {

        try {
            $country = config('config_default_DE.country');
            $country_DE = Country::where('name', $country)->first();

            if (!$country_DE) {
                $this->error('DE country does not exist. setup:payment-methods --DE is failed!');
                return Command::FAILURE;
            }

            $paymentMethods_DE = config('config_default_DE.payment_methods');

            foreach ($paymentMethods_DE as $pm) {
                // Check whether the current payment method exists or not
                $pm_name = $pm['name'];
                $payment_method = $country_DE->paymentMethods()->where('name', $pm_name)->first();

                if (!$payment_method) {
                    // Create a new payment method
                    $created_payment_method = $country_DE->paymentMethods()->create(['name' => $pm_name]);

                    // Declare attributes for the created payment method
                    $attributes = $pm['attributes'];
                    $created_payment_method->attributes()->createMany($attributes);
                }
            }

            $this->info('setup:payment-methods --DE is completed!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('setup:payment-methods --DE is failed!');
            return Command::FAILURE;
        }

    }

    public function setupPaymentMethodsForIR()
    {

        try {
            $country = config('config_default_IR.country');
            $country_IR = Country::where('name', $country)->first();

            if (!$country_IR) {
                $this->error('IR country does not exist. setup:payment-methods --IR is failed!');
                return Command::FAILURE;
            }

            $paymentMethods_IR = config('config_default_IR.payment_methods');

            foreach ($paymentMethods_IR as $pm) {
                // Check whether the current payment method exists or not
                $pm_name = $pm['name'];
                $payment_method = $country_IR->paymentMethods()->where('name', $pm_name)->first();

                if (!$payment_method) {
                    // Create a new payment method
                    $created_payment_method = $country_IR->paymentMethods()->create(['name' => $pm_name]);

                    // Declare attributes for the created payment method
                    $attributes = $pm['attributes'];
                    $created_payment_method->attributes()->createMany($attributes);
                }
            }

            $this->info('setup:payment-methods --IR is completed!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('setup:payment-methods --IR is failed!');
            return Command::FAILURE;
        }

    }

}
