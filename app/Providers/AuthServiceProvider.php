<?php

namespace App\Providers;

use App\Models\Marketplace;
use App\Models\Product;
use App\Models\User;
use App\Policies\MarketplacePolicy;
use App\Policies\ProductPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Marketplace::class => MarketplacePolicy::class,
        Product::class => ProductPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
