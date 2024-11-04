<?php

namespace Tests\Unit\Enums;

use App\Enums\LanguagesEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(LanguagesEnum::class)]
class LanguagesEnumTest extends TestCase
{
    public function testHasExpectedValues()
    {
        $this->assertEquals('en', LanguagesEnum::En->value);
        $this->assertEquals('ar', LanguagesEnum::Ar->value);
    }

    public function testCanListAllCases()
    {
        $cases = LanguagesEnum::cases();

        $this->assertCount(2, $cases);
        $this->assertSame(['en', 'ar'], array_map(fn($case) => $case->value, $cases));
    }

    public function testCanInstantiateFromValue()
    {
        $this->assertSame(LanguagesEnum::En, LanguagesEnum::from('en'));
        $this->assertSame(LanguagesEnum::Ar, LanguagesEnum::from('ar'));
    }

    public function testThrowsErrorForInvalidValue()
    {
        $this->expectException(\ValueError::class);
        LanguagesEnum::from('fr');
    }
}
