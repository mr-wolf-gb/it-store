<?php

namespace App\Policies;

use App\Models\SupportResource;
use App\Models\User;

class SupportResourcePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, SupportResource $supportResource): bool
    {
        if ($supportResource->status !== 'published') {
            return $user?->can('resources.manage') ?? false;
        }

        if ($supportResource->visibility === 'public') {
            return true;
        }

        if ($user === null) {
            return false;
        }

        return $user->can('resources.view_private')
            || $user->can('resources.download_private')
            || $user->can('resources.manage');
    }

    /**
     * Determine whether the user can download or open the resource.
     */
    public function download(?User $user, SupportResource $supportResource): bool
    {
        return $this->view($user, $supportResource);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('resources.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SupportResource $supportResource): bool
    {
        return $user->can('resources.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SupportResource $supportResource): bool
    {
        return $user->can('resources.delete') || $user->can('resources.manage');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SupportResource $supportResource): bool
    {
        return $user->can('resources.manage');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SupportResource $supportResource): bool
    {
        return $user->can('resources.manage');
    }
}
