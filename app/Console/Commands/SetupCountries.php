<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;

class SetupCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup default countries on the database';

    protected function configure()
    {
        $this->addOption('all', null, null, $description = 'Option for setting up all countries.');
        $this->addOption('IR', null, null, $description = 'Option for setting up IR country.');
        $this->addOption('DE', null, null, $description = 'Option for setting up DE country.');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if($this->option('all')) {
            $this->setupIR();
            $this->setupDE();
        }
        elseif($this->option('DE')) {
            $this->setupDE();
        }
        elseif($this->option('IR')) {
            $this->setupIR();
        }
        else {
            $this->error('Invalid option provided. Use a valid option.');
            return Command::FAILURE;
        }
    }

    public function setupDE(){
        try {
            $country = config('config_default_DE.country');
            $country_DE = Country::where('name', $country)->get();
            if($country_DE->isEmpty()) {
                Country::create([
                    'name'=> $country
                ]);
            }

            $this->info('setup:countries --DE is completed!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('setup:countries --DE is failed!');
            return Command::FAILURE;
        }
    }

    public function setupIR(){
        try {
            $country = config('config_default_IR.country');
            $country_IR = Country::where('name', $country)->get();
            if($country_IR->isEmpty()) {
                Country::create([
                    'name'=> $country
                ]);
            }

            $this->info('setup:countries --IR is completed!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('setup:countries --IR is failed!');
            return Command::FAILURE;
        }
    }
}
