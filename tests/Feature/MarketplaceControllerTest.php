<?php

namespace Tests\Feature;

use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\Marketplace\MarketplaceLoginRequest;
use App\Http\Controllers\Marketplace\MarketplaceResetPasswordRequest;
use App\Models\Marketplace;
use App\Resources\MarketplaceResource;
use Database\Factories\MarketplaceFactory;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(MarketplaceController::class)]
#[CoversClass(MarketplaceLoginRequest::class)]
#[CoversClass(MarketplaceResetPasswordRequest::class)]
#[CoversClass(MarketplaceResource::class)]

class MarketplaceControllerTest extends TestCase
{
    use RefreshDatabase;
    private Generator $faker;

    public function testStoreMarketplace(): void
    {
        $this->faker = Factory::create();

        $data = [
            'national_id' => $national_id = (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'name' => $name = $this->faker->name(),
            'mobile' => $mobile = (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'password' => $password = Str::random(),
            'password_confirmation' => $password,
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ];

        $this->postJson('/api/marketplace/register', $data)
            ->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.national_id_registered_successfully'),
            ]);

        $this->assertDatabaseHas('marketplaces', [
            'national_id' => $national_id,
            'name' => $name,
            'mobile' => $mobile,
        ]);

        $marketplace = Marketplace::where('mobile', $mobile)->first();

        $this->assertTrue(Hash::check($password, $marketplace->password));
    }

    public function testFailStoreMarketplaceNoneConfirmedPassword(): void
    {
        $this->faker = Factory::create();

        $data = [
            'national_id' => (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'name' => $this->faker->name(),
            'mobile' => (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'password' => Str::random(),
            'password_confirmation' => Str::random(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ];

        $this->postJson('/api/marketplace/register', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The password confirmation does not match.',
                'errors' => [
                    'password' => ['The password confirmation does not match.'],
                ],
            ]);
    }

    public function testFailStoreExistMarketplaceNationalId(): void
    {
        $this->faker = Factory::create();
        $marketplace = MarketplaceFactory::new()->createOne();

        $data = [
            'national_id' => (string) $marketplace->national_id,
            'name' => $this->faker->name(),
            'mobile' => (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'password' => $password = Str::random(),
            'password_confirmation' => $password,
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ];

        $national_id_attribute = App::getLocale() === 'ar' ? 'الرقم الوطني للمنشأة' : 'national ID';

        $this->postJson('/api/marketplace/register', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.unique', ['attribute' => $national_id_attribute]),
                'errors' => [
                    'national_id' => [__('messages.unique', ['attribute' => $national_id_attribute])],
                ],
            ]);
    }

    public function testLogoutSuccessfully(): void
    {
        $marketplace = MarketplaceFactory::new()->createOne();
        $token = $marketplace->createToken('API TOKEN')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/marketplace/logout')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.logout'),
            ]);

        $this->assertCount(0, $marketplace->tokens);
        $this->assertCount(0, $marketplace->tokens);
    }

    public function testLogoutUnauthenticatedUser(): void
    {
        $this->deleteJson('/api/marketplace/logout')
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
