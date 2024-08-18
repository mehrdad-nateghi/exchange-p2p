<?php

namespace Database\Seeders;

use App\Models\Legacy\Invoice;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run()
    {
        Invoice::factory(10)->create();
    }
}
