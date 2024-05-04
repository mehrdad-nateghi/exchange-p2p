<?php

use App\Http\Controllers\Swagger\SwaggerController;
use Illuminate\Support\Facades\Route;

Route::get('doc/openapi.json', [SwaggerController::class, 'swagger'])->name('swagger');