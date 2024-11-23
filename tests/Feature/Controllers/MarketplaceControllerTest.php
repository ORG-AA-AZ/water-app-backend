<?php

namespace Tests\Feature\Controllers;

use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\Marketplace\MarketplaceDescriptionRequest;
use App\Http\Controllers\Marketplace\MarketplaceForgetPasswordRequest;
use App\Http\Controllers\Marketplace\MarketplaceLoginRequest;
use App\Http\Controllers\Marketplace\MarketplaceRegisterRequest;
use App\Http\Controllers\Marketplace\MarketplaceResetPasswordRequest;
use App\Http\Controllers\Marketplace\MarketplaceSetLocationRequest;
use App\Http\Middleware\EnsureMarketplaceIsActive;
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
#[CoversClass(MarketplaceRegisterRequest::class)]
#[CoversClass(MarketplaceForgetPasswordRequest::class)]
#[CoversClass(MarketplaceDescriptionRequest::class)]
#[CoversClass(MarketplaceSetLocationRequest::class)]
#[CoversClass(MarketplaceResetPasswordRequest::class)]
#[CoversClass(MarketplaceResource::class)]
#[CoversClass(EnsureMarketplaceIsActive::class)]

class MarketplaceControllerTest extends TestCase
{
    use RefreshDatabase;
    private Generator $faker;

    public function testInactiveMarketplaceTryToMakeSomehtingNeedsToBeActive(): void
    {
        $marketplace = MarketplaceFactory::new()->setInactive()->createOne();

        $this->actingAs($marketplace)
            ->postJson('api/marketplace/set-location', [])
            ->assertStatus(403)
            ->assertJson(['message' => __('messages.inactive_marketplace')]);
    }

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

        App::setLocale('ar');

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

    public function testLoginUser(): void
    {
        $marketplace = MarketplaceFactory::new()->createOne();

        $data = [
            'national_id' => $marketplace->national_id,
            'password' => 'password',
        ];

        $this->postJson('/api/marketplace/login', $data)
            ->assertOk()
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'national_id',
                    'name',
                    'mobile',
                ],
                'status',
                'message',
                'token',
            ]);

        App::setLocale('ar');

        $this->postJson('/api/marketplace/login', $data)
            ->assertOk()
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'national_id',
                    'name',
                    'mobile',
                ],
                'status',
                'message',
                'token',
            ]);
    }

    public function testLoginUserWithResetPassword(): void
    {
        $marketplace = MarketplaceFactory::new()->setResetPassword()->createOne();

        $data = [
            'national_id' => $marketplace->national_id,
            'password' => 'reset_password',
        ];

        $this->postJson('/api/marketplace/login', $data)
            ->assertOk()
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'national_id',
                    'name',
                    'mobile',
                ],
                'status',
                'message',
                'token',
            ]);

        App::setLocale('ar');

        $this->postJson('/api/marketplace/login', $data)
            ->assertOk()
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'national_id',
                    'name',
                    'mobile',
                ],
                'status',
                'message',
                'token',
            ]);
    }

    public function testInvalidLoginUserIfUserNotExistOrIncorrectPassword(): void
    {
        $marketplace = MarketplaceFactory::new()->createOne();

        $data = [
            'national_id' => $marketplace->national_id,
            'password' => Str::random(8),
        ];


        $this->postJson('/api/marketplace/login', $data)
            ->assertStatus(401)
            ->assertJson([
                'error' => __('messages.invalid_login'),
            ]);

        App::setLocale('ar');

        $this->postJson('/api/marketplace/login', $data)
            ->assertStatus(401)
            ->assertJson([
                'error' => __('messages.invalid_login'),
            ]);
    }

    public function testInvalidLoginUserIfnationalIdNotActive(): void
    {
        $marketplace = MarketplaceFactory::new()->setInactive()->createOne();

        $data = [
            'national_id' => $marketplace->national_id,
            'password' => 'password',
        ];

        $this->postJson('/api/marketplace/login', $data)
            ->assertStatus(401)
            ->assertJson([
                'error' => __('messages.national_id_not_registered'),
            ]);

        App::setLocale('ar');

        $this->postJson('/api/marketplace/login', $data)
            ->assertStatus(401)
            ->assertJson([
                'error' => __('messages.national_id_not_registered'),
            ]);
    }

    public function testForgetPassword(): void
    {
        $marketplace = MarketplaceFactory::new()->createOne();

        $data = [
            'national_id' => $marketplace->national_id,
        ];

        $this->postJson('/api/marketplace/forget-password', $data)
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.sent_new_password'),
            ]);

        App::setLocale('ar');

        $this->postJson('/api/marketplace/forget-password', $data)
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.sent_new_password'),
            ]);
    }

    public function testForgetPasswordForNoneExistUser(): void
    {
        $this->faker = Factory::create();

        $data = [
            'national_id' => (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
        ];

        $this->postJson('/api/marketplace/forget-password', $data)
            ->assertStatus(401)
            ->assertJson([
                'error' => __('messages.fail_process'),
            ]);

        App::setLocale('ar');

        $this->postJson('/api/marketplace/forget-password', $data)
            ->assertStatus(401)
            ->assertJson([
                'error' => __('messages.fail_process'),
            ]);
    }

    public function testResetPassword(): void
    {
        $this->faker = Factory::create();
        $marketplace = MarketplaceFactory::new()->createOne();

        $data = [
            'national_id' => $marketplace->national_id,
            'mobile' => $marketplace->mobile,
            'password' => $password = Str::random(),
            'password_confirmation' => $password,
        ];

        $this->actingAs($marketplace)
            ->postJson('/api/marketplace/reset-password', $data)
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.reset_password_successfully'),
            ]);

        App::setLocale('ar');

        $this->actingAs($marketplace)
            ->postJson('/api/marketplace/reset-password', $data)
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.reset_password_successfully'),
            ]);

        $marketplace = Marketplace::where('national_id', $marketplace->national_id)->first();

        $this->assertTrue(Hash::check($password, $marketplace->password));
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

    public function testSetDescription(): void
    {
        $this->faker = Factory::create();
        $marketplace = MarketplaceFactory::new()->createOne();

        $data = [
            'national_id' => $marketplace->national_id,
            'description' => $marketplace->description,
        ];

        $this->actingAs($marketplace)
            ->postJson('api/marketplace/set-description', $data)
            ->assertOk()
            ->assertJson([
                'message' => __('messages.description_updated'),
            ]);

        App::setLocale('ar');

        $this->actingAs($marketplace)
            ->postJson('api/marketplace/set-description', $data)
            ->assertOk()
            ->assertJson([
                'message' => __('messages.description_updated'),
            ]);
    }

    public function testSetUserLocation(): void
    {
        $this->faker = Factory::create();
        $marketplace = MarketplaceFactory::new()->createOne();

        $data = [
            'national_id' => $marketplace->national_id,
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ];

        $this->assertNotEquals($marketplace->latitude, $data['latitude']);
        $this->assertNotEquals($marketplace->longitude, $data['longitude']);

        $this->actingAs($marketplace)
            ->postJson('api/marketplace/set-location', $data)
            ->assertOk()
            ->assertJson(['message' => __('messages.location_located')]);

        App::setLocale('ar');

        $this->actingAs($marketplace)
            ->postJson('api/marketplace/set-location', $data)
            ->assertOk()
            ->assertJson(['message' => __('messages.location_located')]);
    }

    public function testFailSetUserLocationIfOneOfCoordinatesMissing(): void
    {
        $this->faker = Factory::create();
        $marketplace = MarketplaceFactory::new()->createOne();

        $data = [
            'national_id' => $marketplace->national_id,
            'longitude' => $this->faker->longitude(),
        ];

        $latitude_attribute = App::getLocale() === 'ar' ? 'خط العرض' : 'latitude';

        $this->actingAs($marketplace)
            ->postJson('api/marketplace/set-location', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.required', ['attribute' => $latitude_attribute]),
                'errors' => [
                    'latitude' => [__('messages.required', ['attribute' => $latitude_attribute])],
                ],
            ]);

        $data = [
            'national_id' => $marketplace->national_id,
            'latitude' => $this->faker->latitude(),
        ];

        $longitude_attribute = App::getLocale() === 'ar' ? 'خط الطول' : 'longitude';

        $this->actingAs($marketplace)
            ->postJson('api/marketplace/set-location', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.required', ['attribute' => $longitude_attribute]),
                'errors' => [
                    'longitude' => [__('messages.required', ['attribute' => $longitude_attribute])],
                ],
            ]);
    }
}
