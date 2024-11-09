<?php

namespace Tests\Unit\Enums;

use App\Enums\PlaceOfLocation;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(PlaceOfLocation::class)]
class PlaceOfLocationTest extends TestCase
{
    public function testHasExpectedValues()
    {
        $this->assertEquals('home', PlaceOfLocation::Home->value);
        $this->assertEquals('work', PlaceOfLocation::Work->value);
        $this->assertEquals('other', PlaceOfLocation::Other->value);
    }

    public function testCanListAllCases()
    {
        $cases = PlaceOfLocation::cases();

        $this->assertCount(3, $cases);
        $this->assertSame(['home', 'work', 'other'], array_map(fn ($case) => $case->value, $cases));
    }

    public function testCanInstantiateFromValue()
    {
        $this->assertEquals('home', PlaceOfLocation::Home->value);
        $this->assertEquals('work', PlaceOfLocation::Work->value);
        $this->assertEquals('other', PlaceOfLocation::Other->value);
    }

    public function testThrowsErrorForInvalidValue()
    {
        $this->expectException(\ValueError::class);
        PlaceOfLocation::from('company');
    }
}
