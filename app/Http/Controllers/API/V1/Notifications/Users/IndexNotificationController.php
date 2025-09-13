<?php

namespace App\Http\Controllers\API\V1\Notifications\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Notification\User\IndexNotificationRequest;
use App\Http\Resources\Notifications\User\NotificationCollection;
use App\QueryFilters\NotificationReadStatusFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class IndexNotificationController extends Controller
{
    public function __invoke(
        IndexNotificationRequest $request
    ): JsonResponse
    {
        try {
            $perPage = (int) request()->input('per_page', config('pagination.default_per_page'));

            // Start with DatabaseNotification model instead of relationship
            $query = DatabaseNotification::query()
                ->where('notifiable_type', get_class(Auth::user()))
                ->where('notifiable_id', Auth::id());

            $notifications = QueryBuilder::for($query)
                ->allowedFilters([
                    AllowedFilter::custom('read_status', new NotificationReadStatusFilter()),
                ])
                ->allowedSorts(['created_at','read_at'])
                ->defaultSort('created_at')
                ->paginate($perPage)
                ->appends(request()->query());

            $notifications = new NotificationCollection($notifications);

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.notifications')]))
                ->data($notifications)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
