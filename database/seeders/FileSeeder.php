<?php

namespace Database\Seeders;

use App\Models\Legacy\File;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    public function run()
    {
        File::factory(10)->create();
    }
}
