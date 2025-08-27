<?php

namespace App\Policies;

use App\Models\Cat;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CatPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cat $cat): bool
    {
        return $user->id === $cat->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Cat $cat): bool
    {
        return $user->id === $cat->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cat $cat): bool
    {
        return $user->id === $cat->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Cat $cat): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Cat $cat): bool
    {
        return false;
    }
}
