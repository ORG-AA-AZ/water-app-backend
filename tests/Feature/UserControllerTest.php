<?php

namespace Tests\Feature;

use App\Http\Controllers\Services\LoginAndRegisterService;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserRegisterRequest;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\NewVerifyCodeRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyRequest;
use App\Models\User;
use App\Resources\UserResource;
use Database\Factories\UserFactory;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UserController::class)]
#[CoversClass(LoginRequest::class)]
#[CoversClass(ForgetPasswordRequest::class)]
#[CoversClass(ResetPasswordRequest::class)]
#[CoversClass(UserRegisterRequest::class)]
#[CoversClass(UserResource::class)]
#[CoversClass(VerifyRequest::class)]
#[CoversClass(NewVerifyCodeRequest::class)]

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    private Generator $faker;

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

        $this->postJson('/api/user/register', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => __('validation.unique', ['attribute' => 'mobile number']),
                'errors' => [
                    'mobile' => [__('validation.unique', ['attribute' => 'mobile number'])],
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
    }

    public function testFailRegisterUserThrowsException(): void
    {
        $this->faker = Factory::create();

        $data = [
            'name' => $this->faker->name(),
            'mobile' => (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'password' => $password = Str::random(),
            'password_confirmation' => $password,
        ];

        $mocked_service = $this->createMock(LoginAndRegisterService::class);
        $mocked_service->expects($this->once())
            ->method('register')
            ->willThrowException(new \Exception('Registration error'));

        $this->app->instance(LoginAndRegisterService::class, $mocked_service);

        $this->postJson('/api/user/register', $data)
            ->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Registration error',
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
                    'token',
                ],
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
                    'token',
                ],
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
                'status' => 'error',
                'message' => __('messages.invalid_login'),
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
                'status' => 'error',
                'message' => __('messages.mobile_not_verified'),
            ]);
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
            ->assertStatus(200)
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
            ->assertStatus(200)
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
            ->assertStatus(200)
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
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ];

        $this->assertNotEquals($user->latitude, $data['latitude']);
        $this->assertNotEquals($user->longitude, $data['longitude']);

        $this->actingAs($user)
            ->postJson('api/user/set-location', $data)
            ->assertOk()
            ->assertJson(['message' => __('messages.location_located')]);
    }
}
