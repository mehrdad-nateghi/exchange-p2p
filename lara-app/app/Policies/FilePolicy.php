<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\File $file
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, File $file)
    {
        $request = $file->fileable->request;
        $bid = $file->fileable->trade->bid;
        return $request->user_id == $user->id || $bid->user_id == $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\File $file
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, File $file)
    {
        $step = $file->fileable;
        return $step->request->is_user_buyer;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\File $file
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, File $file)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\File $file
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, File $file)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\File $file
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, File $file)
    {
        //
    }
}
