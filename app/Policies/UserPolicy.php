<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $auth_user, User $user): bool
    {
        return $auth_user->id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $auth_user, User $user): bool
    {
        return $auth_user->id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $auth_user, User $user): bool
    {
        return $auth_user->id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $auth_user, User $user): bool
    {
        return $auth_user->id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $auth_user, User $user): bool
    {
        return $auth_user->id === $user->id;
    }
}
