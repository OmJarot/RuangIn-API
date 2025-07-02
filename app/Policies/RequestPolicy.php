<?php

namespace App\Policies;

use App\Models\User;
use App\Models\request;
use Illuminate\Auth\Access\Response;

class RequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, request $request): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->level == "user";
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, request $request): bool
    {
        return $user->id == $request->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, request $request): bool
    {
        return $user->level == "admin";
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, request $request): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, request $request): bool
    {
        return false;
    }
}
