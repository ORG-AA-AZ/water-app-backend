<?php

namespace Feature\Controllers;

use App\Models\Product;
use Database\Factories\MarketplaceFactory;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;
    private Generator $faker;

    public function testAddProduct(): void
    {
        $this->faker = Factory::create();
        $marketplace = MarketplaceFactory::new()->createOne();

        $data = [
            'id' => $marketplace->id,
            'name' => $name = $this->faker->word(),
            'brand' => $brand = $this->faker->company(),
            'description' => $description = $this->faker->sentence(),
            'image' => $image = $this->faker->imageUrl(),
            'price' => $price = $this->faker->randomFloat(2, 1, 100),
            'quantity' => $quantity = $this->faker->numberBetween(1, 100),
        ];

        $this->actingAs($marketplace)
            ->postJson('/api/marketplace/products/add-new-product', $data)
            ->assertOk()
            ->assertJson([
                'message' => __('messages.add_product_successfully'),
            ]);

        $this->assertDatabaseHas('products', [
            'marketplace_id' => $marketplace->id,
            'name' => $name,
            'brand' => $brand,
            'description' => $description,
            'image' => $image,
            'price' => $price,
            'quantity' => $quantity,
        ]);

        $product = Product::where('marketplace_id', $marketplace->id)->first();
        $this->assertNotNull($product);
        $this->assertEquals($name, $product->name);
        $this->assertEquals($brand, $product->brand);
        $this->assertEquals($description, $product->description);
        $this->assertEquals($image, $product->image);
        $this->assertEquals($price, $product->price);
        $this->assertEquals($quantity, $product->quantity);
    }

    public function testUnauthenticated(): void
    {
        $this->faker = Factory::create();
        $data = [
            'id' => null,
            'name' => '',
        ];

        $this
            ->postJson('/api/marketplace/products/add-new-product', $data)
            ->assertStatus(401)
            ->assertJson(
                ['message' => 'Unauthenticated.']
            );
    }

    public function testErrorValidationInput(): void
    {
        $this->faker = Factory::create();
        $marketplace = MarketplaceFactory::new()->createOne();

        $data = [
            'id' => $marketplace->id,
            // Without name parameter
            'brand' => $this->faker->company(),
            'description' => $this->faker->sentence(),
            'image' => $this->faker->imageUrl(),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'quantity' => $this->faker->numberBetween(1, 100),
        ];

        $name_attribute = App::getLocale() === 'ar' ? 'الاسم' : 'name';

        $this
            ->actingAs($marketplace)
            ->postJson('/api/marketplace/products/add-new-product', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => __('messages.required', ['attribute' => $name_attribute]),
                'errors' => [
                    'name' => [__('messages.required', ['attribute' => $name_attribute])],
                ],
            ]);
    }
}
