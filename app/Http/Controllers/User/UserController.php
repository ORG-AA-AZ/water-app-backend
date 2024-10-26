<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Sms\ServiceTwilioSms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct(
        private ServiceTwilioSms $sms_service,
    ) {
    }

    public function registerUser(UserRegisterRequest $request)
    {
        try {
            $verification_code = rand(100000, 999999);

            $user = User::create([
                'name' => $request->input('name'),
                'mobile' => $request->input('mobile'),
                'password' => $request->input('password'),
                'mobile_verification_code' => $verification_code,
            ]);

            $this->sms_service->sendVerificationCode($user->mobile, $verification_code);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.mobile_registered_successfully'),
            ], 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => __('messages.failed_to_register'),
            ], 401);
        }
    }

    public function loginUser(UserLoginRequest $request)
    {
        $user = User::where('mobile', $request->input('mobile'))->first();

        if (! $user) {
            return response()->json(['error' => __('messages.mobile_not_registered')], 401);
        }

        if (is_null($user->mobile_verified_at)) {
            return response()->json(['error' => __('messages.mobile_not_verified')], 401);
        }

        $login_using_password = Auth::attempt(['mobile' => $request->input('mobile'), 'password' => $request->input('password')]);
        $login_using_reset_password = Hash::check($request->input('password'), $user->reset_password);

        if (! $login_using_password && ! $login_using_reset_password) {
            return response()->json(['error' => __('messages.invalid_login')], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => __('messages.login_successfully'),
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'token' => $user->createToken('API TOKEN')->plainTextToken,
            ],
        ], 200);
    }

    public function resetUserPassword(UserResetPasswordRequest $request)
    {
        try {
            User::where('mobile', $request->input('mobile'))->first()->update(['password' => $request->input('password')]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.reset_password_successfully'),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => __('messages.fail_process'),
            ], 401);
        }
    }

    public function forgetUserPassword(UserForgetPasswordRequest $request)
    {
        try {
            $user = User::where('mobile', $request->input('mobile'))->first();

            if (! $user) {
                throw new \Exception(__('messages.mobile_not_registered'));
            }

            $user->update(['reset_password' => $reset_password = Str::random(10)]);

            $this->sms_service->sendNewPassword($user->mobile, Hash::make($reset_password));

            return response()->json([
                'status' => 'success',
                'message' => __('messages.sent_new_password'),
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => __('messages.fail_process'),
            ], 401);
        }
    }

    public function logoutUser()
    {
        /** @var User $user */
        $user = Auth::user();

        $user->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.logout'),
        ], 200);
    }

    public function verifyMobile(VerifyRequest $request)
    {
        try {
            $user = User::where('mobile', $request->input('mobile'))->where('mobile_verification_code', $request->input('code'))->first();
            $user->mobile_verified_at = now();
            $user->update(['mobile_verification_code' => null]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.mobile_verified_successfully'),
            ], 200);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => __('messages.invalid_verification'),
            ], 422);
        }
    }

    public function resendVerificationCode(NewVerifyCodeRequest $request)
    {
        try {
            $user = User::where('mobile', $request->input('mobile'))->first();

            $verification_code = rand(100000, 999999);
            $user->update(['mobile_verification_code' => $verification_code]);

            $this->sms_service->sendVerificationCode($user->mobile, $verification_code);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.new_verification_code_sent'),
            ], 200);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => __('messages.invaild_new_verification'),
            ], 422);
        }
    }

    public function setLocation(UserSetLocationRequest $request)
    {
        try {
            $user = User::where('mobile', $request->input('mobile'))->first();
            $user->update(['latitude' => $request->input('latitude'), 'longitude' => $request->input('longitude')]);

            return response()->json(['message' => __('messages.location_located')]);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => __('messages.fail_process'),
            ], 422);
        }
    }
}
