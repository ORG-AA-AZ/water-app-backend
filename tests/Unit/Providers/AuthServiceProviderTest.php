<?php

namespace Tests\Unit\Providers;

use App\Models\Marketplace;
use App\Models\User;
use App\Providers\AuthServiceProvider;
use App\Policies\MarketplacePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AuthServiceProviderTest extends TestCase
{
    public function testPoliciesAreRegistered()
    {
        // Register the provider
        $provider = new AuthServiceProvider($this->app);
        $provider->boot();

        // Assert that the policies are registered correctly
        $this->assertInstanceOf(UserPolicy::class, Gate::getPolicyFor(User::class));
        $this->assertInstanceOf(MarketplacePolicy::class, Gate::getPolicyFor(Marketplace::class));
    }
}
