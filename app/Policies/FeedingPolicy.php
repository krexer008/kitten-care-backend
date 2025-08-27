<?php

namespace App\Policies;

use App\Models\Feeding;
use App\Models\User;

class FeedingPolicy
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
    public function view(User $user, Feeding $feeding): bool
    {
        return $user->id === $feeding->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Feeding $feeding): bool
    {
        return $user->id === $feeding->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Feeding $feeding): bool
    {
        return $user->id === $feeding->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Feeding $feeding): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Feeding $feeding): bool
    {
        return false;
    }
}
