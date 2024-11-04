<?php

namespace Tests\Unit\Policies;

use App\Policies\UserPolicy;
use Database\Factories\UserFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function testViewPolicyAllowsOwnProfile()
    {
        $auth_user = UserFactory::new()->verified()->createOne();
        $policy = new UserPolicy();

        $this->assertTrue(
            $policy->view($auth_user, $auth_user),
            'The view policy should allow the user to view their own profile.'
        );
    }

    public function testViewPolicyDeniesOtherProfile()
    {
        $auth_user = UserFactory::new()->verified()->createOne();
        $otherUser = UserFactory::new()->verified()->createOne();
        $policy = new UserPolicy();

        $this->assertFalse(
            $policy->view($auth_user, $otherUser),
            'The view policy should deny the user from viewing another user\'s profile.'
        );
    }

    public function testUpdatePolicyAllowsOwnProfile()
    {
        $auth_user = UserFactory::new()->verified()->createOne();
        $policy = new UserPolicy();

        $this->assertTrue(
            $policy->update($auth_user, $auth_user),
            'The update policy should allow the user to update their own profile.'
        );
    }

    public function testUpdatePolicyDeniesOtherProfile()
    {
        $auth_user = UserFactory::new()->verified()->createOne();
        $otherUser = UserFactory::new()->verified()->createOne();
        $policy = new UserPolicy();

        $this->assertFalse(
            $policy->update($auth_user, $otherUser),
            'The update policy should deny the user from updating another user\'s profile.'
        );
    }

    public function testDeletePolicyAllowsOwnProfile()
    {
        $auth_user = UserFactory::new()->verified()->createOne();
        $policy = new UserPolicy();

        $this->assertTrue(
            $policy->delete($auth_user, $auth_user),
            'The delete policy should allow the user to delete their own profile.'
        );
    }

    public function testDeletePolicyDeniesOtherProfile()
    {
        $auth_user = UserFactory::new()->verified()->createOne();
        $otherUser = UserFactory::new()->verified()->createOne();
        $policy = new UserPolicy();

        $this->assertFalse(
            $policy->delete($auth_user, $otherUser),
            'The delete policy should deny the user from deleting another user\'s profile.'
        );
    }

    public function testRestorePolicyAllowsOwnProfile()
    {
        $auth_user = UserFactory::new()->verified()->createOne();
        $policy = new UserPolicy();

        $this->assertTrue(
            $policy->restore($auth_user, $auth_user),
            'The restore policy should allow the user to restore their own profile.'
        );
    }

    public function testRestorePolicyDeniesOtherProfile()
    {
        $auth_user = UserFactory::new()->verified()->createOne();
        $otherUser = UserFactory::new()->verified()->createOne();
        $policy = new UserPolicy();

        $this->assertFalse(
            $policy->restore($auth_user, $otherUser),
            'The restore policy should deny the user from restoring another user\'s profile.'
        );
    }

    public function testForceDeletePolicyAllowsOwnProfile()
    {
        $auth_user = UserFactory::new()->verified()->createOne();
        $policy = new UserPolicy();

        $this->assertTrue(
            $policy->forceDelete($auth_user, $auth_user),
            'The forceDelete policy should allow the user to permanently delete their own profile.'
        );
    }

    public function testForceDeletePolicyDeniesOtherProfile()
    {
        $auth_user = UserFactory::new()->verified()->createOne();
        $otherUser = UserFactory::new()->verified()->createOne();
        $policy = new UserPolicy();

        $this->assertFalse(
            $policy->forceDelete($auth_user, $otherUser),
            'The forceDelete policy should deny the user from permanently deleting another user\'s profile.'
        );
    }
}
