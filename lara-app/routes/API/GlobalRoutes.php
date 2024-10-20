<?php

/*
|--------------------------------------------------------------------------
| Global Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\V1\Public\DailyRateRangeController;
use App\Http\Controllers\API\V1\Public\GatewayCallbackController;
use App\Http\Controllers\API\V1\Public\HealthCheckController;
use App\Http\Controllers\API\V1\Public\MeTestController;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

Route::name('global.')->group(function () {
    Route::get('/health',HealthCheckController::class)->name('health.check');
    Route::get('/me-test',MeTestController::class)->name('me.test');
    Route::get('/daily-rate-range',DailyRateRangeController::class)->name('daily.rate.range');
    Route::get('/daily-rate-range',DailyRateRangeController::class)->name('daily.rate.range');
    Route::get('/gateway/callback',GatewayCallbackController::class)->name('gateway.callback');
});

Route::post('/upload',function (Request $request){

    /*$s3 = new S3Client([
        'version' => 'latest',
        'region'  => env('AWS_DEFAULT_REGION'),
        'endpoint' => env('AWS_ENDPOINT'),
        'use_path_style_endpoint' => true,
        'credentials' => [
            'key'    => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
        ],
    ]);

    try {
        $result = $s3->listBuckets();
        foreach ($result['Buckets'] as $bucket) {
            echo $bucket['Name'] . "\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }

    dd('DONE');*/

    $request->validate([
        'file' => 'required|file',
    ]);

    $file = $request->file('file');

    // Log file details
    Log::info('Attempting to upload file', [
        'name' => $file->getClientOriginalName(),
        'size' => $file->getSize(),
        'mime' => $file->getMimeType(),
    ]);

    // Log S3 configuration
    Log::info('S3 Configuration', [
        'driver' => config('filesystems.disks.s3.driver'),
        'key' => config('filesystems.disks.s3.key'),
        'region' => config('filesystems.disks.s3.region'),
        'bucket' => config('filesystems.disks.s3.bucket'),
        'url' => config('filesystems.disks.s3.url'),
        'endpoint' => config('filesystems.disks.s3.endpoint'),
    ]);

    try {
        $path = Storage::disk('s3')->putFile('uploads', $file);
        //$path = Storage::disk('s3')->put('a.txt',"Its working");
        Log::error("path: {$path}");

        if ($path === false) {
            Log::error('File upload failed');
            return response()->json(['error' => 'File upload failed'], 500);
        }
        Log::info('File uploaded successfully', ['path' => $path]);
        return response()->json(['path' => $path]);
    } catch (\Exception $e) {
        Log::error('File upload error: ' . $e->getMessage(), [
            'exception' => get_class($e),
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->name('upload');
