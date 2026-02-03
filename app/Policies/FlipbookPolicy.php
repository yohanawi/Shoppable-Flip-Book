<?php

namespace App\Policies;

use App\Models\Flipbook;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FlipbookPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Administrator', 'Customer']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Flipbook $flipbook): bool
    {
        // Administrator can view all, Customer can only view their own
        return $user->hasRole('Administrator') || $flipbook->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['Administrator', 'Customer']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Flipbook $flipbook): bool
    {
        // Administrator can update all, Customer can only update their own
        return $user->hasRole('Administrator') || $flipbook->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Flipbook $flipbook): bool
    {
        // Administrator can delete all, Customer can only delete their own
        return $user->hasRole('Administrator') || $flipbook->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Flipbook $flipbook): bool
    {
        return $user->hasRole('Administrator') || $flipbook->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Flipbook $flipbook): bool
    {
        return $user->hasRole('Administrator');
    }
}
