<?php

namespace App\Http\Controllers\API\V1\Notifications\Users;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamNotificationsController extends Controller
{
    public function __invoke(): StreamedResponse
    {
        $response = new StreamedResponse(function() {
            try {
                $user = Auth::user();

                while (true) {
                    $notifications = $user->getFormattedNotifications();

                    if ($notifications->isNotEmpty()) {
                        foreach ($notifications as $notification) {
                            echo "data: " . json_encode($notification) . "\n\n";
                        }
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                    sleep(1);
                }

            } catch (\Throwable $t) {
                Log::error($t);
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }
        }, Response::HTTP_OK);

        // Set SSE headers
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }
}
