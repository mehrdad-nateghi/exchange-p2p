<?php

namespace Tests\Feature;

use App\Http\Controllers\FinancialController;
use App\Models\Financial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialTest extends TestCase
{
    use RefreshDatabase;

    public function testFeasibilityRangeWithFinancialInfo()
    {
        // Create a Financial instance in the database
        Financial::factory()->create([
            'feasibility_band_percentage' => 10,
        ]);

        // Set the config value for Euro Daily Rate
        config(['constants.Euro_Daily_Rate' => 100]);

        $controller = new FinancialController();

        $response = $controller->getFeasibilityRange();

        dump($response);

        // Assertions
        $this->assertEquals(200, $response['status']); // Check if the status is 200
    }

    public function testFeasibilityRangeWithoutFinancialInfo()
    {
        // Ensure there is no Financial instance in the database

        $controller = new FinancialController();

        $response = $controller->getFeasibilityRange();

        dump($response);

        // Assertions
        $this->assertEquals(404, $response['status']); // Check if the status is 404
    }
}
