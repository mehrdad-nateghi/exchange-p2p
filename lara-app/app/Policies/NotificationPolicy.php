<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Notifications\DatabaseNotification;

class NotificationPolicy
{
    use HandlesAuthorization;

//    /**
//     * Determine whether the user can view any models.
//     *
//     * @param  \App\Models\User  $user
//     * @return \Illuminate\Auth\Access\Response|bool
//     */
//    public function viewAny(User $user)
//    {
//        //
//    }

    public function view(User $user, DatabaseNotification $notification)
    {
        return $notification->notifiable_id === $user->id;
    }

//    /**
//     * Determine whether the user can create models.
//     *
//     * @param  \App\Models\User  $user
//     * @return \Illuminate\Auth\Access\Response|bool
//     */
//    public function create(User $user)
//    {
//        //
//    }

//    /**
//     * Determine whether the user can update the model.
//     *
//     * @param  \App\Models\User  $user
//     * @param  \App\Models\Notification  $notification
//     * @return \Illuminate\Auth\Access\Response|bool
//     */
//    public function update(User $user, Notification $notification)
//    {
//        //
//    }
//
//    /**
//     * Determine whether the user can delete the model.
//     *
//     * @param  \App\Models\User  $user
//     * @param  \App\Models\Notification  $notification
//     * @return \Illuminate\Auth\Access\Response|bool
//     */
//    public function delete(User $user, Notification $notification)
//    {
//        //
//    }
//
//    /**
//     * Determine whether the user can restore the model.
//     *
//     * @param  \App\Models\User  $user
//     * @param  \App\Models\Notification  $notification
//     * @return \Illuminate\Auth\Access\Response|bool
//     */
//    public function restore(User $user, Notification $notification)
//    {
//        //
//    }
//
//    /**
//     * Determine whether the user can permanently delete the model.
//     *
//     * @param  \App\Models\User  $user
//     * @param  \App\Models\Notification  $notification
//     * @return \Illuminate\Auth\Access\Response|bool
//     */
//    public function forceDelete(User $user, Notification $notification)
//    {
//        //
//    }
}
