<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VeterinaryVisit;
use Illuminate\Auth\Access\Response;

class VeterinaryVisitPolicy
{
    /**
     * Determine whether the user can view any models.
     */
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
    public function view(User $user, VeterinaryVisit $veterinaryVisit): bool
    {
        return $user->id === $veterinaryVisit->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VeterinaryVisit $veterinaryVisit): bool
    {
        return $user->id === $veterinaryVisit->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VeterinaryVisit $veterinaryVisit): bool
    {
        return $user->id === $veterinaryVisit->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VeterinaryVisit $veterinaryVisit): bool
    {
        return $user->id === $veterinaryVisit->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VeterinaryVisit $veterinaryVisit): bool
    {
        return $user->id === $veterinaryVisit->user_id;
    }
}
