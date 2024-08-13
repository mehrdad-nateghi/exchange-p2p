<?php

namespace App\Policies;

use App\Enums\BidStatusEnum;
use App\Models\Request;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Request  $request
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Request $request)
    {
        return $user->id === $request->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Request  $request
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Request $request)
    {
        return 'dasdasdasd';
        // Check if the user has an accepted bid for this request
        $acceptedBid = $request->bids()
            ->where('user_id', $user->id)
            ->where('status', BidStatusEnum::ACCEPTED->value)
            ->first();

        // User can update if they have an accepted bid
        return $acceptedBid !== null;

        return $this->user->id === $request->bids()->where('user_id', $this->user->id)->where('status', BidStatusEnum::ACCEPTED->value)->first()->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Request  $request
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Request $request)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Request  $request
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Request $request)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Request  $request
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Request $request)
    {
        //
    }
}
