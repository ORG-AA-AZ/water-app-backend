<?php

namespace Tests\Unit\Enums;

use App\Enums\ModelsEnum;
use App\Models\Marketplace;
use App\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ModelsEnum::class)]
class ModelsEnumTest extends TestCase
{
    public function testUserEnumReturnsUserModel()
    {
        $this->assertEquals(User::class, ModelsEnum::User->getModel());
    }

    public function testMarketplaceEnumReturnsMarketplaceModel()
    {
        $this->assertEquals(Marketplace::class, ModelsEnum::Marketplace->getModel());
    }

    public function testCasesMatchExpectedClasses()
    {
        $this->assertSame(User::class, ModelsEnum::User->value);
        $this->assertSame(Marketplace::class, ModelsEnum::Marketplace->value);
    }

    public function testAllEnumCasesExist()
    {
        $cases = ModelsEnum::cases();

        // Check that it contains exactly two cases
        $this->assertCount(2, $cases);
        $this->assertContains(ModelsEnum::User, $cases);
        $this->assertContains(ModelsEnum::Marketplace, $cases);
    }
}
