<?php

namespace Tests\Unit\Services\Sms;

use App\Services\Sms\FakeSmsService;
use Tests\TestCase;

class FakeSmsServiceTest extends TestCase
{
    public function testItCanSendVerificationCode()
    {
        $smsService = new FakeSmsService();
        $response = $smsService->sendVerificationCode('1234567890', '123456');

        $this->assertEquals(201, $response->getStatusCode());

        $this->assertEquals([], $response->getData(true));
    }

    public function testItCanSendNewPassword()
    {
        $smsService = new FakeSmsService();
        $response = $smsService->sendNewPassword('1234567890', 'newpassword');

        $this->assertEquals(201, $response->getStatusCode());

        $this->assertEquals([], $response->getData(true));
    }
}
