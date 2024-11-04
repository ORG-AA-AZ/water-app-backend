<?php

namespace Tests\Feature\Controllers;

use App\Enums\LanguagesEnum;
use App\Http\Controllers\Language\SetLanguage;
use App\Http\Controllers\Language\SetLanguageRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(SetLanguage::class)]
#[CoversClass(SetLanguageRequest::class)]

class LanguageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testSetLanguageSuccessfully(): void
    {
        $this->postJson('/api/set-language', ['lang' => LanguagesEnum::Ar->value])
            ->assertStatus(200)
            ->assertJson(['message' => __('messages.set_language_successfully')]);

        $this->assertEquals('ar', App::getLocale());
    }

    public function testSetLanguageFailsWithInvalidLanguage(): void
    {
        App::setLocale('ar');

        $this->postJson('/api/set-language', ['lang' => 'invalid_lang'])
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.in', ['attribute' => 'اللغة']),
            ]);

        $this->assertEquals('ar', App::getLocale());
    }

    public function testSetLanguageFailsWhenLanguageNotProvided(): void
    {
        $this->postJson('/api/set-language', [])
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.required', ['attribute' => 'language']),
            ]);

        $this->assertEquals('en', App::getLocale());
    }
}
