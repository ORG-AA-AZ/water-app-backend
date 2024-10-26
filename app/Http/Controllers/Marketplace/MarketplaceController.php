<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Marketplace;
use App\Services\Sms\ServiceTwilioSms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MarketplaceController extends Controller
{
    public function __construct(
        private ServiceTwilioSms $sms_service,
    ) {
    }

    public function registerMarketplace(MarketplaceRegisterRequest $request)
    {
        try {
            $marketplace = Marketplace::create([
                'name' => $request->input('name'),
                'mobile' => $request->input('mobile'),
                'national_id' => $request->input('national_id'),
                'password' => $request->input('password'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.national_id_registered_successfully'),
                'data' => [
                    'id' => $marketplace->id,
                    'name' => $marketplace->name,
                    'national_id' => $marketplace->national_id,
                    'mobile' => $marketplace->mobile,
                    'token' => $marketplace->createToken('API TOKEN')->plainTextToken,
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => __('messages.failed_to_register'),
            ], 401);
        }
    }

    public function loginMarketplace(MarketplaceLoginRequest $request)
    {
        $marketplace = Marketplace::where('national_id', $request->input('national_id'))->first();

        if (! $marketplace || ! $marketplace->is_active) {
            throw new \Exception(__('messages.national_id_not_registered'));
        }

        $login_using_password = Auth::attempt(['national_id' => $request->input('national_id'), 'password' => $request->input('password')]);
        $login_using_reset_password = Hash::check($request->input('password'), $marketplace->reset_password);

        if (! $login_using_password && ! $login_using_reset_password) {
            throw new \Exception(__('messages.invalid_login'));
        }

        return response()->json([
            'status' => 'success',
            'message' => __('messages.login_successfully'),
            'data' => [
                'id' => $marketplace->id,
                'national_id' => $marketplace->national_id,
                'name' => $marketplace->name,
                'mobile' => $marketplace->mobile,
                'token' => $marketplace->createToken('API TOKEN')->plainTextToken,
            ],
        ], 200);
    }

    public function resetMarketplacePassword(MarketplaceResetPasswordRequest $request)
    {
        try {
            Marketplace::where('mobile', $request->input('mobile'))->first()->update(['password' => $request->input('password')]);

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

    public function forgetMarketplacePassword(MarketplaceForgetPasswordRequest $request)
    {
        try {
            $entity = Marketplace::where('national_id', $request->input('national_id'))->first();

            if (! $entity) {
                throw new \Exception(__('messages.mobile_not_registered'));
            }

            $entity->update(['reset_password' => $reset_password = Str::random(10)]);

            $this->sms_service->sendNewPassword($entity->mobile, Hash::make($reset_password));

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

    public function logoutMarketplace()
    {
        /** @var Marketplace $marketplace */
        $marketplace = Auth::user();

        $marketplace->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.logout'),
        ], 200);
    }

    public function setLocation(MarketplaceSetLocationRequest $request)
    {
        try {
            $entity = Marketplace::where('national_id', $request->input('national_id'))->first();
            $entity->update(['latitude' => $request->input('latitude'), 'longitude' => $request->input('longitude')]);

            return response()->json(['message' => __('messages.location_located')]);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => __('messages.fail_process'),
            ], 422);
        }
    }
}
