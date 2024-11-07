<?php

namespace Tests\Unit\Policies;

use App\Policies\MarketplacePolicy;
use Database\Factories\MarketplaceFactory;
use Tests\TestCase;

class MarketplacePolicyTest extends TestCase
{
    public function testViewPolicyAllowsOwnMarketplace()
    {
        $auth_marketplace = MarketplaceFactory::new()->createOne();
        $policy = new MarketplacePolicy();

        $this->assertTrue(
            $policy->view($auth_marketplace, $auth_marketplace),
            'The view policy should allow the user to view their own marketplace.'
        );
    }

    public function testViewPolicyDeniesOtherMarketplace()
    {
        $auth_marketplace = MarketplaceFactory::new()->createOne();
        $otherMarketplace = MarketplaceFactory::new()->createOne();
        $policy = new MarketplacePolicy();

        $this->assertFalse(
            $policy->view($auth_marketplace, $otherMarketplace),
            'The view policy should deny the user from viewing another marketplace.'
        );
    }

    public function testUpdatePolicyAllowsOwnMarketplace()
    {
        $auth_marketplace = MarketplaceFactory::new()->createOne();
        $policy = new MarketplacePolicy();

        $this->assertTrue(
            $policy->update($auth_marketplace, $auth_marketplace),
            'The update policy should allow the user to update their own marketplace.'
        );
    }

    public function testUpdatePolicyDeniesOtherMarketplace()
    {
        $auth_marketplace = MarketplaceFactory::new()->createOne();
        $otherMarketplace = MarketplaceFactory::new()->createOne();
        $policy = new MarketplacePolicy();

        $this->assertFalse(
            $policy->update($auth_marketplace, $otherMarketplace),
            'The update policy should deny the user from updating another marketplace.'
        );
    }

    public function testDeletePolicyAllowsOwnMarketplace()
    {
        $auth_marketplace = MarketplaceFactory::new()->createOne();
        $policy = new MarketplacePolicy();

        $this->assertTrue(
            $policy->delete($auth_marketplace, $auth_marketplace),
            'The delete policy should allow the user to delete their own marketplace.'
        );
    }

    public function testDeletePolicyDeniesOtherMarketplace()
    {
        $auth_marketplace = MarketplaceFactory::new()->createOne();
        $otherMarketplace = MarketplaceFactory::new()->createOne();
        $policy = new MarketplacePolicy();

        $this->assertFalse(
            $policy->delete($auth_marketplace, $otherMarketplace),
            'The delete policy should deny the user from deleting another marketplace.'
        );
    }

    public function testRestorePolicyAllowsOwnMarketplace()
    {
        $auth_marketplace = MarketplaceFactory::new()->createOne();
        $policy = new MarketplacePolicy();

        $this->assertTrue(
            $policy->restore($auth_marketplace, $auth_marketplace),
            'The restore policy should allow the user to restore their own marketplace.'
        );
    }

    public function testRestorePolicyDeniesOtherMarketplace()
    {
        $auth_marketplace = MarketplaceFactory::new()->createOne();
        $otherMarketplace = MarketplaceFactory::new()->createOne();
        $policy = new MarketplacePolicy();

        $this->assertFalse(
            $policy->restore($auth_marketplace, $otherMarketplace),
            'The restore policy should deny the user from restoring another marketplace.'
        );
    }

    public function testForceDeletePolicyAllowsOwnMarketplace()
    {
        $auth_marketplace = MarketplaceFactory::new()->createOne();
        $policy = new MarketplacePolicy();

        $this->assertTrue(
            $policy->forceDelete($auth_marketplace, $auth_marketplace),
            'The forceDelete policy should allow the user to permanently delete their own marketplace.'
        );
    }

    public function testForceDeletePolicyDeniesOtherMarketplace()
    {
        $auth_marketplace = MarketplaceFactory::new()->createOne();
        $otherMarketplace = MarketplaceFactory::new()->createOne();
        $policy = new MarketplacePolicy();

        $this->assertFalse(
            $policy->forceDelete($auth_marketplace, $otherMarketplace),
            'The forceDelete policy should deny the user from permanently deleting another marketplace.'
        );
    }
}
