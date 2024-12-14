<?php

namespace Feature\Controllers;

use App\Models\Product;
use Database\Factories\MarketplaceFactory;
use Database\Factories\ProductFactory;
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
            'name' => $name = $this->faker->word(),
            'brand' => $brand = $this->faker->company(),
            'description' => $description = $this->faker->sentence(),
            'image' => $image = $this->faker->imageUrl(),
            'price' => $price = $this->faker->randomFloat(2, 1, 100),
            'quantity' => $quantity = $this->faker->numberBetween(1, 100),
            'marketplace_id' => $marketplace->id,
        ];

        $this->actingAs($marketplace)
            ->postJson('/api/marketplace/products/add-new-product', $data)
            ->assertOk()
            ->assertJson([
                'message' => __('messages.add_product_successfully'),
            ]);

        $this->assertDatabaseHas('products', [
            'name' => $name,
            'brand' => $brand,
            'description' => $description,
            'image' => $image,
            'price' => $price,
            'quantity' => $quantity,
            'marketplace_id' => $marketplace->id,
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
            // Without name parameter
            'brand' => $this->faker->company(),
            'description' => $this->faker->sentence(),
            'image' => $this->faker->imageUrl(),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'quantity' => $this->faker->numberBetween(1, 100),
            'marketplace_id' => $marketplace->id,
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

    public function testUpdateProduct(): void
    {
        $this->faker = Factory::create();
        $marketplace = MarketplaceFactory::new()->createOne();
        $product = ProductFactory::new()->forMarketplace($marketplace)->createOne();

        $data = [
            'id' => $product->id,
            'name' => $name = 'Test name for proudct',
            'brand' => $brand = 'TestBrand',
        ];

        $this->actingAs($marketplace)
            ->patchJson('/api/marketplace/products/update-product', $data)
            ->assertOk()
            ->assertJson([
                'message' => __('messages.update_product_successfully'),
            ]);

        $product = Product::where('marketplace_id', $marketplace->id)->first();
        $this->assertNotNull($product);
        $this->assertEquals($name, $product->name);
        $this->assertEquals($brand, $product->brand);
    }

    public function testDeleteProduct(): void
    {
        $this->faker = Factory::create();
        $marketplace = MarketplaceFactory::new()->createOne();
        $product = ProductFactory::new()->forMarketplace($marketplace)->createOne();
        
        $this->assertDatabaseCount('products', 1);

        $this->actingAs($marketplace)
            ->deleteJson('/api/marketplace/products/delete-product/' . $product->id)
            ->assertOk()
            ->assertJson([
                'message' => __('messages.delete_product_successfully'),
            ]);

        $this->assertDatabaseCount('products', 0);
    }

    public function testFailToDeleteProductWithInvalidId(): void
    {
        $this->faker = Factory::create();
        $marketplace = MarketplaceFactory::new()->createOne();
        ProductFactory::new()->forMarketplace($marketplace)->createOne();
        
        $this->assertDatabaseCount('products', 1);

        $this->actingAs($marketplace)
            ->deleteJson('/api/marketplace/products/delete-product/' . 5)
            ->assertStatus(404)
            ->assertJson([
                'message' => __('messages.product_not_found'),
            ]);

        $this->assertDatabaseCount('products', 1);
    }
}
