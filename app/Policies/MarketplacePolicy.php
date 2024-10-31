<?php

namespace App\Policies;

use App\Models\Marketplace;
use App\Models\User;

class MarketplacePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(Marketplace $auth_marketplace, Marketplace $marketplace): bool
    {
        return $auth_marketplace->id === $marketplace->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Marketplace $auth_marketplace, Marketplace $marketplace): bool
    {
        return $auth_marketplace->id === $marketplace->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Marketplace $auth_marketplace, Marketplace $marketplace): bool
    {
        return $auth_marketplace->id === $marketplace->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Marketplace $auth_marketplace, Marketplace $marketplace): bool
    {
        return $auth_marketplace->id === $marketplace->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Marketplace $auth_marketplace, Marketplace $marketplace): bool
    {
        return $auth_marketplace->id === $marketplace->id;
    }
}
