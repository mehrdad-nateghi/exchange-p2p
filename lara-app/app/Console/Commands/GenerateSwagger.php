<?php

namespace App\Console\Commands;

use App\Http\Controllers\Swagger\SwaggerController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateSwagger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:swagger';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate swagger.yaml in swagger folder.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting Swagger generation...');

        try {
            $this->info('Initializing SwaggerController...');
            $controller = app()->make(SwaggerController::class);

            $this->info('Calling swagger() method...');
            $result = $controller->swagger();

            $this->info('Swagger generation completed.');

            return Command::SUCCESS;
        } catch (\Throwable $t) {
            $this->error('An error occurred: ' . $t->getMessage());
            $this->error('Stack trace: ' . $t->getTraceAsString());
            Log::error($t);
            return Command::FAILURE;
        }
    }
}
