<?php

namespace Tests\Feature\Controllers;

use App\Enums\PlaceOfLocation;
use App\Http\Controllers\User\MarketplaceRateAndReviewRequest;
use App\Http\Controllers\User\NewVerifyCodeRequest;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserForgetPasswordRequest;
use App\Http\Controllers\User\UserLoginRequest;
use App\Http\Controllers\User\UserRegisterRequest;
use App\Http\Controllers\User\UserResetPasswordRequest;
use App\Http\Controllers\User\UserSetLocationRequest;
use App\Http\Controllers\User\VerifyRequest;
use App\Http\Middleware\EnsureMobileIsVerified;
use App\Models\User;
use App\Resources\UserResource;
use Database\Factories\MarketplaceFactory;
use Database\Factories\UserFactory;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UserController::class)]
#[CoversClass(UserLoginRequest::class)]
#[CoversClass(UserForgetPasswordRequest::class)]
#[CoversClass(UserResetPasswordRequest::class)]
#[CoversClass(UserRegisterRequest::class)]
#[CoversClass(UserResource::class)]
#[CoversClass(VerifyRequest::class)]
#[CoversClass(NewVerifyCodeRequest::class)]
#[CoversClass(UserSetLocationRequest::class)]
#[CoversClass(MarketplaceRateAndReviewRequest::class)]
#[CoversClass(EnsureMobileIsVerified::class)]

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    private Generator $faker;

    public function testUnverifiedUserTryToMakeSomehtingNeedsVerify(): void
    {
        $user = UserFactory::new()->createOne();

        // Any route needs the user to be verified
        $this->actingAs($user)
            ->postJson('api/user/set-location', [])
            ->assertStatus(403)
            ->assertJson(['message' => __('messages.mobile_not_verified')]);
    }

    public function testRegisterUser(): void
    {
        $this->faker = Factory::create();

        $data = [
            'name' => $name = $this->faker->name(),
            'mobile' => $mobile = (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'password' => $password = Str::random(),
            'password_confirmation' => $password,
        ];

        $this->postJson('/api/user/register', $data)
            ->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.mobile_registered_successfully'),
            ]);

        App::setLocale('ar');

        $data = [
            'name' => $name = $this->faker->name(),
            'mobile' => $mobile = (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'password' => $password = Str::random(),
            'password_confirmation' => $password,
        ];

        $this->postJson('/api/user/register', $data)
            ->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.mobile_registered_successfully'),
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'mobile' => $mobile,
        ]);

        $user = User::where('mobile', $mobile)->first();

        $this->assertNotNull($user->mobile_verification_code);
        $this->assertNull($user->mobile_verified_at);
        $this->assertTrue(Hash::check($password, $user->password));
    }

    public function testFailRegisterUserWithExistMobile(): void
    {
        $this->faker = Factory::create();
        $user = UserFactory::new()->verified()->createOne();

        $data = [
            'name' => $this->faker->name(),
            'mobile' => $user->mobile,
            'password' => $password = Str::random(),
            'password_confirmation' => $password,
        ];

        $mobile_attribute = App::getLocale() === 'ar' ? 'رقم الهاتف المحمول' : 'mobile number';

        $this->postJson('/api/user/register', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.unique', ['attribute' => $mobile_attribute]),
                'errors' => [
                    'mobile' => [__('messages.unique', ['attribute' => $mobile_attribute])],
                ],
            ]);

        App::setLocale('en');

        $this->postJson('/api/user/register', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.unique', ['attribute' => $mobile_attribute]),
                'errors' => [
                    'mobile' => [__('messages.unique', ['attribute' => $mobile_attribute])],
                ],
            ]);
    }

    public function testFailRegisterUserNoneConfirmedPassword(): void
    {
        $this->faker = Factory::create();

        $data = [
            'name' => $this->faker->name(),
            'mobile' => (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'password' => Str::random(),
            'password_confirmation' => Str::random(),
        ];

        $this->postJson('/api/user/register', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The password confirmation does not match.',
                'errors' => [
                    'password' => ['The password confirmation does not match.'],
                ],
            ]);

        App::setLocale('ar');

        $this->postJson('/api/user/register', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The password confirmation does not match.',
                'errors' => [
                    'password' => ['The password confirmation does not match.'],
                ],
            ]);
    }

    public function testLoginUser(): void
    {
        $user = UserFactory::new()->verified()->createOne();

        $data = [
            'mobile' => $user->mobile,
            'password' => 'password',
        ];

        $this->postJson('/api/user/login', $data)
            ->assertOk()
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'mobile',
                ],
                'status',
                'message',
                'token',
            ]);

        App::setLocale('ar');

        $this->postJson('/api/user/login', $data)
            ->assertOk()
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
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
        $user = UserFactory::new()->verified()->setResetPassword()->createOne();

        $data = [
            'mobile' => $user->mobile,
            'password' => 'reset_password',
        ];

        $this->postJson('/api/user/login', $data)
            ->assertOk()
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'mobile',
                ],
                'status',
                'message',
                'token',
            ]);

        App::setLocale('ar');

        $this->postJson('/api/user/login', $data)
            ->assertOk()
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
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
        $user = UserFactory::new()->verified()->createOne();

        $data = [
            'mobile' => $user->mobile,
            'password' => Str::random(8),
        ];

        $this->postJson('/api/user/login', $data)
            ->assertStatus(401)
            ->assertJson([
                'error' => __('messages.invalid_login'),
            ]);

        App::setLocale('ar');

        $this->postJson('/api/user/login', $data)
            ->assertStatus(401)
            ->assertJson([
                'error' => __('messages.invalid_login'),
            ]);
    }

    public function testInvalidLoginUserIfMobileNotVerified(): void
    {
        $user = UserFactory::new()->createOne();

        $data = [
            'mobile' => $user->mobile,
            'password' => 'password',
        ];

        $this->postJson('/api/user/login', $data)
            ->assertStatus(401)
            ->assertJson([
                'error' => __('messages.mobile_not_verified'),
            ]);

        App::setLocale('ar');

        $this->postJson('/api/user/login', $data)
            ->assertStatus(401)
            ->assertJson([
                'error' => __('messages.mobile_not_verified'),
            ]);
    }

    public function testForgetPassword(): void
    {
        $user = UserFactory::new()->verified()->createOne();

        $data = [
            'mobile' => $user->mobile,
        ];

        $this->postJson('/api/user/forget-password', $data)
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.sent_new_password'),
            ]);

        App::setLocale('ar');

        $this->postJson('/api/user/forget-password', $data)
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
            'mobile' => (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
        ];

        $this->postJson('/api/user/forget-password', $data)
            ->assertStatus(401)
            ->assertJson([
                'error' => __('messages.fail_process'),
            ]);

        App::setLocale('ar');

        $this->postJson('/api/user/forget-password', $data)
            ->assertStatus(401)
            ->assertJson([
                'error' => __('messages.fail_process'),
            ]);
    }

    public function testResetPassword(): void
    {
        $this->faker = Factory::create();
        $user = UserFactory::new()->verified()->createOne();

        $data = [
            'mobile' => $user->mobile,
            'password' => $password = Str::random(),
            'password_confirmation' => $password,
        ];

        $this->actingAs($user)
            ->postJson('/api/user/reset-password', $data)
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.reset_password_successfully'),
            ]);

        App::setLocale('ar');

        $this->actingAs($user)
            ->postJson('/api/user/reset-password', $data)
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.reset_password_successfully'),
            ]);

        $user = User::where('mobile', $user->mobile)->first();

        $this->assertTrue(Hash::check($password, $user->password));
    }

    public function testVerifyMobileNumber(): void
    {
        $user = UserFactory::new()->createOne();

        $data = [
            'mobile' => $user->mobile,
            'code' => $user->mobile_verification_code,
        ];

        $this->postJson('/api/user/verify-mobile', $data)
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.mobile_verified_successfully'),
            ]);

        App::setLocale('ar');

        $user = UserFactory::new()->createOne();

        $data = [
            'mobile' => $user->mobile,
            'code' => $user->mobile_verification_code,
        ];

        $this->postJson('/api/user/verify-mobile', $data)
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.mobile_verified_successfully'),
            ]);
    }

    public function testResendVerifyCodeToMobileNumber(): void
    {
        $user = UserFactory::new()->createOne();

        $data = [
            'mobile' => $user->mobile,
        ];

        $this->postJson('/api/user/resend-verify-code', $data)
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.new_verification_code_sent'),
            ]);

        App::setLocale('ar');

        $this->postJson('/api/user/resend-verify-code', $data)
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.new_verification_code_sent'),
            ]);
    }

    public function testLogoutSuccessfully(): void
    {
        $user = UserFactory::new()->verified()->createOne();
        $user->createToken('API TOKEN')->plainTextToken;

        $this->actingAs($user)->deleteJson('/api/user/logout')
            ->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.logout'),
            ]);

        $this->assertCount(0, $user->tokens);
    }

    public function testLogoutUnauthenticatedUser(): void
    {
        $this->deleteJson('/api/user/logout')
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function testSetUserLocation(): void
    {
        $this->faker = Factory::create();
        $user = UserFactory::new()->verified()->createOne();

        $data = [
            'mobile' => $user->mobile,
            'place' => PlaceOfLocation::Work,
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ];

        $this->assertNull($user->location);

        $this->actingAs($user)
            ->postJson('api/user/set-location', $data)
            ->assertOk()
            ->assertJson(['message' => __('messages.location_located')]);

        App::setLocale('ar');

        $this->actingAs($user)
            ->postJson('api/user/set-location', $data)
            ->assertOk()
            ->assertJson(['message' => __('messages.location_located')]);
    }

    public function testFailSetUserLocationIfOneOfCoordinatesMissing(): void
    {
        $this->faker = Factory::create();
        $user = UserFactory::new()->verified()->createOne();

        $data = [
            'mobile' => $user->mobile,
            'place' => PlaceOfLocation::Home,
            'longitude' => $this->faker->longitude(),
        ];

        $latitude_attribute = App::getLocale() === 'ar' ? 'خط العرض' : 'latitude';

        $this->actingAs($user)
            ->postJson('api/user/set-location', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.required', ['attribute' => $latitude_attribute]),
                'errors' => [
                    'latitude' => [__('messages.required', ['attribute' => $latitude_attribute])],
                ],
            ]);

        $data = [
            'mobile' => $user->mobile,
            'place' => PlaceOfLocation::Home,
            'latitude' => $this->faker->latitude(),
        ];

        $longitude_attribute = App::getLocale() === 'ar' ? 'خط الطول' : 'longitude';

        $this->actingAs($user)
            ->postJson('api/user/set-location', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.required', ['attribute' => $longitude_attribute]),
                'errors' => [
                    'longitude' => [__('messages.required', ['attribute' => $longitude_attribute])],
                ],
            ]);
    }

    public function testSetReviewAsUser(): void
    {
        $this->faker = Factory::create();
        $user = UserFactory::new()->verified()->createOne();
        $marketplace = MarketplaceFactory::new()->createOne();

        $data = [
            'national_id' => $marketplace->national_id,
            'mobile' => $user->mobile,
            'rate' => 4,
            'review' => 'very good',
        ];

        $this->actingAs($user)
            ->postJson('api/user/set-rate-and-review', $data)
            ->assertOk()
            ->assertJson([
                'message' => __('messages.feedback_sent'),
            ]);

        App::setLocale('ar');

        $this->actingAs($user)
            ->postJson('api/user/set-rate-and-review', $data)
            ->assertOk()
            ->assertJson([
                'message' => __('messages.feedback_sent'),
            ]);
    }

    public function testFailSetReviewToMarketplaceIfUnauthorized(): void
    {
        $this->faker = Factory::create();
        $user = UserFactory::new()->verified()->createOne();

        $data = [
            'national_id' => (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'mobile' => $user->mobile,
            'rate' => 4,
            'review' => 'very good',
        ];

        $this->actingAs($user)
            ->postJson('api/user/set-rate-and-review', $data)
            ->assertStatus(403)
            ->assertJson([
                'message' => 'This action is unauthorized.',
            ]);
    }
}
